# Eski adreslerden yeni adreslere yönlendirmeler

Statik sitenin `.html` adresleri WordPress'e geçişte değişir. Arama motorlarındaki kayıtlar ve dışarıdan verilmiş bağlantılar kırılmasın diye eski adresler **301 (kalıcı)** ile yeni adreslere yönlendirilir.

- Kurallar canlı sunucudaki `.htaccess` dosyasına, `# BEGIN WordPress` satırının **üstüne** eklenir. WordPress kendi bloğunu yeniden yazarken bu kısma dokunmaz.
- WordPress'in alan adının kök dizininde (`https://servomotion.com.tr/`, **www'suz**) kurulu olduğu varsayılır. Tek adres www'suzdur: Ayarlar > Genel'deki WordPress ve Site adresi `https://servomotion.com.tr`, www'lu adres ve http aşağıdaki kurallarla 301 ile buna yönlendirilir. Alt klasöre kurulursa hedeflerin başına o klasör eklenir.
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
| `/uygulama-film-folyo.html` | `/uygulama/film-folyo/` |
| `/uygulama-tekstil.html` | `/uygulama/tekstil/` |
| `/uygulama-etiket.html` | `/uygulama/etiket/` |
| `/referanslar.html`, `/referanslar/` | `/basari-hikayeleri/` (sayfa adı "Başarı Hikayeleri" oldu) |
| `/basari-hikayeleri.html` | `/basari-hikayeleri/` |
| `/servis.html` | `/servis/` |
| `/servis-destek-hatti.html` | `/servis-destek-hatti/` |
| `/servis-iade-sevkiyat.html` | `/servis-iade-sevkiyat/` |
| `/servis-kilavuzlar.html` | `/servis-kilavuzlar/` |
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

Açık dosyalar (kurulum notları, uygulama notları, video) Media Library'ye yüklendiği için adresleri değişir. Aşağıdaki hedeflerde yerel kurulumdaki yükleme klasörü (`2026/10`) yazılıdır. **Canlıda dosyalar yüklendikten sonra Media Library'deki gerçek adresle kontrol edilip gerekirse düzeltilmelidir.**

**Veri sayfaları ve uygunluk sertifikası artık doğrudan indirilemez** (talep formuyla, 7 gün geçerli bağlantıyla verilir; dosyalar `uploads/servo-korumali/` altındadır). Bu yüzden eski doğrudan PDF adresleri indirme yerine dokümanlar sayfasındaki talep formuna yönlendirilir.

| Eski adres | Yeni adres |
|---|---|
| `/servomotion-core-precisor-veri-sayfasi.pdf` | `/dokumanlar/#talep` |
| `/servomotion-shuttle-precisor-veri-sayfasi.pdf` | `/dokumanlar/#talep` |
| `/servomotion-tuv-uygunluk-sertifikasi.pdf` | `/dokumanlar/#talep` |
| `/uygulama-{lastik,nonwoven,ambalaj,film-folyo,tekstil,etiket}.pdf` | `/wp-content/uploads/2026/10/uygulama-{…}.pdf` |
| `/servomotion-kurulum-notlari.pdf`, `/servomotion-kurulum-kontrol-listesi.xlsx` | `/wp-content/uploads/2026/10/…` (kılavuzlar sayfasında serbest erişimli) |
| `/video/servomotion-shuttle-precisor.mp4` | `/wp-content/uploads/2026/10/servomotion-shuttle-precisor.mp4` |
| `/sitemap.xml` | `/sitemap_index.xml` (Rank Math) |

Favicon'lar temadan basılır. Bazı tarayıcılar ve iOS kök dizindeki dosyaları doğrudan istediği için bu adresler de temadaki dosyalara yönlendirilir: `/favicon.svg`, `/favicon-32.png`, `/favicon-192.png`, `/favicon-512.png`, `/apple-touch-icon.png` → `/wp-content/themes/servo/assets/img/favicon/…`.

### Yönlendirilmeyenler

- `/404.html`: WordPress'in kendi 404 sayfası çalışır.
- `/kurulum-notlari.html`, `/KURULUM-NOTLARI.md`: müşteriye yazılmış iç notlardır, site sayfası değildir (sitemap'te yoktu). 404 döner. (Aynı adlı PDF ve xlsx dosyaları ise kılavuzlar sayfasında yayındadır, yukarıdaki tabloya bakın.)
- `/site.webmanifest`, `/robots.txt`: `robots.txt`'yi WordPress/Rank Math üretir. Manifest kullanılmıyor.

### İngilizce sayfalar (FAZ 4)

Yayında olan EN sayfaların eski `*.html` adresleri **301 (kalıcı)** ile yeni Polylang adreslerine gider. EN karşılığı henüz yayında olmayan sayfalar (Core Precisor, Shuttle Precisor) taslaktır; bunların eski adresleri sayfalar yayınlanana kadar **302 (geçici)** ile Türkçe karşılığına gider. Her sayfa yayınlandığında ilgili satır 301'e çevrilir.

Not: Polylang, EN ana sayfayı `/en/home/` adresinde sunar (`/en/` buraya yönlenir); ön ek ve sayfa adı birlikte gelir.

| Eski adres | Hedef | Tür |
|---|---|---|
| `/en/`, `/en/index.html` | `/en/home/` | 301 |
| `/en/applications.html` | `/en/applications/` | 301 |
| `/en/application-<ad>.html` (`rubber`, `nonwoven`, `packaging`, `film-foil`, `textiles`, `labels`) | `/en/application/<ad>/` | 301 |
| `/en/service.html` | `/en/service/` | 301 |
| `/en/service-helpdesk.html`, `service-return-shipment.html`, `service-manuals.html` | `/en/service-…/` (aynı ad) | 301 |
| `/en/company.html` | `/en/company/` | 301 |
| `/en/documents.html` | `/en/documents/` | 301 |
| `/en/success-stories.html` | `/en/success-stories/` | 301 |
| `/en/guide.html` | `/en/guide/` | 301 |
| `/en/guide-<ad>.html` | `/en/guide/<ad>/` | 301 |
| `/en/core-precisor.html` | `/core-precisor/` | 302 |
| `/en/shuttle-precisor.html` | `/shuttle-precisor/` | 302 |
| `/en/contact.html` | `/en/contact/` | 301 |
| `/en/faq.html` | `/en/faq/` | 301 |
| `/en/glossary.html` | `/en/glossary/` | 301 |
| `/en/privacy.html` | `/en/privacy/` | 301 |
| `/servomotion-core-precisor-datasheet.pdf` | `/dokumanlar/` |
| `/servomotion-shuttle-precisor-datasheet.pdf` | `/dokumanlar/` |

## .htaccess kural bloğu

`# BEGIN WordPress` satırının üstüne eklenir. İlk iki kural www'lu adresi ve http'yi **tek adımda** `https://servomotion.com.tr`'ye (www'suz) yönlendirir; WordPress'in "Site adresi" ayarı da aynı biçimde (`https://servomotion.com.tr`) olmalıdır. Kuralların davranışı Apache'de denendi: `www.servomotion.com.tr/sayfa/?a=1` → `https://servomotion.com.tr/sayfa/?a=1` (301, tek adım).

