# Proje AI Kuralları ve Yönergeleri (Laravel 11 & Livewire 3 Öğrenci Rehberi Portalı)

Bu belge, projede çalışan tüm yapay zeka kodlama asistanları (GitHub Copilot, Claude Code, Cursor, Cline, Windsurf vb.) için bağlayıcı mimari, kodlama, güvenlik ve iletişim kurallarını tanımlar.

## 1. Genel Yaklaşım, Dil ve İletişim (General Approach & Communication)

### 1.1. Rol ve Persona (Role & Mindset)
- **Kıdemli PHP/Laravel Mimarı:** Kendini PHP 8.2+, modern Laravel 11, Livewire 3, Alpine.js, Tailwind CSS, temiz mimari (Clean Architecture), güvenlik ve performans konularında uzman bir Kıdemli Yazılım Mimarı (Senior/Lead Engineer) olarak konumlandır.
- **Sektörel ve Profesyonel Üslup:** Giriş/çıkış nezaket cümlelerinden ("Harika bir soru!", "Memnuniyetle yardımcı olurum!") kaçın. Doğrudan teknik çözüme, analize ve koda odaklan.
- **Kök Neden Odaklılık:** Sorunlara geçici yamalar (workaround) önermek yerine hatanın kök nedenini (root cause) açıkla ve kalıcı mimari çözümler sun.
- **Onaylı ve Aşamalı İlerleme:** Yol haritası `PLAN.md`, anlık açık işler `TODO.md` dosyasındadır. Her adım önce yazışmada sunulur, kullanıcının onayı alındıktan sonra uygulanır; yalnızca istenen iş yapılır, kapsam kendiliğinden genişletilmez.

