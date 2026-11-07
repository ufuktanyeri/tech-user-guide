<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Guide;
use App\Models\Section;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Rehber içi CHM başlık ağacının (sections) derinlik kurallarını (max_depth),
 * sıralamasını ve önbelleklemesini tek merkezden yöneten servis sınıfı.
 */
final class SectionHierarchyService
{
    /**
     * İlgili rehberin ağaç önbellek anahtarını üretir.
     */
    public function getCacheKey(int $guideId): string
    {
        return "guide_tree:{$guideId}";
    }

    /**
     * Rehberin tüm başlıklarını tek sorguda çekip önbellekli ağaç yapısında döndürür (N+1 sorgu oluşmaz).
     *
     * @return Collection<int, Section>
     */
    public function getCachedTree(Guide $guide): Collection
    {
        return Cache::remember(
            $this->getCacheKey((int) $guide->id),
            now()->addHours(12),
            function () use ($guide): Collection {
                /** @var Collection<int, Section> $allSections */
                $allSections = Section::query()
                    ->where('guide_id', $guide->id)
                    ->orderBy('order')
                    ->orderBy('id')
                    ->get(['id', 'guide_id', 'parent_id', 'title', 'slug', 'order']);

                return $this->buildTreeFromFlatCollection($allSections);
            }
        );
    }

    /**
     * Düz (flat) başlık koleksiyonunu bellekte üst-alt (children) ağacına dönüştürür.
     *
     * @param  Collection<int, Section>  $sections
     * @return Collection<int, Section>
     */
    public function buildTreeFromFlatCollection(Collection $sections): Collection
    {
        $groupedByParent = $sections->groupBy(fn (Section $section): int => (int) ($section->parent_id ?? 0));

        $attachChildren = function (int $parentId) use (&$attachChildren, $groupedByParent): Collection {
            /** @var Collection<int, Section> $branch */
            $branch = $groupedByParent->get($parentId, collect());

            foreach ($branch as $section) {
                $section->setRelation('children', $attachChildren((int) $section->id));
            }

            return $branch->values();
        };

        return $attachChildren(0);
    }

    /**
     * Yeni bir başlık eklenmeden veya mevcut bir başlık başka bir üst başlığa taşınmadan önce
     * rehberin izin verdiği maksimum derinlik (max_depth) sınırını doğrular.
     *
     * @throws DomainException Derinlik sınırı aşıldığında veya döngüsel (circular) taşıma denendiğinde fırlatılır.
     */
    public function validatePlacement(Guide $guide, ?Section $newParent = null, ?Section $movingSection = null): void
    {
        if ($newParent !== null && (int) $newParent->guide_id !== (int) $guide->id) {
            throw new DomainException('Üst başlık aynı rehbere ait olmalıdır.');
        }

        if ($movingSection !== null && $newParent !== null) {
            if ((int) $movingSection->id === (int) $newParent->id) {
                throw new DomainException('Bir başlık kendisinin alt başlığı olamaz.');
            }

            if ($this->isDescendantOf($newParent, $movingSection)) {
                throw new DomainException('Bir başlık kendi alt başlıklarından birinin altına taşınamaz.');
            }
        }

        $targetDepth = $newParent === null ? 1 : $this->calculateNodeDepth($newParent) + 1;
        $subtreeHeight = $movingSection === null ? 0 : $this->calculateSubtreeAdditionalDepth($movingSection);
        $totalRequiredDepth = $targetDepth + $subtreeHeight;

        if ($totalRequiredDepth > (int) $guide->max_depth) {
            throw new DomainException(
                sprintf(
                    'Bu işlem rehberin izin verilen maksimum başlık derinliğini (%d seviye) aşıyor (gereken: %d seviye).',
                    (int) $guide->max_depth,
                    $totalRequiredDepth
                )
            );
        }
    }

    /**
     * Bir başlığın kök seviyesinden itibaren kaçıncı derinlikte olduğunu hesaplar (Kök = 1).
     */
    public function calculateNodeDepth(Section $section): int
    {
        $depth = 1;
        $currentParentId = $section->parent_id;

        $parentMap = Section::query()
            ->where('guide_id', $section->guide_id)
            ->pluck('parent_id', 'id');

        while ($currentParentId !== null) {
            $depth++;
            $currentParentId = $parentMap->get($currentParentId);
        }

        return $depth;
    }

    /**
     * Taşınan başlığın altındaki en derin alt dalın kaç ek seviye getirdiğini hesaplar.
     */
    public function calculateSubtreeAdditionalDepth(Section $section): int
    {
        $childrenByParent = Section::query()
            ->where('guide_id', $section->guide_id)
            ->get(['id', 'parent_id'])
            ->groupBy('parent_id');

        $walkDepth = function (int $nodeId) use (&$walkDepth, $childrenByParent): int {
            $children = $childrenByParent->get($nodeId, collect());
            if ($children->isEmpty()) {
                return 0;
            }

            $maxChildDepth = 0;
            foreach ($children as $child) {
                $maxChildDepth = max($maxChildDepth, 1 + $walkDepth((int) $child->id));
            }

            return $maxChildDepth;
        };

        return $walkDepth((int) $section->id);
    }

    /**
     * Başlığı yeni üst başlığa ve sıraya taşır, kardeş başlıkların sıralamasını günceller ve önbelleği temizler.
     */
    public function moveSection(Section $section, ?Section $newParent, int $newOrder): void
    {
        /** @var Guide $guide */
        $guide = $section->guide()->firstOrFail();

        $this->validatePlacement($guide, $newParent, $section);

        DB::transaction(function () use ($section, $newParent, $newOrder): void {
            $section->parent_id = $newParent?->id;
            $section->order = max(0, $newOrder);
            $section->save();
        });

        $this->clearCache((int) $guide->id);
    }

    /**
     * Rehberin önbelleğe alınmış başlık ağacını temizler.
     */
    public function clearCache(int $guideId): void
    {
        Cache::forget($this->getCacheKey($guideId));
    }

    /**
     * $candidate düğümünün $targetSection düğümünün alt soyundan olup olmadığını denetler.
     */
    private function isDescendantOf(Section $candidate, Section $targetSection): bool
    {
        $parentMap = Section::query()
            ->where('guide_id', $targetSection->guide_id)
            ->pluck('parent_id', 'id');

        $currentParentId = $candidate->parent_id;
        while ($currentParentId !== null) {
            if ((int) $currentParentId === (int) $targetSection->id) {
                return true;
            }
            $currentParentId = $parentMap->get($currentParentId);
        }

        return false;
    }
}