```apache
# BEGIN ServoMotion yönlendirmeler
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# Tek adres: https://servomotion.com.tr (www'suz). www ve http tek adımda buna gider.
RewriteCond %{HTTP_HOST} ^www\.servomotion\.com\.tr$ [NC]
RewriteRule ^(.*)$ https://servomotion.com.tr/$1 [R=301,L]

RewriteCond %{HTTPS} !=on
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteCond %{HTTP_HOST} ^servomotion\.com\.tr$ [NC]
RewriteRule ^(.*)$ https://servomotion.com.tr/$1 [R=301,L]

# Eski blog adresleri (tasarımdaki kurallar, doğrudan yeni rehber adresine)
RewriteRule ^blog\.html$ /rehber/ [R=301,L]
RewriteRule ^blog-(.+)\.html$ /rehber/$1/ [R=301,L]

# Rehber yazıları
RewriteRule ^rehber-(.+)\.html$ /rehber/$1/ [R=301,L]

# Vaka çalışmaları → Başarı hikayeleri
RewriteRule ^referanslar(\.html|/)?$ /basari-hikayeleri/ [R=301,L]

# Uygulama sayfaları
RewriteRule ^uygulama-(lastik|nonwoven|ambalaj|film-folyo|tekstil|etiket)\.html$ /uygulama/$1/ [R=301,L]

# Sayfalar
RewriteRule ^index\.html$ / [R=301,L]
RewriteRule ^(core-precisor|shuttle-precisor|basari-hikayeleri|servis|servis-destek-hatti|servis-iade-sevkiyat|servis-kilavuzlar|kurumsal|iletisim|dokumanlar|sss|sozluk|kvkk|rehber)\.html$ /$1/ [R=301,L]

# Dosyalar (yükleme klasörünü canlıdaki Media Library adresiyle kontrol edin)
# Veri sayfaları ve sertifika doğrudan indirilemez: talep formuna gider.
RewriteRule ^(servomotion-core-precisor-veri-sayfasi|servomotion-shuttle-precisor-veri-sayfasi|servomotion-tuv-uygunluk-sertifikasi)\.pdf$ /dokumanlar/#talep [R=301,NE,L]
RewriteRule ^(uygulama-(?:lastik|nonwoven|ambalaj|film-folyo|tekstil|etiket)|servomotion-kurulum-notlari)\.pdf$ /wp-content/uploads/2026/10/$1.pdf [R=301,L]
RewriteRule ^servomotion-kurulum-kontrol-listesi\.xlsx$ /wp-content/uploads/2026/10/servomotion-kurulum-kontrol-listesi.xlsx [R=301,L]
RewriteRule ^video/servomotion-shuttle-precisor\.mp4$ /wp-content/uploads/2026/10/servomotion-shuttle-precisor.mp4 [R=301,L]
RewriteRule ^sitemap\.xml$ /sitemap_index.xml [R=301,L]

# Favicon'lar
RewriteRule ^(favicon\.svg|favicon-(?:32|192|512)\.png|apple-touch-icon\.png)$ /wp-content/themes/servo/assets/img/favicon/$1 [R=301,L]

# İngilizce sayfalar: yayında olanlar 301 (FAZ 4)
RewriteRule ^en/(?:index\.html)?$ /en/home/ [R=301,L]
RewriteRule ^en/application-(rubber|nonwoven|packaging|film-foil|textiles|labels)\.html$ /en/application/$1/ [R=301,L]
RewriteRule ^en/guide-([a-z-]+)\.html$ /en/guide/$1/ [R=301,L]
RewriteRule ^en/(applications|service|service-helpdesk|service-return-shipment|service-manuals|company|documents|success-stories|guide|contact|faq|glossary|privacy)\.html$ /en/$1/ [R=301,L]
# EN karşılığı taslak olanlar: yayınlanana kadar geçici (302)
RewriteRule ^en/(core-precisor|shuttle-precisor)\.html$ /$1/ [R=302,L]
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
