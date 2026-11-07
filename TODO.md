# TODO.md — Açık İşler, Bekleyen Kararlar ve İlerleme Günlüğü

Son güncelleme: 7 Kasım 2026, 02:35 (Türkiye). Yol haritası ve teknik mimari `PLAN.md` dosyasındadır; bu dosya yalnızca anlık açık işleri, bekleyen kullanıcı kararlarını ve tamamlanan adımları tutar.

## 1. Bekleyen (Kullanıcı Kararı ve Onayı)

- [ ] **Sentez `PLAN.md` ve `CLAUDE.md` Onayı:** Güncellenen sentez mimari planı (`PLAN.md`) incelenip onaylanacak. Onay gelmeden hiçbir kurulum aşamasına başlanmaz.
- [ ] **Eski Yönerge Dosyalarının Durumu:** Kök dizindeki `django_ai_kurallar_ve_yonergeleri.md` ve `laravel_ai_kurallar_ve_yonergeleri.md` dosyalarının içindeki gerekli mimari ve kalite kuralları `CLAUDE.md` ile `PLAN.md` içine aktarıldı. Ayrıca `.github/copilot-instructions.md` hâlâ bu iki eski dosyaya referans veriyor. Karar bekleyenler:
  - `.github/copilot-instructions.md` dosyasının `CLAUDE.md` ve `PLAN.md`'yi işaret edecek şekilde güncellenmesi.
  - Eski iki yönerge dosyasının silinmesi veya `resources/` altına arşivlenmesi.
- [ ] **VS Code GitHub Hatası:** VS Code kapatılıp açıldığında hata yinelenirse mesaj metni paylaşılacak. Günlüklerdeki adaylar:
  1. Copilot eklenti pazarının `git pull --ff-only` çağrısının "Filename too long" ile başarısız olması (çözüm adayı: `git config --global core.longpaths true`, **henüz uygulanmadı**).
  2. VS Code'da GitHub hesabına oturum açılmamış olması.
- [ ] **Git / GitHub Kurulumu:** Depo: https://www.google.com/search?q=https://github.com/ufuktanyeri/tech-user-guide (kullanıcı oluşturdu; herkese açık; içinde `README.md`, MIT `LICENSE`, Visual Studio şablonu `.gitignore` var). Yerelde henüz `git init` yapılmadı. Karar bekleyenler:
  - Git kökü (`app/` dizini mi, yoksa tüm proje klasörü mü?).
  - `.gitignore` şablonunun Laravel + SQLite + Vite standartlarına (`CLAUDE.md` ve `PLAN.md` kurallarına) göre güncellenmesi ve `resources/` arşivinin repoya dahil edilip edilmeyeceği.
  - İlk commit (`feat: initial commit` / `chore: project setup`) zamanı.

## 2. Yapılacak (Onay Sonrası, `PLAN.md` Sırasıyla)

