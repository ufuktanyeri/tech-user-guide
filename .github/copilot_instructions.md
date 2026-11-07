# GitHub Copilot ve AI Kodlama Asistanı Yönergeleri

Bu depoda kod üretirken, mevcut kodu incelerken veya test yazarken projenin kök dizininde (veya `app/` dizininde) bulunan **`CLAUDE.md`** ve **`PLAN.md`** dosyalarındaki tüm mimari, kodlama, güvenlik ve dil kurallarına istisnasız sadık kal.

## Temel Kırmızı Çizgiler (Özet)

1. **Tek Doğruluk Kaynağı:** Yol haritası, veri modeli ve aşama kararları `PLAN.md` dosyasındadır; açık işler `TODO.md` içinde takip edilir. Kullanıcının onayı olmadan yeni aşamaya geçilmez ve kapsam genişletilmez.
2. **Teknoloji Yığını:** Laravel 11.x (PHP 8.2+), Livewire 3 + Alpine.js, Tailwind CSS, Blade, TinyMCE (self-hosted, Türkçe), SQLite (Geliştirme) / MySQL 8 (Sunucu), PHPUnit (`Tests\TestCase`), Laravel Pint ve Larastan.
3. **Dil Kuralı:** Tüm arayüz metinleri (`app/lang/tr`), doğrulama mesajları, açıklamalar ve yorum satırları akıcı **Türkçe**; tüm sınıf, metot, değişken, rota ve veritabanı tablo/sütun adları kesinlikle **İngilizce** yazılır.
4. **Katı PHP ve Eloquent Standartları:**
   - Tüm PHP dosyalarının başında `declare(strict_types=1);` zorunludur; tüm parametre ve dönüş tipleri açıkça belirtilir.
   - Tembel kodlama (`// ... kodun geri kalanı aynı ...`, `// TODO`) kesinlikle yasaktır; kodlar eksiksiz verilir.
   - `app/` içinde doğrudan `env()` çağrılmaz; `config()` kullanılır.
   - N+1 sorguları yasaktır (`Model::preventLazyLoading()`); ilişkiler `with()` ile çekilir.
5. **Güvenlik ve Arşiv Koruması:**
   - Editörden gelen HTML içerik `HtmlSanitizerService` ile temizlenmeden kaydedilmez; öğrenci girdilerinde `{!! !!}` kullanılmaz.
   - `resources/` dizini eski rehberlerin arşividir; salt okunurdur ve dokunulmaz.
