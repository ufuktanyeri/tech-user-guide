<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * resources/ arşivindeki 5 ana rehber kategorisini veritabanına ekleyen Seeder sınıfı.
 */
final class CategorySeeder extends Seeder
{
    /**
     * Varsayılan kategorileri oluşturur veya günceller.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Yapay Zeka',
                'slug' => 'yapay-zeka',
                'description' => 'LLM modelleri, Claude Code, Cline, akademik yapay zeka iş akışları ve tasarım sistemleri rehberleri.',
                'icon' => 'cpu',
                'order' => 1,
            ],
            [
                'name' => 'Web Programlama',
                'slug' => 'web-programlama',
                'description' => 'MVC mimarisi, CSS seçicileri, Apache/IIS web sunucuları ve online kod editörleri uygulama rehberleri.',
                'icon' => 'globe',
                'order' => 2,
            ],
            [
                'name' => 'Geliştirme Araçları',
                'slug' => 'gelistirme-araclari',
                'description' => 'VS Code ile Git işlemleri, Visual Studio 2026 Community, GitHub gelişmiş arama ve DevTools rehberleri.',
                'icon' => 'wrench',
                'order' => 3,
            ],
            [
                'name' => 'Akademik',
                'slug' => 'akademik',
                'description' => 'Üniversite kütüphane ve veritabanı erişim kılavuzları, kampüs dışı proxy/VPN ayarları ve terimler sözlüğü.',
                'icon' => 'graduation-cap',
                'order' => 4,
            ],
            [
                'name' => 'Diğer',
                'slug' => 'diger',
                'description' => 'Donanım incelemeleri, genel teknoloji okuma notları ve yardımcı teknik dokümanlar.',
                'icon' => 'folder',
                'order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
}