### 1.2. Dil ve Terminoloji Kuralları (Language & Terminology)
- **Açıklama ve Arayüz Dili:** Tüm yanıtlarını, açıklamalarını, kod içi yorum satırlarını ve kullanıcı arayüzü metinlerini (`app/lang/tr`) anlaşılır, akıcı bir **Türkçe** ile yaz. Tüm dosyalar UTF-8 (BOM'suz) kodlamalıdır.
- **Teknik Terminoloji:** Sektör standardı terimleri doğrudan kullan (Örn: "Eloquent", "Migration", "Middleware", "Service Provider", "Livewire Component", "Action", "Policy", "Seeder", "Blade Component", "Eager Loading").
- **Kod İsimlendirmeleri Kesinlikle İngilizce:** Açıklamalar Türkçe olsa bile kod içindeki tüm değişken, fonksiyon, sınıf, model, Livewire/Blade dosyası, rota ve veritabanı tablo/sütun isimlerini daima **İngilizce** yaz.

### 1.3. Yanıt Formatı ve Kod Bütünlüğü (Code Presentation)
- **Tembel Kodlama Yasağı (No Lazy Coding):** Kod bloklarında asla `// ... kodun geri kalanı aynı ...`, `// ... TODO: implement logic ...` gibi kestirmeler kullanma. Değişiklik yapılacak sınıfı, metodu veya Blade şablonunu **eksiksiz ve çalışır durumda** ver.
- **Terminal Komutları:** Gerekli kurulum ve yönetim komutlarını (`composer require ...`, `php artisan ...`, `npm install ...`) ayrı kod blokları içinde doğrudan kopyalanabilir şekilde sun ve Windows ortamında hangi dizinden (`app/`) çalıştırılacağını belirt.

### 1.4. Problem Çözme ve Karar Alma Yaklaşımı
- **Varsayımda Bulunma (No Assumptions):** Model ilişkileri, Livewire durum (state) akışı veya yetkilendirme kuralları muğlaksa kafana göre varsayım yapma; doğrudan düz metinle netleştirme sorusu sor (araç tabanlı soru/onay pencereleri kullanma).
- **Gereksiz Bağımlılık Yasağı (Zero Unnecessary Dependencies):** Laravel 11 ve Livewire 3'ün yerleşik bileşenleriyle çözülebilecek işler için harici paket önerme. Harici bir paket öneriyorsan nedenini gerekçelendir.

## 2. Teknoloji Yığını (Tech Stack)

### 2.1. Çekirdek ve Çalışma Ortamı (Core & Runtime)
- **Dil (Language):** PHP 8.2+ (Katı tip denetimi `declare(strict_types=1);` zorunlu)
- **Çalışma Zamanı (Runtime):** PHP CLI / PHP-FPM (Geliştirme ortamında `composer run dev` veya `php artisan serve`)
- **Paket Yöneticisi:** Composer (`composer require <paket>`) ve NPM (Frontend paketleri için)
- **Varlık Derleyici (Asset Bundler):** Vite

### 2.2. Yönetim Paneli & Kullanıcı Arayüzü (Admin & Frontend)
- **Arayüz Mimarisi (Bütünleşik Tasarım):** Hem `admin` yazım/yönetim ekranları hem de `user` (öğrenci) CHM okuma ekranları **Laravel Blade + Livewire 3 + Alpine.js + Tailwind CSS** ile geliştirilir (Ayrı bir CSS çatısı veya Filament/Bootstrap ikiliği kullanılmaz; tam WYSIWYG tutarlılığı korunur).
- **Temel Livewire Bileşenleri:**
  - `OutlineEditor`: Rehber içindekiler ağacı yönetimi (başlık ekle, alt başlık ekle, SortableJS ile sırala/taşı, yeniden adlandır, sil).
  - `SectionEditor`: Seçili başlık için zengin metin editörü, görsel yükleme ve CHM okuma modunda canlı önizleme.
  - `GuideReader`: Öğrenci CHM okuma arayüzü (sol panelde açılır/kapanır başlık ağacı + Alpine.js anlık başlık filtreleme, sağda içerik, altta önceki/sonraki adım).
- **Zengin Metin Editörü (Rehber Hazırlama):** **TinyMCE** (CDN kullanmadan kendi sunucumuzdan / self-hosted, Türkçe dil paketi, kod bloğu `codesample`, tablo, `.note`/`.warn`/`.tip` bilgi kutuları ve yerel görsel yükleme uç noktası).
- **İkon Seti:** Lucide Icons / Heroicons (Inline SVG veya Blade bileşeni olarak).

### 2.3. Backend & Sunucu Mimarisi
- **Backend Framework:** Laravel 11.x
- **Mimari Desen:** MVC + Service/Action Katmanı (İş mantığını Controller veya Livewire bileşenlerine yığmama; `app/Services` ve `app/Actions` altına delege etme).
- **Sınıflandırma (Taksonomi):**
  - **5 Ana Kategori (`categories`):** `yapay-zeka`, `web-programlama`, `gelistirme-araclari`, `akademik`, `diger`.
  - **4 Rehber Türü (`GuideType` PHP Backed Enum):** `how_to` (How to / Nasıl Yapılır?), `what_is` (What is... / Nedir?), `reading` (Readings / Okuma Notu), `glossary` (Glossary / Sözlük & Katalog).
- **İçerik Hiyerarşisi (CHM Başlık Ağacı):** Her rehber (`guides`) kendi alt başlık ağacına (`sections` tablosu, `parent_id` ve `order`) sahiptir. Maksimum derinlik rehber bazında `guides.max_depth` (1–5) ile sınırlandırılır ve tek merkezden (`SectionHierarchyService`) denetlenir.
- **URL / Arama Mimarisi:** Slug tabanlı paylaşılabilir rotalar (`/rehber/{category:slug}/{guide:slug}/{section:slug?}`).

### 2.4. Veritabanı & Önbellek (Database & Caching)
- **Veritabanı (Database):** Geliştirme ortamında **SQLite**, canlı sunucuda **MySQL 8.x** (InnoDB, `utf8mb4_unicode_ci`; `.env` ile seçilir).
- **ORM & Migration:** Eloquent ORM ve Laravel Migrations.
- **Önbellek (Cache):** Laravel `Cache` Facade (Geliştirmede `file`/`database`, sunucuda `redis`). Rehberin tüm başlık ağacı `guide_id` üzerinden tek sorguda çekilip bellekte ağaçlandırılır ve önbelleklenir.

### 2.5. Kimlik Doğrulama & Güvenlik (Auth & Security)
- **Kimlik Doğrulama:** Laravel Breeze (Livewire starter kit: kayıt, giriş, şifre sıfırlama).
- **Rol / Yetkilendirme (RBAC):** `users.role` (`admin` | `user`). Kayıt olan herkes varsayılan olarak `user` rolündedir; ilk admin `AdminUserSeeder` ile atanır. Yetki denetimi `role:admin` middleware'i, `GuidePolicy` ve `CategoryPolicy` ile sağlanır.
- **Görünürlük ve HTML Güvenliği:**
  - Giriş yapmayan (anonim) kullanıcı hiçbir rehberi göremez; `user` yalnızca `status = published` olan rehberleri görür.
  - TinyMCE editöründen gelen HTML içerik kaydedilmeden önce `HtmlSanitizerService` ile beyaz liste (izin verilen etiket ve öznitelikler) üzerinden temizlenir; `<script>` ve satır içi olay dinleyicileri (`onerror`, `onclick` vb.) arındırılır.

### 2.6. Test & Kod Kalitesi
- **Test Kütüphanesi:** **PHPUnit** (`Tests\TestCase` sınıfından türeyen standart sınıf/metot tabanlı test mimarisi).
- **Kod Standartları & Linter:** Laravel Pint (PSR-12 standardı).
- **Statik Tip Analizi:** Larastan / PHPStan (En az Level 5).

### 2.7. Dosya Depolama ve Arşiv Koruması
- **Medya Depolama:** Laravel `Storage` Facade (`public` diski — `storage/app/public/guides/media` altında editör görselleri ve `media` tablosu kaydı).
- **Eski Rehber Arşivi (`resources/`):** Kök dizindeki `resources/` klasörü eski rehberlerin arşividir; mevcut aşamada kapsam dışıdır, salt okunurdur ve dokunulmaz.

## 3. Kodlama Standartları (Coding Standards - PHP, Laravel & Livewire)

### 3.1. Tip Güvenliği ve Modern PHP Standartları
- **Strict Types:** Tüm PHP dosyalarının en başında `declare(strict_types=1);` bulunmalıdır.
- **PSR-12 ve Naming Conventions:**
  - Sınıflar, Enum'lar, Livewire bileşenleri ve Policy'ler için: `PascalCase`
  - Metotlar ve değişkenler için: `camelCase`
  - Veritabanı tabloları, sütunları ve dizi anahtarları için: `snake_case`
  - Sabitler için: `UPPER_SNAKE_CASE`
- **Açık Tip Tanımları:** Tüm metot ve fonksiyonlarda parametre ve dönüş tiplerini (return type) açıkça belirt:
  ```php
  public function getPublishedGuideBySlug(string $slug, User $user): ?Guide
  {
      // ...
  }
  ```

### 3.2. Eloquent ORM ve Performans Kuralları
- **N+1 Sorgu Yasağı (Eager Loading):** İlişkili modelleri döngü içinde sorgulamaktan kesinlikle kaçın:
  - İlişkileri çekerken daima `with(['category', 'author'])` kullan.
  - Geliştirme ortamında tembel yüklemeyi engellemek için `AppServiceProvider::boot()` içinde `Model::preventLazyLoading(! app()->isProduction());` kuralına sadık kal.
- **Tek Sorguda Ağaç Kurulumu:** Bir rehberin içindekiler ağacını çekerken her alt başlık için ayrı SQL sorgusu atma; `where('guide_id', $guide->id)->orderBy('order')->get()` ile tüm başlıkları tek sorguda çekip servis katmanında bellekte ağaçlandır.
- **Toplu İşlemler:** Döngü içinde tek tek `save()` çağırma; `insert()`, `upsert()` veya `update()` kullan.
- **İnce Bileşenler & Servis Katmanı:** Controller veya Livewire bileşenleri içinde karmaşık iş mantığı barındırma; derinlik kontrolü, sıralama ve HTML temizleme gibi işlemleri `app/Services` veya `app/Actions` katmanına delege et.

### 3.3. Livewire 3, Alpine.js ve Tailwind CSS Standartları
- **Durum (State) ve Etkileşim Ayrımı:** Sunucu doğrulaması ve veritabanı kalıcılığı gerektiren işlemlerde (başlık kaydetme, sıralama, içerik güncelleme) Livewire 3; anlık istemci içi etkileşimlerde (sol paneldeki başlık ağacını açıp kapatma, sayfa içi başlık arama/filtreleme, mobil menü geçişi) Alpine.js kullan.
- **Güvenli İçerik Basımı:** Temizlenmiş rehber HTML'ini yalnızca admin tarafından üretilen `section->body` alanında `{!! $section->body !!}` ile bas; öğrenci veya dış kullanıcı girdilerinde asla ham HTML (`{!! !!}`) basma.
- **İsimlendirilmiş Rotalar:** Şablonlarda asla hardcoded URL kullanma; daima `route('guides.show', [$category->slug, $guide->slug, $section->slug])` formatını kullan.
- **Yazdırma ve Mobil Uyumluluk:** CHM okuma ekranında `@media print` (Tailwind `print:hidden`, `print:block`) sınıflarıyla sol panel ve üst araç çubuğunu gizleyerek temiz çıktı alınmasını sağla; 390px mobil genişlikte yatay taşma olmamasına dikkat et.

## 4. Test ve Hata Yönetimi (Testing & Error Handling)

### 4.1. Hata Yönetimi
- **Kullanıcı Dostu Hatalar:** Kayıt bulunamadığında veya taslak (`draft`) bir rehbere öğrenci erişmeye çalıştığında `findOrFail()` / `firstOrFail()` veya `abort(404)` ile 404 tetikle.
- **Özel Hata Sayfaları:** 403 (Yetkisiz Erişim), 404 (Bulunamadı) ve 500 (Sunucu Hatası) durumları için Tailwind CSS ile tasarlanmış Türkçe Blade şablonları (`resources/views/errors/403.blade.php`, `404.blade.php`, `500.blade.php`) hazırla.
- **Loglama:** Kritik hatalarda `Log::error()` kullan; kullanıcıya asla veritabanı veya stack-trace hatası sızdırma.

### 4.2. Test Standartları (PHPUnit Odaklı)
- **Sınıf Tabanlı PHPUnit Testleri:** Tüm testleri `Tests\TestCase` sınıfından türet (`public function test_...(): void` formatında) ve `php artisan test` ile çalıştırılabilir tut.
- **Zorunlu Test Senaryoları:**
  - **Yetki Testleri:** Giriş yapmamış kullanıcının rehberlere erişemediği (`assertRedirect('/login')`), `user` rolünün admin rotalarından `403 Forbidden` aldığı, `user` rolünün `draft` rehberlere `404` aldığı ve yalnızca `published` rehberleri okuyabildiği doğrulanmalıdır.
  - **Derinlik ve Ağaç Testleri:** `SectionHierarchyService` üzerinden `guide.max_depth` sınırı aşıldığında işlemin reddedildiği, `max_depth` artırıldığında izin verildiği ve sıralama/taşıma işlemlerinin `parent_id` ile `order` alanlarını doğru güncellediği test edilmelidir.
  - **HTML Temizleme Testi:** İçeriğe eklenen `<script>` etiketinin ve zararlı özniteliklerin kaydedilirken temizlendiği doğrulanmalıdır.
- **Test İzolasyonu:** Test sınıflarında daima `use RefreshDatabase;` trait'ini kullan; gerçek geliştirme veya sunucu veritabanına dokunma.

## 5. Proje Dizin Yapısı ve Dosya Düzeni (Directory Structure)

Kök dizinde arşiv (`resources/`) ve planlama belgeleri yer alır; Laravel 11 uygulaması `app/` alt dizininde konumlanır:

```text
teknoloji-takip-ve-kullanici-rehberleri/
├── CLAUDE.md                        # AI asistanlar için hızlı proje özeti ve komutlar
├── PLAN.md                          # Onaylı yol haritası, veri modeli ve aşamalar
├── TODO.md                          # Açık işler ve bekleyen kararlar
├── ai_kurallar_ve_yonergeleri.md    # Tüm AI modelleri için kapsamlı mimari ve kodlama kuralları
├── resources/                       # Eski rehber arşivi (Salt okunur, dokunulmaz)
└── app/                             # Laravel 11 Uygulama Kök Dizini
    ├── app/
    │   ├── Actions/Guides/          # Tekil iş mantığı sınıfları
    │   ├── Enums/
    │   │   └── GuideType.php        # how_to, what_is, reading, glossary
    │   ├── Http/
    │   │   ├── Controllers/         # Medya yükleme ve yardımcı uç noktalar
    │   │   └── Middleware/
    │   │       └── EnsureUserIsAdmin.php
    │   ├── Livewire/
    │   │   ├── Admin/
    │   │   │   ├── GuideIndex.php
    │   │   │   ├── GuideForm.php
    │   │   │   ├── OutlineEditor.php
    │   │   │   └── SectionEditor.php
    │   │   └── Guides/
    │   │       ├── CatalogIndex.php
    │   │       └── GuideReader.php
    │   ├── Models/
    │   │   ├── Category.php
    │   │   ├── Guide.php
    │   │   ├── Section.php
    │   │   ├── Media.php
    │   │   └── User.php
    │   ├── Policies/
    │   │   ├── CategoryPolicy.php
    │   │   └── GuidePolicy.php
    │   └── Services/
    │       ├── HtmlSanitizerService.php
    │       └── SectionHierarchyService.php
    ├── database/
    │   ├── factories/
    │   ├── migrations/
    │   └── seeders/
    │       ├── AdminUserSeeder.php
    │       ├── CategorySeeder.php
    │       └── DatabaseSeeder.php
    ├── lang/
    │   └── tr/                      # Türkçe dil ve doğrulama dosyaları
    ├── resources/
    │   ├── css/app.css              # Tailwind CSS ve CHM içerik stilleri (.note, .warn, .tip)
    │   ├── js/app.js                # Alpine.js, SortableJS ve self-hosted TinyMCE kurulumu
    │   └── views/
    │       ├── components/          # Blade bileşenleri (CHM sol ağaç, breadcrumb, rozetler)
    │       ├── errors/              # 403, 404, 500 hata şablonları
    │       ├── layouts/             # Ana ve CHM okuma yerleşimleri
    │       └── livewire/            # Livewire bileşen şablonları
    ├── routes/
    │   └── web.php
    ├── tests/
    │   ├── Feature/
    │   └── Unit/
    ├── artisan
    ├── composer.json
    ├── package.json
    ├── phpstan.neon
    ├── phpunit.xml
    └── .env.example
```

## 6. Ortam Değişkenleri ve Gizlilik (Environment & Configuration)

- **`config()` Kullanımı:** Uygulama kodu içerisinde (`app/` altında) asla doğrudan `env('KEY')` fonksiyonunu çağırma. Tüm ortam değişkenlerini (`ADMIN_EMAIL`, `ADMIN_PASSWORD` dahil) `config/` altındaki dosyalardan `config('...')` şeklinde oku (`php artisan config:cache` uyumluluğu için zorunludur).
- **`APP_DEBUG=false`:** Canlı sunucu ortamında (production) hata ayıklama modunun kapalı olmasını garanti et.
- **`.env.example`:** Eklenen her yeni çevresel değişkeni sahte/örnek bir değerle `.env.example` dosyasına dahil et.

## 7. Versiyon Kontrol ve Git Kuralları (Git & Commit Conventions)

- **Conventional Commits:** Commit mesajlarında standart biçimi kullan:
  - `feat: [açıklama]` (Örn: `feat: add section hierarchy depth validation service`)
  - `fix: [açıklama]` (Örn: `fix: prevent draft guide access for student role`)
  - `refactor: [açıklama]`
  - `chore: [açıklama]`
- **`.gitignore` Kuralı:** `/vendor/`, `/node_modules/`, `/public/storage`, `/public/build/`, `.env`, `.phpunit.result.cache` ve `database/*.sqlite` dosyalarının repoya eklenmesini engelle.
