# PLAN.md — Kullanıcı Rehberi ve Teknoloji Takip Uygulaması (Sentez Mimari)

Durum: Sentez mimari planlaması tamamlandı, kurulum onay bekliyor. Son güncelleme: 7 Kasım 2026.

## 1. Amaç ve Kapsam

Admin'in **kategori** (`yapay-zeka`, `web-programlama`, `gelistirme-araclari`, `akademik`, `diger`) ve **rehber türü** (`How to`, `What is...`, `Readings`, `Glossary`) seçerek rehber oluşturduğu, içindekiler başlık ağacını kurup her başlığın zengin HTML içeriğini WYSIWYG editörle yazdığı; öğrencilerin ise kayıt olup giriş yaparak **CHM (Compiled HTML Help)** formatında (sol panelde açılır/kapanır başlık ağacı ve anlık filtreleme, sağda içerik, altta önceki/sonraki adım) okuduğu web uygulamasıdır. Yeni rehberler sürekli eklenecek; yapı buna göre veri odaklı, önbellekli ve genişleyebilir şekilde kurgulanmıştır.

Kök `resources/` klasöründeki mevcut rehberler, içerikleri ortak bir yapıda hazırlamak için yerel kaynak olarak kullanılacaktır. Henüz veritabanına aktarılmamış bu koleksiyon mevcut haliyle GitHub'a gönderilmez. Rehberler admin dashboard üzerinden oluşturulur, güncellenir, silinir ve yayınlanır. Otomatik toplu aktarımın sonraki fazda olması, bu dosyaların içerik kaynağı olarak kullanılmasına engel değildir.

## 2. Mimari ve Teknoloji Kararları

| Katman | Karar ve Gerekçe |
| :--- | :--- |
| **Çekirdek & Framework** | **Laravel 11.x (PHP 8.2+)**, katı tip denetimi (`declare(strict_types=1);`), MVC + `app/Services` ve `app/Actions` iş mantığı ayrımı. |
| **Reaktif Arayüz & Stil** | **Livewire 3 + Alpine.js + Tailwind CSS + Blade**. Admin yazım ekranı ile öğrenci CHM okuma ekranı birebir aynı CSS çatısını paylaşır (WYSIWYG tutarlılığı). |
| **Kimlik Doğrulama & Rol** | **Laravel Breeze (Livewire starter kit)**: kayıt, giriş, şifre sıfırlama. Roller: `admin` (oluşturur, düzenler, yayınlar), `user` (okur). Öğrenci kendi kaydını açar → `user`. İlk admin seeder ile oluşturulur. |
| **Zengin Metin Editörü** | **TinyMCE (Self-hosted / kendi sunucumuzdan, CDN yok)**, Türkçe dil paketi, kod bloğu (`codesample`), tablo, özel bilgi/uyarı kutusu şablonları (`.note`, `.warn`, `.tip`) ve yerel resim yükleme uç noktası. |
| **HTML Güvenliği** | Editörden gelen HTML sunucuda izin verilen etiket/öznitelik listesiyle (`mews/purifier` veya özel DOM sanitizer servisi) temizlenir; `<script>`, `onerror` vb. zararlı içerikler arındırılır. |
| **Sınıflandırma (Taksonomi)** | **5 Ana Kategori** (`categories` tablosu) + **4 Rehber Türü** (`GuideType` PHP Backed Enum: `how_to` [How to / Nasıl Yapılır], `what_is` [What is... / Nedir], `reading` [Readings / Okuma Notu], `glossary` [Sözlük / Katalog]). |
| **CHM Ağaç Veri Modeli** | Rehber içi başlıklar (`sections`) `parent_id` ve `order` ile ağaç oluşturur; derinlik rehber bazında `guides.max_depth` (1–5) ile sınırlandırılır. Tüm ağaç `guide_id` üzerinden tek sorguda çekilip bellekte gruplanır ve önbelleğe (`Cache` Facade) alınır. |
| **Veritabanı & Önbellek** | Geliştirme: **SQLite** (sıfır ek kurulum). Sunucu: **MySQL 8.x (`utf8mb4_unicode_ci`)**. Okuma ağacı ve yayınlanmış rehberler için Laravel `Cache` (geliştirmede `file`/`database`, sunucuda `redis`) kullanılır. |
| **Kod Kalitesi & Test** | **PHPUnit** (`Tests\TestCase` sınıf tabanlı testler), **Laravel Pint** (PSR-12 kod biçimlendirme), **Larastan / PHPStan (Level 5+)** statik analiz ve geliştirme ortamında `Model::preventLazyLoading()` (N+1 sorgu yasağı). |
| **Geliştirme Ortamı** | Windows 11; mevcut PHP 8.2.12, Composer 2.10, Node 24, npm 11, Git 2.56. |

