# GitHub Copilot Proje Yönergeleri

Bu proje için [ayrıntılı AI kurallarını](../AI_RULES.md), [proje yönergelerini](../AGENTS.md) ve [yol haritasını](../PLAN.md) uygula. Açık işleri [TODO.md](../TODO.md) üzerinden takip et.

## Projenin amacı

Kullanıcı rehberlerini ortak bir yapıda hazırlamak ve admin dashboard üzerinden oluşturma, güncelleme, silme ve yayınlama işlemleriyle yönetmek. Öğrenciler kayıt olup yayınlanan rehberleri iki panelli, içindekiler ağaçlı bir web arayüzünde okur.

## Temel kurallar

- Kullanıcı onayı olmadan yeni aşamaya geçme; kapsamı genişletme.
- Planlanan teknoloji: Laravel 11.x, PHP 8.2+, Livewire 3, Alpine.js, Tailwind CSS, Blade ve kendi sunucusundan sunulan TinyMCE. Bu teknolojiyi kendiliğinden değiştirme.
- Arayüz, belgeler ve yorumlar Türkçe; kod tanımlayıcıları İngilizce; dosyalar UTF-8 olmalıdır.
- PHP dosyalarında strict types, açık parametre/dönüş tipleri, Service/Action ayrımı ve N+1 önlemleri uygulanır. Ortam değişkenlerini uygulamada doğrudan `env()` ile okuma; `config()` kullan.
- Yeni hesaplar `user` rolündedir. Yönetim yalnızca `admin` rolüne açıktır; anonim kullanıcı rehber göremez ve öğrenci yalnızca yayınlanmış rehberleri görür.
- Rehber başlık derinliği 1–5 aralığındadır ve sunucuda `SectionHierarchyService` üzerinden denetlenir.
- Editör HTML'i `HtmlSanitizerService` üzerinden temizlenir. Öğrenci girdilerini ham HTML olarak basma.
- Testler PHPUnit sınıflarıyla yazılır; Pint ve Larastan kalite araçlarıdır.
- Kök `resources/`, yeni rehberler için yerel içerik kaynağıdır. Henüz veritabanına aktarılmamıştır; değiştirme, Git'e ekleme veya GitHub'a gönderme.
- Gelecekteki `app/resources/`, Laravel arayüz kaynaklarıdır; kökteki yerel koleksiyonla karıştırma.
- Laravel iskeleti henüz kurulmamıştır. Mevcut dört PHP taslağını çalışan uygulama veya tamamlanan modül olarak sunma.
- Commit mesajlarında Conventional Commits kullan; gerçekleşmeyen testleri ve işlemleri tamamlandı olarak bildirme.
