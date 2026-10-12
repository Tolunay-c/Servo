# Eski adreslerden yeni adreslere yönlendirmeler

Statik sitenin `.html` adresleri WordPress'e geçişte değişir. Arama motorlarındaki kayıtlar ve dışarıdan verilmiş bağlantılar kırılmasın diye eski adresler **301 (kalıcı)** ile yeni adreslere yönlendirilir.

- Kurallar canlı sunucudaki `.htaccess` dosyasına, `# BEGIN WordPress` satırının **üstüne** eklenir. WordPress kendi bloğunu yeniden yazarken bu kısma dokunmaz.
- WordPress'in alan adının kök dizininde (`https://www.servomotion.com.tr/`) kurulu olduğu varsayılır. Alt klasöre kurulursa hedeflerin başına o klasör eklenir.
- Sorgu dizesi korunur (`iletisim.html?konu=film` → `/iletisim/?konu=film`). Çapa (`#lastik`) tarayıcıda kalır; yeni sayfalarda aynı çapalar vardır.
- Zincir oluşmaz: eski `blog-*.html` adresleri tasarımdaki gibi önce `rehber-*.html`'e değil, doğrudan yeni rehber adresine gider.

## Eşleşme listesi

### Sayfalar

| Eski adres | Yeni adres |
|---|---|
| `/index.html` | `/` |
| `/core-precisor.html` | `/core-precisor/` |
| `/shuttle-precisor.html` | `/shuttle-precisor/` |
| `/uygulama-lastik.html` | `/uygulama/lastik/` |
| `/uygulama-nonwoven.html` | `/uygulama/nonwoven/` |
| `/uygulama-ambalaj.html` | `/uygulama/ambalaj/` |
| `/referanslar.html` | `/referanslar/` |
| `/servis.html` | `/servis/` |
| `/kurumsal.html` | `/kurumsal/` |
| `/iletisim.html` | `/iletisim/` |
| `/dokumanlar.html` | `/dokumanlar/` |
| `/sss.html` | `/sss/` |
| `/sozluk.html` | `/sozluk/` |
| `/kvkk.html` | `/kvkk/` |
| `/rehber.html` | `/rehber/` |

### Rehber yazıları

| Eski adres | Yeni adres |
|---|---|
| `/rehber-core-konfigurasyonlari.html` | `/rehber/core-konfigurasyonlari/` |
| `/rehber-hassasiyet.html` | `/rehber/hassasiyet/` |
| `/rehber-kesif-sekiz-kriter.html` | `/rehber/kesif-sekiz-kriter/` |
| `/rehber-mekik-mimarisi.html` | `/rehber/mekik-mimarisi/` |
| `/rehber-nonwoven-kenar.html` | `/rehber/nonwoven-kenar/` |
| `/rehber-profinet-recete.html` | `/rehber/profinet-recete/` |
| `/rehber-sabit-referans.html` | `/rehber/sabit-referans/` |
| `/rehber-sistem-secimi.html` | `/rehber/sistem-secimi/` |
| `/rehber-urun-degisimi.html` | `/rehber/urun-degisimi/` |
| `/rehber-uygulamaya-ozel.html` | `/rehber/uygulamaya-ozel/` |
| `/rehber-yatirim-degeri.html` | `/rehber/yatirim-degeri/` |

### Eski blog adresleri (tasarımın `.htaccess`'indeki kurallar)

| Eski adres | Yeni adres |
|---|---|
| `/blog.html` | `/rehber/` |
| `/blog-{yazi}.html` | `/rehber/{yazi}/` |

### Dosyalar

PDF ve video dosyaları Media Library'ye yüklendiği için adresleri değişir. Aşağıdaki hedeflerde yerel kurulumdaki yükleme klasörü (`2026/10`) yazılıdır. **Canlıda dosyalar yüklendikten sonra Media Library'deki gerçek adresle kontrol edilip gerekirse düzeltilmelidir.**

| Eski adres | Yeni adres |
|---|---|
| `/servomotion-core-precisor-veri-sayfasi.pdf` | `/wp-content/uploads/2026/10/servomotion-core-precisor-veri-sayfasi.pdf` |
| `/servomotion-shuttle-precisor-veri-sayfasi.pdf` | `/wp-content/uploads/2026/10/servomotion-shuttle-precisor-veri-sayfasi.pdf` |
| `/servomotion-tuv-uygunluk-sertifikasi.pdf` | `/wp-content/uploads/2026/10/servomotion-tuv-uygunluk-sertifikasi.pdf` |
| `/video/servomotion-shuttle-precisor.mp4` | `/wp-content/uploads/2026/10/servomotion-shuttle-precisor.mp4` |
| `/sitemap.xml` | `/sitemap_index.xml` (Rank Math) |

Favicon'lar temadan basılır. Bazı tarayıcılar ve iOS kök dizindeki dosyaları doğrudan istediği için bu adresler de temadaki dosyalara yönlendirilir: `/favicon.svg`, `/favicon-32.png`, `/favicon-192.png`, `/favicon-512.png`, `/apple-touch-icon.png` → `/wp-content/themes/servo/assets/img/favicon/…`.

### Yönlendirilmeyenler

