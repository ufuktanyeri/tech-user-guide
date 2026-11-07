# Tech User Guide

**Kullanıcı rehberlerini standart bir yapıda hazırlama, yönetme ve okuma platformu.**

Tech User Guide; teknoloji, geliştirme araçları, yapay zekâ ve akademik çalışmalar için hazırlanan kullanıcı rehberlerini ortak bir içerik ve sunum düzeninde bir araya getirmek amacıyla geliştirilmektedir. Hedef, her yeni rehber için ayrı bir dosya düzeni ve arayüz hazırlamak yerine, içeriklerin yönetim panelinden oluşturulabildiği ve sürdürülebildiği bir ortam sağlamaktır.

> **Geliştirme durumu:** Mimari ve yol haritası belgelenmiştir. Laravel uygulama iskeleti henüz kurulmamıştır. Bu belgede anlatılan kullanıcı işlevleri geliştirme hedefleridir; mevcut depo çalıştırılabilir bir portal sunmaz.

## Neden bu proje?

Mevcut rehberlerin başlık, içerik, gezinme ve görsel sunum bakımından ortak bir standarda kavuşması; yeni rehberlerin aynı düzen içinde hazırlanması amaçlanmaktadır. Rehber sayısı arttığında yeni sayfaları elle kodlamak yerine veritabanına dayalı kategori, rehber ve bölüm yapısı kullanılacaktır.

Yönetici, rehberin tamamını ve her bir bölümünü aynı uygulamada yönetebilecek. Okuyucu ise hangi rehberi açarsa açsın benzer bir okuma ve gezinme deneyimiyle karşılaşacaktır.

## Admin dashboard ve rehber hazırlama

Yönetim paneli aşağıdaki işlemler için tasarlanmıştır:

1. Rehber oluşturma; başlık, açıklama, kategori ve rehber türü belirleme.
2. Her rehber için izin verilen başlık derinliğini 1–5 arasında seçme.
3. İçindekiler ağacını kurma; başlık ve alt başlık ekleme, yeniden adlandırma, sıralama ve taşıma.
4. Başlıkların içeriğini Türkçe TinyMCE editörüyle hazırlama; metin, liste, tablo, görsel, kod bloğu ve bilgi/uyarı kutuları kullanma.
5. İçeriği okuyucunun göreceği düzende önizleme.
6. Taslak olarak saklama, yayınlama, güncelleme ve onaylı silme.
7. Kategorileri ve rehberleri yönetme; kategori, tür ve yayın durumuyla filtreleme.

Yazım ve okuma ekranları aynı tasarım sistemini paylaşacaktır. Başlık derinliği ve yetki kontrolleri sunucu tarafında uygulanacak; editörden gelen HTML kaydedilmeden önce temizlenecektir.

## Öğrenci ve okuyucu deneyimi

Öğrenciler kendi hesaplarını açıp giriş yapacaktır. Yeni hesaplar `user` rolünde olacak; yalnızca yayınlanmış rehberlere erişebilecektir. Yönetim işlemleri `admin` rolüyle sınırlandırılacaktır.

Okuma ekranı, CHM yardım dosyalarındaki gezinme düzenini web arayüzüne taşır:

- **Rehber kataloğu:** Kategori, tür ve başlık/açıklama üzerinden arama ve filtreleme.
- **Sol panel:** Açılır/kapanır içindekiler ağacı ve anlık başlık arama.
- **Sağ panel:** Seçilen bölümün içeriği, kategori yolu ve önceki/sonraki bölüm bağlantıları.
- **Okuma kolaylığı:** Mobil uyum ve temiz yazdırma görünümü.

Buradaki CHM ifadesi iki panelli okuma düzenini anlatır. Tek dosya CHM veya çevrimdışı HTML çıktısı sonraki fazda değerlendirilecektir.

## İçerik sınıflandırması

| Ana kategoriler | Rehber türleri |
| --- | --- |
| Yapay Zekâ | How to — Nasıl yapılır? |
| Web Programlama | What is... — Nedir? |
| Geliştirme Araçları | Readings — Okuma ve çalışma notları |
| Akademik | Glossary — Sözlük ve katalog |
| Diğer | |

Veri modeli; kullanıcılar, kategoriler, rehberler, hiyerarşik bölümler ve yüklenen medyadan oluşur. Başlık ağacı rehber bazında oluşturulacak ve önbelleklenecektir.

## Mevcut rehberlerin rolü

Yerel proje kökündeki `resources/`, daha önce hazırlanmış rehberlerin bulunduğu **kaynak içerik koleksiyonudur**. Bu dosyalar, yeni uygulamada standart rehber içerikleri hazırlanırken kullanılacaktır.

**Bu koleksiyon henüz veritabanına aktarılmamıştır ve mevcut haliyle GitHub'a gönderilmez.** Kök `.gitignore` dosyasındaki `/resources/` kuralı yalnızca bu yerel klasörü dışlar. Depoyu klonlayan kişide bu koleksiyon bulunmaz.