## 3. Veri Modeli

| Tablo / Yapı | Alanlar | Açıklama ve İlişkiler |
| :--- | :--- | :--- |
| `users` | `id`, `name`, `email`, `password`, `role` (`admin` \| `user`), `timestamps` | Starter kit tablosuna `role` sütunu ve `isAdmin(): bool` metodu eklenir. |
| `categories` | `id`, `name`, `slug`, `description`, `icon`, `order`, `timestamps` | Varsayılan seeder ile 5 kategori yüklenir: `yapay-zeka`, `web-programlama`, `gelistirme-araclari`, `akademik`, `diger`. |
| `guides` | `id`, `category_id`, `user_id`, `type` (`how_to` \| `what_is` \| `reading` \| `glossary`), `title`, `slug`, `description`, `max_depth` (1–5), `status` (`draft` \| `published`), `published_at`, `timestamps` | `Category` ve `User` ile `belongsTo`, `Section` ile `hasMany` ilişkilidir. `type` alanı `App\Enums\GuideType` enum'una cast edilir. |
| `sections` | `id`, `guide_id`, `parent_id` (`null` = kök), `title`, `slug`, `order`, `body` (`longtext`, temizlenmiş HTML), `timestamps` | Ağaç hiyerarşisi; derinlik `guides.max_depth` ile `SectionHierarchyService` üzerinden denetlenir. |
| `media` | `id`, `user_id`, `guide_id` (`nullable`), `path`, `original_name`, `mime`, `size`, `timestamps` | TinyMCE editöründen yüklenen görseller (`storage/app/public/guides/media`). |

## 4. Ekranlar ve Kullanıcı Akışı

| Rol | Ekran | İçerik ve Etkileşim |
| :--- | :--- | :--- |
| **Herkes** | Giriş, Kayıt, Şifre Sıfırlama | Laravel Breeze (Livewire) Türkçe kimlik doğrulama ekranları. Giriş yapmadan hiçbir rehber görünmez. |
| **user** | Rehber Kataloğu (Ana Sayfa) | Yayındaki (`published`) rehberler; üstte **Kategori sekmeleri** (`Yapay Zeka`, `Web Programlama` vb.), **Tür filtre rozetleri** (`How to`, `What is...`, `Readings`, `Sözlük`) ve başlık/açıklama arama kutusu. |
| **user** | CHM Okuma Ekranı (`GuideReader`) | **Üst Bar:** Kategori yolu (breadcrumb), rehber türü rozeti, rehberler arası hızlı geçiş, yazdırma (`@media print`) düğmesi. **Sol Panel:** Alpine.js destekli anlık başlık filtreleme + açılır/kapanır içindekiler ağacı. **Sağ Panel:** Seçili başlığın içeriği + altta Önceki/Sonraki başlık bağlantıları. |
| **admin** | Rehberlerim & Kategori Yönetimi | Tüm rehberler (taslak + yayın), kategori ve tür filtreleri, yeni rehber oluşturma ve kategori sıralama. |
| **admin** | Rehber Oluştur / Düzenle | Başlık, kategori seçimi, rehber türü (`How to` / `What is` / `Readings` / `Glossary`), açıklama, maksimum başlık derinliği (`max_depth`: 1–5), yayın durumu. |
| **admin** | İçindekiler Ağacı (`OutlineEditor`) | Livewire + SortableJS bileşeni: kök başlık ekle, alt başlık ekle (`max_depth` sınırına kadar), sürükle-bırak ile sırala/taşı, yeniden adlandır, alt başlıklarıyla birlikte onaylı sil. |
| **admin** | İçerik Editörü (`SectionEditor`) | Solda başlık ağacı, sağda seçili başlık için TinyMCE (kod bloğu, tablo, `.note`/`.warn`/`.tip` kutuları, görsel yükleme), kaydet ve CHM okuma modunda canlı önizle. |

