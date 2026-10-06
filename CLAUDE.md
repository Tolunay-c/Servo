# CLAUDE.md

Bu repo, bir müşteri için hazırlanmış **statik HTML/CSS/JS tasarımın özel bir WordPress temasına dönüştürülmesi** projesidir. Repo kökü temanın kendisidir ve lokal ortamda `wp-content/themes/` altına clone'lanmış olarak çalışır.

## Temel kurallar

1. **`_design/` klasörüne asla dokunma.** Orijinal tasarım orada referans olarak durur. Okuyabilirsin, düzenleyemez, taşıyamaz, silemezsin.
2. **Tasarıma sadık kal.** HTML yapısı, class isimleri, ID'ler, animasyonlar ve responsive davranış birebir korunmalı. Tema çıktısı `_design/` içindeki sayfalarla görsel olarak aynı görünmeli.
3. **CSS ve JS'i yeniden yazma.** Tasarımın kendi dosyalarını `assets/` altına kopyala ve olduğu gibi kullan. Sadece yol (path) düzeltmeleri ve WordPress uyumu için gereken minimum değişiklikleri yap.
4. **Page builder kullanma** (Elementor, WPBakery vb.). Düzenlenebilir içerik ACF ile sağlanır.
5. Emin olmadığın bir yapısal kararda (yeni CPT, alan yapısı, sayfa şablonu) önce bana sor, sonra uygula.

## Klasör yapısı

```
/
├── _design/              # Orijinal tasarım (salt okunur, deploy edilmez)
├── docs/                 # Proje dokümanları (analiz, notlar; deploy edilmez)
├── acf-json/             # ACF Local JSON (alan grupları burada versiyonlanır)
├── assets/
│   ├── css/
│   ├── js/
│   ├── img/
│   └── fonts/
├── inc/
│   ├── setup.php         # theme supports, menüler, image sizes
│   ├── enqueue.php       # CSS/JS yükleme
│   ├── cpt.php           # Custom Post Type ve taxonomy tanımları
│   ├── acf.php           # ACF ayarları, options page, local JSON
│   └── helpers.php       # Yardımcı fonksiyonlar
├── template-parts/       # Tekrar eden bölümler (hero, kart, CTA vb.)
├── page-templates/       # Özel sayfa şablonları
├── functions.php         # Sadece inc/ dosyalarını require eder
├── header.php
├── footer.php
├── front-page.php
├── page.php
├── single.php
├── archive.php
├── index.php
├── 404.php
├── search.php
├── screenshot.png
└── style.css             # Tema başlığı (Theme Name, Text Domain vb.)
```

## Dönüşüm iş akışı

Her adımı ayrı commit olarak tamamla.