Laravel kurulduğunda oluşacak `app/resources/` ise uygulamanın Blade, CSS ve JavaScript kaynaklarını içerecektir; yerel rehber koleksiyonundan farklıdır ve sürüm kontrolüne dahildir.

Mevcut dosyaların otomatik olarak veritabanına toplu aktarılması ilk geliştirme fazının dışında tutulmuştur.

## Planlanan teknoloji yığını

| Katman | Teknoloji |
| --- | --- |
| Sunucu | PHP 8.2+, Laravel 11.x, Eloquent |
| Uygulama mimarisi | MVC, Service ve Action katmanları |
| Arayüz | Blade, Livewire 3, Alpine.js, Tailwind CSS |
| Kimlik doğrulama | Laravel Breeze — Livewire starter kit |
| İçerik editörü | Kendi sunucusundan sunulan Türkçe TinyMCE |
| Veri depolama | Geliştirmede SQLite, sunucuda MySQL 8 |
| Varlık derleme | Vite ve npm |
| Kalite araçları | PHPUnit, Laravel Pint, Larastan/PHPStan |

Bu tablo mevcut mimari planı aktarır. Paketler henüz kurulmadığı için bir bağımlılık kilit dosyası veya doğrulanmış kurulum çıktısı değildir.

## Depo düzeni

| Konum | Görevi |
| --- | --- |
| `app/` | Laravel uygulamasının kurulacağı dizin; şu anda dört PHP taslağı içerir. |
| `README.md` | Projenin amacı ve başlangıç bilgileri. |
| `PLAN.md` | Mimari, veri modeli, ekranlar ve aşamalı yol haritası. |
| `TODO.md` | Açık işler, bekleyen kararlar ve tamamlanan hazırlıklar. |
| `AI_RULES.md` | Ayrıntılı geliştirme ve yapay zekâ asistanı kuralları. |
| `AGENTS.md`, `CLAUDE.md` | Asistanlara yönelik proje giriş yönergeleri. |
| `.github/` | GitHub Copilot proje yönergeleri. |
| `.vscode/` | Paylaşılan VS Code çalışma alanı ayarları. |
| `docs/surec.md` | Çalışma kaydı ve modül durumları. |

`app/` içindeki migration, enum, kategori seeder ve hiyerarşi servisi taslakları henüz Laravel iskeletine entegre edilmemiştir. Bunların bulunması ilgili modüllerin tamamlandığı anlamına gelmez.

## Geliştirmeye başlama

Depoyu klonlayıp VS Code ile proje kökünü açabilirsiniz:

```powershell
git clone https://github.com/ufuktanyeri/tech-user-guide.git
cd tech-user-guide
code .
```

Önce [yol haritasını](PLAN.md), [açık işleri](TODO.md) ve [geliştirme kurallarını](AI_RULES.md) okuyun.

Laravel iskeleti, `composer.json`, `package.json` ve `artisan` henüz bulunmadığından bu aşamada `composer install`, `npm install` veya `php artisan serve` ile uygulama başlatılamaz. Kurulum, mevcut taslaklar korunarak ve ilgili aşama onaylandığında yapılacaktır. Uygulama komutlarının çalışma dizini daha sonra `app/` olacaktır.

## Yol haritası

1. Ortam uyumluluğunu doğrulama ve Laravel iskeletini kurma.
2. Kayıt/giriş, roller ve yetkilendirme.
3. Veri modeli, kategoriler ve başlık hiyerarşisi.
4. Admin rehber ve kategori yönetimi.
5. İçindekiler ağacı ve zengin içerik editörü.
6. Öğrenci kataloğu ve iki panelli okuma ekranı.
7. Türkçeleştirme, erişim/güvenlik testleri, mobil görünüm ve teslim.

Ayrıntılı kabul ölçütleri [PLAN.md](PLAN.md) içindedir. Otomatik eski içerik aktarımı, çevrimdışı dışa aktarma, sürüm geçmişi, yer imleri ve okuma ilerlemesi sonraki fazda değerlendirilecektir.

## Çalışma kuralları

- Arayüz, açıklamalar ve belgeler Türkçe; kod tanımlayıcıları İngilizcedir.
- Dosyalar UTF-8 kodlamasıyla tutulur; ortak biçim kuralları EditorConfig ve Git attributes ile tanımlıdır.
- Mimari kararların kaynağı `PLAN.md`, açık işlerin kaynağı `TODO.md` dosyasıdır.
- Yeni uygulama aşamalarına kullanıcı onayıyla geçilir.
- Commit mesajları Conventional Commits biçimindedir.
- Yerel kaynak rehberler, gizli ortam dosyaları, bağımlılıklar ve üretilen çalışma çıktıları commit edilmez.

## Lisans

Depo [MIT lisansı](LICENSE) ile sunulmaktadır.
