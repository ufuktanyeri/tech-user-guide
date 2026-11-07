# AGENTS.md

This file provides guidance to Codex (claude.ai/code) and AI coding assistants when working with code in this repository.

## 1. Proje Özeti ve Çalışma Prensibi

Kullanıcı rehberi hazırlama ve okuma uygulaması (Türkçe arayüz, CHM tarzı iki panelli yerleşim).
- **`admin` rolü:** Kategori (`Yapay Zeka`, `Web Programlama`, `Geliştirme Araçları`, `Akademik`, `Diğer`) ve rehber türü (`How to`, `What is...`, `Readings`, `Glossary`) seçerek rehber oluşturur, içindekiler ağacını (`sections`) kurar ve her başlığın HTML içeriğini TinyMCE editörüyle yazar.
- **`user` rolü (Öğrenciler):** Kendi hesaplarını açıp giriş yaparak yayınlanmış rehberleri kategori/tür bazında filtreler; sol panelde açılır/kapanır başlık ağacı ve anlık arama, sağ panelde içerik ve önceki/sonraki adım biçiminde okur.

**Tek Doğruluk Kaynağı ve Onay Akışı:**
- Yol haritası, mimari kararlar ve aşama durumu `PLAN.md` dosyasındadır; anlık açık işler `TODO.md` içinde takip edilir.
- Kullanıcının onayı olmadan hiçbir yeni aşamaya geçilmez. Bir aşama tamamlandığında doğrulama testleri çalıştırılır, sonuç kullanıcıya sunulur ve `PLAN.md` içindeki ilgili kutu işaretlenir.

## 2. Teknoloji Yığını

- **Backend:** Laravel 11.x (PHP 8.2+), Eloquent ORM, MVC + `app/Services` ve `app/Actions` katmanı
- **Frontend & Etkileşim:** Livewire 3 + Alpine.js, Tailwind CSS, Blade (Admin ve öğrenci ekranları aynı tasarım sistemini kullanır)
- **Kimlik Doğrulama:** Laravel Breeze (Livewire starter kit — kayıt, giriş, şifre sıfırlama)
- **Zengin Metin Editörü:** TinyMCE (kendi sunucumuzdan / self-hosted, Türkçe dil paketi, kod bloğu, tablo, `.note`/`.warn`/`.tip` bilgi kutuları ve yerel görsel yükleme uç noktası)
- **Veritabanı & Önbellek:** Geliştirmede SQLite, sunucuda MySQL 8 (`utf8mb4_unicode_ci`). Ağaç sorguları için Laravel `Cache` Facade
- **Test & Kalite Güvencesi:** PHPUnit (`Tests\TestCase` sınıf tabanlı mimari), Laravel Pint (PSR-12), Larastan / PHPStan (Level 5+)

## 3. Komutlar (Hepsi `app/` Dizininden Çalıştırılır)

```powershell
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # 5 ana kategoriyi ve ilk admin kullanıcıyı oluşturur
composer run dev                    # serve + vite + queue + log (veya ayrı terminallerde: php artisan serve / npm run dev)
php artisan test                    # tüm PHPUnit testlerini çalıştırır
php artisan test --filter=GuideTest # tek test sınıfını çalıştırır
./vendor/bin/pint                   # PSR-12 kod biçimlendirme kontrolü ve düzeltmesi
./vendor/bin/phpstan analyse        # statik tip ve kod analizi
```

## 4. Mimari Kurallar

- **Roller ve Yetkilendirme:** `users.role` = `admin` | `user`. Kayıt olan herkes varsayılan olarak `user` rolündedir; ilk admin `AdminUserSeeder` ile atanır. Yetki denetimi `GuidePolicy`, `CategoryPolicy` ve `role:admin` middleware'i ile yapılır. Giriş yapmayan (anonim) kullanıcı hiçbir rehberi göremez; `user` yalnızca `status = published` olanları görür.
- **Veri Modeli ve Taksonomi:**
  - `categories` (`name`, `slug`, `order`) → `guides` (`category_id`, `type` [`GuideType` Enum: `how_to`, `what_is`, `reading`, `glossary`], `title`, `slug`, `max_depth`, `status`) → `sections` (`guide_id`, `parent_id`, `order`, `body` HTML) ve `media` (`storage/app/public/guides/media`).