1. **Analiz:** `_design/` içindeki tüm sayfaları incele. Hangi bölümlerin ortak (header, footer, CTA), hangilerinin sayfaya özel olduğunu, hangi içeriklerin tekrar eden yapı olduğunu (hizmetler, projeler, ekip, referanslar vb.) çıkar ve bana kısa bir özet sun. Onayımdan sonra devam et.
2. **İskelet:** `style.css`, `functions.php`, `inc/` dosyaları ve boş şablonları oluştur.
3. **Asset'ler:** CSS, JS, görsel ve fontları `assets/` altına kopyala, `inc/enqueue.php` ile yükle.
4. **Header / Footer:** Ortak bölümleri `header.php` ve `footer.php`'ye taşı. `wp_head()`, `wp_body_open()`, `wp_footer()` çağrılarını unutma. Menüleri `wp_nav_menu` ile dinamik yap; tasarımın menü markup'ını korumak gerekirse custom walker yaz.
5. **Ana sayfa:** `front-page.php`'yi oluştur, her bölümü `template-parts/` altına ayır.
6. **Diğer sayfalar:** Her tasarım sayfası için uygun şablonu oluştur (`page-templates/` veya `page-{slug}.php`).
7. **Dinamik içerik:** Sabit metin ve görselleri ACF alanlarına bağla. Tekrar eden içerikler için CPT tanımla.
8. **Blog / arşiv / tekil:** Tasarımda varsa `archive.php`, `single.php`; yoksa tasarımın genel stiline uygun sade versiyonlarını üret.
9. **Formlar:** İletişim formlarını çalışır hale getir (Contact Form 7 markup'ı tasarıma uyarlanır veya `admin-post.php` ile özel handler yazılır, karar için bana sor).
10. **Kontrol:** Aşağıdaki kontrol listesini uygula.

## Kod standartları

- **Fonksiyon öneki:** Tüm tema fonksiyonları `servo_` önekiyle başlar. Global namespace'i kirletme.
- **Text domain:** Kullanıcıya görünen sabit metinler `__()`, `esc_html_e()` vb. ile çeviriye hazır olmalı.
- **Escape:** Her çıktı escape edilir: `esc_html()`, `esc_attr()`, `esc_url()`, zengin metin için `wp_kses_post()`.
- **Yollar:** Asset yolları için her zaman `get_template_directory_uri()` kullan, asla hardcoded URL yazma.
- **Görseller:** İçerik görselleri `wp_get_attachment_image()` ile basılsın (srcset ve lazy loading için). Gerekirse `add_image_size()` ile boyut tanımla.
- **Enqueue:** CSS/JS sadece `wp_enqueue_style` / `wp_enqueue_script` ile yüklenir, versiyon olarak `filemtime()` kullanılır. Şablonlara doğrudan `<link>` veya `<script>` yazma.
- **jQuery:** Tasarım jQuery kullanmıyorsa ekleme. Kullanıyorsa WordPress'in kendi jQuery'sini dependency olarak tanımla.
- **WordPress Coding Standards**'a uy (girinti tab, Yoda conditions zorunlu değil ama tutarlı ol).

## ACF kuralları

- Alan grupları ACF arayüzünden oluşturulur ve **Local JSON** ile `acf-json/` klasörüne kaydedilir (`inc/acf.php` içinde save/load point ayarlı olmalı).
- Genel site ayarları (logo, telefon, adres, sosyal medya linkleri, footer metinleri) için bir **Options Page** kullan.
- Alan isimleri `snake_case` ve anlamlı olsun (`hero_baslik`, `hero_gorsel`).
- ACF eklentisi devre dışıyken site fatal error vermemeli: `get_field()` çağrılarını `function_exists()` kontrolü ile sar veya helper fonksiyon kullan.
- Boş alanlar için markup basılmamalı (boş başlık etiketi, boş görsel vb.).

## Repoya girmeyecekler

WordPress core dosyaları, `wp-config.php`, `wp-content/uploads`, `node_modules`, `.env`, işletim sistemi/editör dosyaları. `_design/` repoda tutulur ama canlıya deploy edilmez.

## Deploy edilmeyecekler

`_design/` ve `docs/` repoda tutulur ama canlıya deploy edilmez.

## Proje kararları

Analiz (`docs/ANALIZ.md`) sonrası onaylanan kararlar:

1. **Çok dillilik:** Polylang kullanılacak. 1. fazda sadece TR yapılır, EN şablonları 2. fazda gelir. Mimari Polylang'e uygun kurulur:
   - Tüm sabit metinler çeviriye hazırdır.
   - Options alanları şablonlarda doğrudan `get_field( 'x', 'option' )` ile değil, her zaman `servo_option()` helper'ıyla okunur. 2. fazda dile göre ayrım (`options_tr` / `options_en`) sadece bu helper değiştirilerek eklenir.
2. **CSS:** Tasarımdaki `<style>` blokları içerik değiştirilmeden `assets/css/` altında dosyalara taşınır ve ilgili şablonda koşullu enqueue edilir.
3. **Header:** İki header yapısı (standart ve ürün sayfası) var. Aynı WP menüsü iki ayrı walker ile basılır. Walker'lar alt menüyü (tasarımdaki `navdrop` yapısı) destekler.
4. **Inline SVG'ler:** Temada `assets/img/svg/` altında dosya olarak tutulur. ACF'de "görsel" alanı açılır: görsel yüklenirse fotoğraf, boşsa varsayılan SVG basılır.
5. **Ürün sayfaları:** Konfigüratör ve simülasyondaki sabitler ve metinler (`CFG` dizileri vb.) JS'te kalır. Hero, KPI, metin bölümleri, teknik tablolar ve CTA ACF'ye bağlanır.
6. **Bülten:** Ön yüz şimdilik mevcut davranışla ("e-posta ile yazın") kalır. `servomotion/v1/bulten` REST endpoint'i kaydedilir ve şimdilik "bağlı değil" yanıtı döner, ileride sadece servis bağlantısı eklenir.
7. **Rehber içeriği:** Gutenberg kullanılır. `.callout`, `.keyval` ve `.atable` için block style/pattern tanımlanır. 11 yazı için sonradan WP-CLI import scripti yazılacak.
8. **URL ve yönlendirmeler:**
   - Rehber yazılarının adresi `/rehber/yazi-adi/` olur. Bu yapı Ayarlar > Kalıcı bağlantılar'da `/rehber/%postname%/` olarak ayarlanır (tema bunu zorlamaz). Bu yüzden tüm CPT ve taxonomy'ler `with_front => false` ile kaydedilir.
   - Eski `*.html` adresleri `.htaccess` içinde 301 ile yönlendirilir. Proje sonunda eski-yeni URL eşleşme listesi ve kural bloğu çıkarılır.
9. **SEO:** Rank Math kullanılır. Tema description, OG, canonical ve hreflang basmaz. Organization şemasını Rank Math verir. Tema sadece SSS sayfasında, SSS CPT'sinden FAQPage JSON-LD üretir (Rank Math FAQ bloğu kullanılmaz, çift şema olmaz).
10. **Fontlar:** Barlow, Barlow Condensed, IBM Plex Mono ve Poppins `assets/fonts/` altında self-host edilir. Google Fonts CDN kullanılmaz.
11. **Kapsam dışı:** `kurulum-notlari.html`, `KURULUM-NOTLARI.md`, `.htaccess`, `robots.txt`, `sitemap.xml` temaya dönüştürülmez. PDF, video ve xlsx dosyaları Media Library'ye yüklenir.
12. **Önek ve text domain:** Fonksiyon öneki `servo_`, text domain `servomotion`.
13. **Alan eklentisi:** ACF Pro yerine Secure Custom Fields (SCF) kullanılır. API aynıdır (repeater, options page, relationship dahil).
14. **Formlar:** Uygulama özeti aracı için `register_rest_route` ile özel bir endpoint yazılır ve `wp_mail` ile gönderilir (nonce, honeypot, rate-limit). Endpoint adresi `wp_add_inline_script` ile tasarımın `window.SERVOMOTION_FORM_ENDPOINT` kancasına verilir, tasarımın JS'i değişmez.
15. **CPT ve taxonomy'ler:**
    - Rehber yazıları yerleşik `post` tipindedir: kategori + `urun_alani` taxonomy'si.
    - `uygulama` (tekil sayfalı), `vaka`, `dokuman`, `sss` (+ `sss_grup` taxonomy'si), `terim` CPT'leri kullanılır.
    - `uygulama` tekil adresi `/uygulama/{slug}/` olur. `has_archive` false'tur, `/uygulama/` arşiv üretmez.
    - `urun_alani` ve `sss_grup` ön yüzde arşiv üretmez. `vaka`, `dokuman`, `sss` ve `terim` tekil sayfasızdır ve aramada çıkmaz.
    - Referanslar sayfasında her vakanın `id`'si post slug'ından üretilir (`servo_vaka_anchor()`). Uygulama sayfalarındaki vaka linkleri vakayı relationship alanından alır ve `servo_vaka_url()` ile basar. Böylece başlık değişse de çapa kırılmaz.
    - Ürünler CPT değil, 2 ayrı sayfa şablonudur.
    - Kurumsal ve servis listeleri ACF repeater ile yapılır.

## Kontrol listesi

- [ ] Tüm sayfalar `_design/` ile görsel olarak birebir aynı
- [ ] Mobil, tablet ve masaüstünde responsive davranış korunmuş
- [ ] Konsolda JS hatası yok, 404 veren asset yok
- [ ] Menüler panelden düzenlenebiliyor
- [ ] Tüm metin ve görseller panelden değiştirilebiliyor
- [ ] ACF kapalıyken site çökmüyor
- [ ] `WP_DEBUG` açıkken notice/warning yok
- [ ] Formlar çalışıyor ve mail gidiyor
- [ ] Sayfa başlıkları `add_theme_support('title-tag')` ile yönetiliyor, SEO eklentisiyle uyumlu
- [ ] 404 ve arama sayfaları tasarıma uygun

## Commit mesajları

Kısa ve Türkçe, yapılan işi anlatan biçimde: `header ve footer temaya taşındı`, `hizmetler CPT eklendi`, `iletişim formu bağlandı`.