- [ ] **Aşama 0 — Ortam Doğrulama:** Mevcut PHP 8.2.12 sürümünün güncel Laravel 11 için yeterliliğini ve gerekli PHP uzantılarını (`pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `gd`) kontrol et; gerekirse Herd/Laragon ile güncelle.
- [ ] **Aşama 1 — İskele ve Katı Standartların Kurulumu:**
  - `app/` dizini içinde Laravel 11 + Livewire 3 (Breeze) kurulumunu gerçekleştir. *(Not: `app/` içinde dosya varsa geçici klasörde oluşturulup `app/` içine taşınacak).*
  - `.env` içinde SQLite, `APP_LOCALE=tr`, `APP_TIMEZONE=Europe/Istanbul` ayarlarını yap.
  - `AppServiceProvider` içine `Model::preventLazyLoading(! app()->isProduction());` kuralını ekle ve `larastan/larastan` paketini kur.
- [ ] **Aşama 2 — Roller, Yetkilendirme ve Güvenlik:**
  - `users.role` (`admin` | `user`) migration'ı, `User::isAdmin(): bool` metodu ve `declare(strict_types=1);` bildirimlerini ekle.
  - `AdminUserSeeder` (`config('app.admin_email')` üzerinden), `EnsureUserIsAdmin` (`role:admin`) middleware'i, `GuidePolicy` ve `CategoryPolicy` sınıflarını oluştur; PHPUnit yetki testlerini doğrula.
- [ ] **Aşama 3 — Veri Modeli, Taksonomi ve Derinlik Servisi:**
  - `GuideType` (`how_to`, `what_is`, `reading`, `glossary`) PHP Backed Enum sınıfını oluştur.
  - `categories`, `guides`, `sections`, `media` migration'larını, modellerini ve factory'lerini yaz.
  - `CategorySeeder` ile 5 ana kategoriyi (`Yapay Zeka`, `Web Programlama`, `Geliştirme Araçları`, `Akademik`, `Diğer`) veritabanına ekle.
  - `SectionHierarchyService` ile `guide.max_depth` (1–5) derinlik denetimini ve ağaç önbellek (`Cache`) temizliğini kurgula; birim testlerini çalıştır.
- [ ] **Aşama 4 — Admin: Kategori ve Rehber Yönetimi:** Kategori, `GuideType` ve durum filtreli rehber listesi ile rehber oluşturma/düzenleme/silme ekranlarını tamamla.
- [ ] **Aşama 5 — Admin: İçindekiler Ağacı (`OutlineEditor`):** Livewire + SortableJS ile başlık/alt başlık ekleme, yeniden adlandırma, silme ve sürükle-bırak sıralama bileşenini geliştir.
- [ ] **Aşama 6 — Admin: Zengin İçerik Editörü (`SectionEditor`) ve HTML Temizleme:** Self-hosted Türkçe TinyMCE (kod bloğu, tablo, `.note`/`.warn`/`.tip` bilgi kutuları, görsel yükleme) ve `HtmlSanitizerService` (XSS arındırma) entegrasyonunu tamamla.
- [ ] **Aşama 7 — Öğrenci: Rehber Kataloğu ve CHM Okuma Ekranı (`GuideReader`):**
  - Ana sayfada kategori sekmeleri, `How to` / `What is...` / `Readings` / `Sözlük` tür rozetleri ve arama kutusu.
  - `GuideReader` ekranında Alpine.js ile sol panel anlık başlık filtreleme, açılır/kapanır ağaç, sağ içerik alanı, önceki/sonraki başlık gezinmesi ve yazdırma (`@media print`) desteği.
- [ ] **Aşama 8 — Kalite Güvencesi, Türkçeleştirme ve Teslim:**
  - `app/lang/tr` dil dosyaları, `403`/`404`/`500` özel hata sayfaları ve mobil uyumluluk (390px) kontrolü.
  - `./vendor/bin/pint`, `./vendor/bin/phpstan analyse` ve `php artisan test` kontrolleri.
  - Windows 11 kurulum ve çalıştırma adımlarını içeren Türkçe `README.md` yazımı.

## 3. Tamamlanan

- [x] **7 Kas 2026** — Üç ayrı planlama ve yönerge yapısı (`PLAN.md` + `CLAUDE.md` + `TODO.md`, `django_ai_kurallar_ve_yonergeleri.md`, `laravel_ai_kurallar_ve_yonergeleri.md`) teknoloji ve mimari açıdan karşılaştırıldı.
- [x] **7 Kas 2026** — Sentez mimari (Laravel 11 + Livewire 3 + Alpine.js + Tailwind CSS + TinyMCE + 5 Kategori & 4 Rehber Türü [`How to`, `What is...`, `Readings`, `Glossary`] + Katı PHP/Larastan standartları) oluşturularak `PLAN.md`, `CLAUDE.md` ve `TODO.md` güncellendi.
- [x] **6 Kas 2026** — Depodaki 196 dosyanın içeriği tek cümlelik çizelgeyle envanterlendi.
- [x] **6 Kas 2026** — Kök içerik kullanıcı tarafından `resources/` altına taşındı, `app/` açıldı; kontrol edildi (197 dosya, `desktop.ini` dâhil).
- [x] **6 Kas 2026** — `resources/` içeriği onaylı 5 kategoriye ayrıldı: `yapay-zeka`, `web-programlama`, `gelistirme-araclari`, `akademik`, `diger`; `desktop.ini` silindi; sonuç 195 dosya / 27 klasör.
- [x] **6 Kas 2026** — Uygulama gereksinimleri netleşti (admin/user, öğrenci kaydı, başlık formu, HTML editör, sol panel, derinlik girdisi) ve ilk teknoloji önerisi `PLAN.md`'ye yazıldı.
- [x] **6 Kas 2026** — İlk `CLAUDE.md` ve `PLAN.md` oluşturuldu.
- [x] **6 Kas 2026** — VS Code git/GitHub ayarları ve günlükleri incelendi; ayarlarda sorun yok; değişiklik yapılmadı.

## 4. Kapsam Dışı (İkinci Fazda Değerlendirilecekler)

- `resources/` arşivindeki mevcut 195 dosyanın (`html`, `md`, `docx`, `pdf`) otomatik ayrıştırıcı komutlarla (`php artisan guides:import-legacy`) veritabanına aktarılması (`PLAN.md` §6).
- Rehberi tek dosya çevrimdışı HTML veya CHM paketi olarak dışa aktarma.
- Sürüm geçmişi, yorumlar, öğrenci yer imleri ve "okudum" ilerleme takibi.

## 5. Çalışma Kuralları (Kullanıcının İsteği)

- Her adım önce yazışmada sunulur, onaydan sonra uygulanır; yalnızca istenen iş yapılır, kapsam genişletilmez.
- Sorular ve planlar düz metinle sorulur/sunulur; araç tabanlı soru ve onay pencereleri kullanılmaz.
- Kullanıcının zaten verdiği bilgi yeniden araştırılmaz.