- `/404.html`: WordPress'in kendi 404 sayfası çalışır.
- `/kurulum-notlari.html`, `/KURULUM-NOTLARI.md`, `/servomotion-kurulum-notlari.pdf`, `/servomotion-kurulum-kontrol-listesi.xlsx`: müşteriye yazılmış iç notlardır, site sayfası değildir (sitemap'te yoktu). 404 döner.
- `/site.webmanifest`, `/robots.txt`: `robots.txt`'yi WordPress/Rank Math üretir. Manifest kullanılmıyor.

### İngilizce sayfalar (2. faz)

EN sayfaları Polylang ile 2. fazda gelir. O zamana kadar eski `/en/` adresleri **302 (geçici)** ile Türkçe karşılıklarına gider. Geçici yönlendirme kullanılır ki arama motorları eski EN adresleri kalıcı olarak TR'ye bağlamasın. 2. fazda bu blok, Polylang'in EN adresleriyle 301 kurallarına çevrilir.

| Eski adres | Geçici hedef |
|---|---|
| `/en/`, `/en/index.html` | `/` |
| `/en/core-precisor.html` | `/core-precisor/` |
| `/en/shuttle-precisor.html` | `/shuttle-precisor/` |
| `/en/applications.html` | `/` |
| `/en/service.html` | `/servis/` |
| `/en/company.html` | `/kurumsal/` |
| `/en/contact.html` | `/iletisim/` |
| `/en/faq.html` | `/sss/` |
| `/en/glossary.html` | `/sozluk/` |
| `/servomotion-core-precisor-datasheet.pdf` | `/dokumanlar/` |
| `/servomotion-shuttle-precisor-datasheet.pdf` | `/dokumanlar/` |

## .htaccess kural bloğu

`# BEGIN WordPress` satırının üstüne eklenir. İlk iki kural (https ve www) tasarımın `.htaccess`'inden gelir. www'suz adres kullanılacaksa tasarımdaki nota göre değiştirilir ve WordPress'in "Site adresi" ayarı aynı biçimde olmalıdır.

```apache
# BEGIN ServoMotion yönlendirmeler
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# https zorunlu
RewriteCond %{HTTPS} !=on
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteRule ^(.*)$ https://www.servomotion.com.tr/$1 [R=301,L]

# www'suz adresi www'ya taşı
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ https://www.servomotion.com.tr/$1 [R=301,L]

# Eski blog adresleri (tasarımdaki kurallar, doğrudan yeni rehber adresine)
RewriteRule ^blog\.html$ /rehber/ [R=301,L]
RewriteRule ^blog-(.+)\.html$ /rehber/$1/ [R=301,L]

# Rehber yazıları
RewriteRule ^rehber-(.+)\.html$ /rehber/$1/ [R=301,L]

# Uygulama sayfaları
RewriteRule ^uygulama-(lastik|nonwoven|ambalaj)\.html$ /uygulama/$1/ [R=301,L]

# Sayfalar
RewriteRule ^index\.html$ / [R=301,L]
RewriteRule ^(core-precisor|shuttle-precisor|referanslar|servis|kurumsal|iletisim|dokumanlar|sss|sozluk|kvkk|rehber)\.html$ /$1/ [R=301,L]

# Dosyalar (yükleme klasörünü canlıdaki Media Library adresiyle kontrol edin)
RewriteRule ^(servomotion-core-precisor-veri-sayfasi|servomotion-shuttle-precisor-veri-sayfasi|servomotion-tuv-uygunluk-sertifikasi)\.pdf$ /wp-content/uploads/2026/10/$1.pdf [R=301,L]
RewriteRule ^video/servomotion-shuttle-precisor\.mp4$ /wp-content/uploads/2026/10/servomotion-shuttle-precisor.mp4 [R=301,L]
RewriteRule ^sitemap\.xml$ /sitemap_index.xml [R=301,L]

# Favicon'lar
RewriteRule ^(favicon\.svg|favicon-(?:32|192|512)\.png|apple-touch-icon\.png)$ /wp-content/themes/servo/assets/img/favicon/$1 [R=301,L]

# İngilizce sayfalar: 2. faza kadar geçici (302)
RewriteRule ^en/(?:index\.html)?$ / [R=302,L]
RewriteRule ^en/(core-precisor|shuttle-precisor)\.html$ /$1/ [R=302,L]
RewriteRule ^en/applications\.html$ / [R=302,L]
RewriteRule ^en/service\.html$ /servis/ [R=302,L]
RewriteRule ^en/company\.html$ /kurumsal/ [R=302,L]
RewriteRule ^en/contact\.html$ /iletisim/ [R=302,L]
RewriteRule ^en/faq\.html$ /sss/ [R=302,L]
RewriteRule ^en/glossary\.html$ /sozluk/ [R=302,L]
RewriteRule ^servomotion-(core|shuttle)-precisor-datasheet\.pdf$ /dokumanlar/ [R=302,L]
</IfModule>
# END ServoMotion yönlendirmeler
```

Nginx kullanılırsa aynı eşleşmeler `return 301` / `rewrite ... permanent` kurallarıyla yazılır.

## Kontrol

Yayından sonra her eski adres için yönlendirmeyi doğrulayın, örneğin:

```sh
curl -sI https://www.servomotion.com.tr/rehber-hassasiyet.html | grep -i '^location'
```

Google Search Console'a yeni site haritası (`/sitemap_index.xml`) gönderilir ve "Sayfalar" raporundaki 404'ler birkaç hafta izlenir.