- **Derinlik Kuralı (Tek Noktadan Denetim):** `Section` oluşturulurken veya taşınırken `guide.max_depth` (1–5) kontrolü yalnızca `SectionHierarchyService` içinde uygulanır; arayüzdeki gizleme yalnızca kullanıcı deneyimi kolaylığıdır.
- **Livewire Bileşenleri:**
  - `OutlineEditor`: Başlık ağacı yönetimi (ekle, alt başlık ekle, SortableJS ile sırala/taşı, yeniden adlandır, sil).
  - `SectionEditor`: Seçili başlık için TinyMCE editörü, görsel yükleme ve CHM önizleme.
  - `GuideReader`: Öğrenci CHM okuma ekranı (sol panelde Alpine.js anlık başlık filtreleme + ağaç menü, sağda içerik, altta önceki/sonraki başlık).
- **Önbellekleme ve N+1 Yasağı:** Geliştirme ortamında `Model::preventLazyLoading(! app()->isProduction())` aktiftir. İlişkili modeller daima `with(['category', 'author'])` ile çekilir; rehberin içindekiler ağacı `guide_id` üzerinden tek sorguda alınıp bellekte kurulur ve önbelleklenir.

## 5. Kodlama, Dil ve Güvenlik Standartları

- **Dil Ayrımı:** Tüm kullanıcı arayüzü metinleri (`app/lang/tr`), doğrulama mesajları, açıklamalar ve kod içi yorum satırları akıcı **Türkçe**; tüm sınıf, metot, değişken, rota ve veritabanı tablo/sütun adları kesinlikle **İngilizce** yazılır. Tüm dosyalar UTF-8 (BOM'suz) kodlamalıdır.
- **Katı Tip Güvenliği (`Strict Types`):** Tüm PHP dosyalarının en başında `declare(strict_types=1);` bulunur. Tüm metotlarda parametre ve dönüş tipleri (`?Guide`, `void`, `Collection` vb.) açıkça tanımlanır.
- **Tembel Kodlama Yasağı (No Lazy Coding):** Kod bloklarında `// ... kodun geri kalanı aynı ...` veya `// TODO` gibi eksik bırakmalar yapılmaz; düzenlenen metot, sınıf veya Blade şablonu eksiksiz ve çalışır durumda yazılır.
- **Ortam Değişkenleri (`config()` Kuralı):** Uygulama kodunda (`app/` altında) asla doğrudan `env()` fonksiyonu çağrılmaz; tüm çevresel değişkenler `config/*.php` dosyaları üzerinden `config('...')` ile okunur.
- **HTML Güvenliği (XSS Koruması):** TinyMCE editöründen gelen HTML içerik veritabanına kaydedilmeden önce `HtmlSanitizerService` üzerinden temizlenir (yalnızca izinli etiket ve öznitelikler korunur). Öğrenci veya dış kullanıcı girdilerinde asla `{!! !!}` (unescaped output) kullanılmaz.
- **Test Standardı:** Tüm testler `Tests\TestCase` sınıfından türeyen standart PHPUnit sınıfları olarak (`use RefreshDatabase;` ile izole şekilde) yazılır.
- **Arşiv Koruması ve Ortam:** `resources/` klasörü eski rehberlerin arşividir; mevcut aşamada salt okunurdur ve dokunulmaz. Windows 11 ortamında kullanıcıya terminal komutu verilirken hangi dizinde (`app/`) çalıştırılacağı açıkça belirtilir.
- **Git Kuralları:** Commit mesajlarında Conventional Commits (`feat:`, `fix:`, `refactor:`, `chore:`) standardı kullanılır.
