<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Rehber türlerini (How to, What is..., Readings, Glossary) tanımlayan Backed Enum.
 */
enum GuideType: string
{
    case HowTo = 'how_to';
    case WhatIs = 'what_is';
    case Reading = 'reading';
    case Glossary = 'glossary';

    /**
     * Kullanıcı arayüzünde gösterilecek Türkçe başlığı döndürür.
     */
    public function label(): string
    {
        return match ($this) {
            self::HowTo => 'Nasıl Yapılır? (How to)',
            self::WhatIs => 'Nedir? (What is...)',
            self::Reading => 'Okuma ve Çalışma Notu (Readings)',
            self::Glossary => 'Sözlük ve Katalog (Glossary)',
        };
    }

    /**
     * Liste ve kartlarda gösterilecek kısa Türkçe etiketi döndürür.
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::HowTo => 'How to',
            self::WhatIs => 'What is...',
            self::Reading => 'Readings',
            self::Glossary => 'Sözlük',
        };
    }

    /**
     * Tailwind CSS rozet (badge) renk sınıflarını döndürür.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::HowTo => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
            self::WhatIs => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800',
            self::Reading => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
            self::Glossary => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
        };
    }

    /**
     * Form seçim kutuları (select) için [value => label] dizisi döndürür.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}