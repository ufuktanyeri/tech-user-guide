# TODO.md — Açık İşler, Bekleyen Kararlar ve İlerleme Günlüğü

Son güncelleme: 2026-11-07 04:34 (Türkiye). Yol haritası ve teknik mimari `PLAN.md` dosyasındadır; bu dosya anlık açık işleri, bekleyen kullanıcı kararlarını ve tamamlanan adımları tutar.

## 1. Bekleyen İşler ve Kararlar

- [ ] **Uygulama planı onayı:** Git/README/VS Code hazırlığı onaylandı. Laravel kurulumu ve yeni uygulama aşamaları için PLAN.md onayı ayrıca bekleniyor.
- [x] **VS Code son adımı:** Proje ayarları güncellendi; JSON, yönerge yolları ve 21 Git dışlama/dahil etme senaryosu doğrulandı. Ayarlar bb9c6c7 commit'ine kaydedildi; VS Code açıldığında proje main dalıyla tanındı.
- [x] **GitHub teslimi:** Hazırlık commit'leri origin/main'e gönderildi; yerel/uzak commit eşleşmesi ve GitHub dosya listesi doğrulandı. Kök resources/ ile .git.del/ hiçbir commit'e girmedi.

**Kesinleşen kararlar**

- Git kökü proje ana dizinidir. Depo: https://github.com/ufuktanyeri/tech-user-guide
- Kök resources/ yeni rehberler için yerel içerik kaynağıdır; henüz veritabanına aktarılmamıştır ve mevcut haliyle GitHub'a gönderilmez.
- Laravel'in gelecekteki app/resources/ dizini uygulama kaynaklarıdır ve sürüm kontrolüne dahildir.
- Ayrıntılı kurallar AI_RULES.md, Copilot giriş yönergesi .github/copilot-instructions.md dosyasındadır.
- Eski yönerge dosyaları güncel kök dosya listesinde bulunmuyor; bunların silinmesi/taşınması bu çalışmada yapılmadı.
- Sahiplik sorunu ufukt hesabıyla yeni Git deposu oluşturularak giderildi. Eski boş metadata .git.del/ olarak yerelde korundu ve Git dışında tutuldu; gerekirse yalnızca kullanıcı siler.
- Eski Copilot eklenti pazarı hatası mevcut proje sahipliği sorunuyla aynı kabul edilmedi; genel Git ayarları değiştirilmedi.

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

- [x] **7 Kas 2026** — Git deposu ufukt hesabıyla oluşturuldu; uzak main geçmişi ve MIT lisansı korundu. Laravel/Vite ignore, EditorConfig ve satır sonu kuralları hazırlandı; kaynak rehberler commit dışında bırakıldı.
- [x] **7 Kas 2026** — Türkçe README, proje yönerge bağlantıları, Copilot dosya adı ve kaynak koleksiyonunun yerel kalacağına ilişkin belgeler güncellendi.

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