## 5. Uygulama Aşamaları

Her aşamanın sonunda otomatik testler ve doğrulama adımları çalıştırılır, sonuç kullanıcıya gösterilir ve onay alındıktan sonra bir sonraki aşamaya geçilir.

### Aşama 0 — Ortam Doğrulama
- [ ] Güncel Laravel 11 sürümünün PHP 8.2.12 ile uyumluluğunu ve gerekli PHP uzantılarını (`pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `gd`) doğrula.
- [ ] Doğrulama: `php -v`, `composer -V`, `node -v` ve `php -m` çıktıları eksiksizdir.

### Aşama 1 — İskele ve Katı Standartların Kurulumu
- [ ] `app/` dizini içinde Laravel 11 + Livewire 3 starter kit (Breeze) kurulumunu gerçekleştir.
- [ ] `.env` yapılandırması: `DB_CONNECTION=sqlite`, `APP_LOCALE=tr`, `APP_TIMEZONE=Europe/Istanbul`.
- [ ] `AppServiceProvider::boot()` içine `Model::preventLazyLoading(! app()->isProduction());` kuralını ekle; Larastan (`larastan/larastan`) paketini geliştirme bağımlılığı olarak kur.
- [ ] Doğrulama: `php artisan migrate`, `php artisan test` ve tarayıcıda kayıt/giriş/çıkış akışı sorunsuz çalışır.

### Aşama 2 — Roller, Yetkilendirme ve Güvenlik
- [ ] `users.role` (`admin` | `user`) migration'ı, `User::isAdmin(): bool` metodu ve `declare(strict_types=1);` bildirimlerini ekle.
- [ ] `AdminUserSeeder` oluştur: ilk admin bilgilerini `config('app.admin_email')` üzerinden `.env`'den (`ADMIN_EMAIL`, `ADMIN_PASSWORD`) güvenli şekilde oku.
- [ ] `EnsureUserIsAdmin` (`role:admin`) middleware'i ile `GuidePolicy` ve `CategoryPolicy` sınıflarını tanımla.
- [ ] Doğrulama: PHPUnit testi — anonim kullanıcı `/login`'e yönlendirilir (`302`), `user` rolü admin rotalarında `403 Forbidden` alır, `admin` rolü `200 OK` ile erişir.

### Aşama 3 — Veri Modeli, Taksonomi ve Derinlik Servisi
- [ ] `GuideType` (`how_to`, `what_is`, `reading`, `glossary`) PHP Backed Enum sınıfını oluştur (Türkçe etiket ve renk/ikon yardımcı metotlarıyla).
- [ ] `categories`, `guides`, `sections`, `media` migration'larını, Eloquent modellerini (`with()` eager loading kurallarıyla) ve factory'lerini yaz.
- [ ] `CategorySeeder` ile `resources/` arşivindeki 5 ana kategoriyi (`Yapay Zeka`, `Web Programlama`, `Geliştirme Araçları`, `Akademik`, `Diğer`) oluştur.
- [ ] `SectionHierarchyService` sınıfını yaz: başlık ekleme ve taşıma sırasında derinlik `guide->max_depth` değerini aşarsa `DomainException` / doğrulama hatası fırlat; değişiklik sonrası ilgili rehberin ağaç önbelleğini (`Cache::forget`) temizle.
- [ ] Doğrulama: PHPUnit birim ve özellik testleri — derinlik sınırı aşılınca işlem reddedilir, `max_depth` artırılınca izin verilir, N+1 sorgu oluşmaz.

### Aşama 4 — Admin: Kategori ve Rehber Yönetimi
- [ ] Admin rehber listesi (kategori, tür ve durum filtreli), rehber oluşturma ve düzenleme formları (ad, kategori, `GuideType` seçimi, açıklama, `max_depth`, durum) ve onaylı silme işlemini geliştir.
- [ ] Doğrulama: Türkçe doğrulama mesajları ve PHPUnit CRUD testleri temiz geçer.

### Aşama 5 — Admin: İçindekiler Ağacı (`OutlineEditor`)
- [ ] Livewire `OutlineEditor` bileşenini geliştir: başlık ekle, alt başlık ekle, yeniden adlandır, onaylı sil (alt başlıklarıyla), SortableJS ile sırala ve üst başlık değiştir.
- [ ] Derinlik sınırına ulaşıldığında arayüzde "Alt başlık ekle" düğmesini gizle, sunucu tarafında `SectionHierarchyService` ile zorunlu denetim yap.
- [ ] Doğrulama: Sayfa yenilenmeden ağaç güncellenir; sıralama ve taşıma testleri `order` ve `parent_id` değerlerini hatasız doğrular.

### Aşama 6 — Admin: Zengin İçerik Editörü (`SectionEditor`) ve HTML Temizleme
- [ ] TinyMCE npm paketini, Türkçe dil dosyasını ve CHM içerik stilini (`note`, `warn`, `tip` bilgi kutuları, kod blokları ve tablolar) kur; yerel görsel yükleme uç noktasını (`media`) bağla.
- [ ] `HtmlSanitizerService` ile kayıt öncesi izin verilen HTML etiket/öznitelik filtrelemesini uygula (`<script>`, inline event handler vb. temizlenir).
- [ ] Sol panelde başlık ağacı, sağda TinyMCE editörü ve "CHM Önizleme" geçişini tamamla.
- [ ] Doğrulama: Tablolu, resimli ve kod bloklu içerik kaydedilir; PHPUnit testinde zararlı `<script>` etiketlerinin temizlendiği doğrulanır.

### Aşama 7 — Öğrenci: Rehber Kataloğu ve CHM Okuma Ekranı (`GuideReader`)
- [ ] Öğrenci ana sayfasını geliştir: kategori sekmeleri, `How to` / `What is...` / `Readings` / `Sözlük` tür rozetleri ve arama çubuğu (yalnızca `status = published` rehberler).
- [ ] `GuideReader` bileşenini geliştir:
  - Sol panelde açılır/kapanır CHM başlık ağacı + Alpine.js ile sayfa içi anlık başlık filtreleme (`Rehberde ara...`).
  - Sağ panelde seçili başlığın içeriği, üstte breadcrumb, altta Önceki / Sonraki başlık gezinmesi ve temiz yazdırma (`@media print`) görünümü.
  - Paylaşılabilir SEO dostu URL yapısı: `/rehber/{category:slug}/{guide:slug}/{section:slug?}`.
- [ ] Doğrulama: Taslak rehberler `user` rolüne `404` döner; yayınlanan rehber ve alt başlıkları N+1 sorgu olmadan önbellekten hızlıca sunulur.

### Aşama 8 — Kalite Güvencesi, Türkçeleştirme ve Teslim
- [ ] Tüm arayüz metinlerinin (`app/lang/tr`), özel hata sayfalarının (`403.blade.php`, `404.blade.php`, `500.blade.php`) ve mobil/masaüstü (390px – 1920px) uyumluluğunun kontrolünü yap.
- [ ] `./vendor/bin/pint`, `./vendor/bin/phpstan analyse` ve `php artisan test` komutlarını çalıştırarak sıfır hata/uyarı durumunu doğrula.
- [ ] Windows ortamı için Türkçe `README.md` kurulum ve kullanım kılavuzunu yaz.

## 6. Kapsam Dışı (İkinci Fazda Değerlendirilecekler)

- `resources/` arşivindeki mevcut 195 dosyanın (HTML, Markdown, DOCX, PDF) otomatik ayrıştırıcı komutlarla (`php artisan guides:import-legacy`) veritabanına toplu aktarılması.
- Rehberi tek dosya çevrimdışı HTML veya CHM paketi olarak dışa aktarma.
- Sürüm geçmişi (revision history), öğrenci yer imleri ve "okudum" ilerleme takibi.
