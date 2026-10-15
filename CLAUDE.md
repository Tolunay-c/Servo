# CLAUDE.md

Bu repo, bir müşteri için hazırlanmış **statik HTML/CSS/JS tasarımın özel bir WordPress temasına dönüştürülmesi** projesidir. Repo kökü temanın kendisidir ve lokal ortamda `wp-content/themes/` altına clone'lanmış olarak çalışır.

Tamamlanmış fazların ayrıntıları, uygulama notları ve karar gerekçeleri `docs/faz0-analiz.md` sonundaki "CLAUDE.md'den taşınan ayrıntılar" bölümündedir.

## Temel kurallar

1. **`_design/` klasörüne asla dokunma.** Orijinal tasarım orada referans olarak durur. Okuyabilirsin, düzenleyemez, taşıyamaz, silemezsin.
2. **Tasarıma sadık kal.** HTML yapısı, class isimleri, ID'ler, animasyonlar ve responsive davranış birebir korunmalı. Tema çıktısı **`servomotion-site-tum131026/`** içindeki sayfalarla görsel olarak aynı görünmeli. Bu klasör son teslim paketidir (revizyonlar uygulanmış) ve yeni referans tasarımdır; `_design/` revizyonlardan önceki eski sürümdür ve artık referans alınmaz. Bu klasör de `_design/` gibi salt okunurdur: hiçbir dosyasına dokunma.
3. **CSS ve JS'i yeniden yazma.** Tasarımın kendi dosyalarını `assets/` altına kopyala ve olduğu gibi kullan. Sadece yol (path) düzeltmeleri ve WordPress uyumu için gereken minimum değişiklikleri yap.
4. **Page builder kullanma** (Elementor, WPBakery vb.). Düzenlenebilir içerik ACF (SCF) ile sağlanır.
5. Emin olmadığın bir yapısal kararda (yeni CPT, alan yapısı, sayfa şablonu) önce bana sor, sonra uygula.
6. **Asla commit veya push yapma; değişiklikleri sadece çalışma ağacında bırak.** Commit'leri proje sahibi atar.
7. Her iş adımını ayrı tamamla ve sonunda dur.

## Klasör yapısı

```
/
├── _design/              # Eski tasarım, revizyon öncesi (salt okunur, deploy edilmez)
├── servomotion-site-tum131026/  # Yeni referans tasarım, son teslim paketi (salt okunur, deploy edilmez)
├── docs/                 # Proje dokümanları (analiz, kılavuz, yönlendirmeler, canlı kurulum; deploy edilmez)
├── bin/                  # Geliştirme betikleri (paket.sh: canlı tema paketi; deploy edilmez)
├── acf-json/             # ACF Local JSON (alan grupları burada versiyonlanır)
├── assets/               # css, js, img, fonts
├── languages/            # servomotion.pot, servomotion-en_GB.po/.mo
├── inc/
│   ├── setup.php         # theme supports, menüler, image sizes
│   ├── enqueue.php       # CSS/JS yükleme
│   ├── cpt.php           # Custom Post Type ve taxonomy tanımları
│   ├── acf.php           # ACF ayarları, options page, local JSON
│   ├── bloklar.php       # Flexible content altyapısı (blok döngüsü, bölüm ID'leri, panel filtreleri)
│   ├── urun.php          # Ürün sayfaları (Core, Shuttle): bölüm sırası ve alan çıktı yardımcıları
│   ├── rehber.php        # Rehber: block style/desenler, blok çıktı dönüşümleri, yazı yardımcıları
│   ├── cli*.php          # WP-CLI: rehber-aktar, faz1, faz2, faz3, faz4 içerik betikleri
│   ├── talep.php         # Talep formları: REST /talep, korumalı doküman dizini, süreli indirme bağlantısı
│   ├── arama.php         # Site içi arama (blok metinlerinin dizinlenmesi)
│   ├── formlar.php       # REST uç noktaları (uygulama özeti, bülten), geliştirme modu mail kaydı
│   ├── seo.php           # og:type, Article şeması, generator etiketi
│   ├── dil.php           # Polylang yardımcıları, EN adres ön ekleri
│   ├── walkers.php       # Menü walker'ları
│   └── helpers.php       # Yardımcı fonksiyonlar
├── template-parts/       # blocks/ (flexible content, bkz. docs/BLOKLAR.md), urun/, core/, shuttle/, rehber/, site/
├── page-templates/       # Özel sayfa şablonları
├── functions.php         # Sadece inc/ dosyalarını require eder
├── header.php, footer.php, front-page.php, page.php, home.php (rehber listesi),
│   single.php, single-uygulama.php, archive.php, index.php, 404.php, search.php
├── screenshot.png
└── style.css             # Tema başlığı (Theme Name, Text Domain vb.)
```

## Kod standartları

- **Fonksiyon öneki:** Tüm tema fonksiyonları `servo_` önekiyle başlar. Global namespace'i kirletme.
- **Text domain:** `servomotion`. Kullanıcıya görünen sabit metinler `__()`, `esc_html_e()` vb. ile çeviriye hazır olmalı.
- **Escape:** Her çıktı escape edilir: `esc_html()`, `esc_attr()`, `esc_url()`, zengin metin için `wp_kses_post()`.
- **Yollar:** Asset yolları için her zaman `get_template_directory_uri()` kullan, asla hardcoded URL yazma.
- **Görseller:** İçerik görselleri `wp_get_attachment_image()` ile basılsın (srcset ve lazy loading). Gerekirse `add_image_size()`.
- **Enqueue:** CSS/JS sadece `wp_enqueue_style` / `wp_enqueue_script` ile yüklenir, versiyon `filemtime()`. Şablonlara doğrudan `<link>` veya `<script>` yazma.
- **jQuery:** Tasarım jQuery kullanmıyorsa ekleme.
- **WordPress Coding Standards**'a uy (girinti tab, Yoda conditions zorunlu değil ama tutarlı ol).

## ACF kuralları

- Alan grupları arayüzden oluşturulur ve **Local JSON** ile `acf-json/` klasörüne kaydedilir.
- Genel site ayarları (logo, telefon, adres, sosyal medya, footer metinleri) **Options Page**'dedir. Options alanları şablonlarda doğrudan `get_field( 'x', 'option' )` ile değil, her zaman `servo_option()` helper'ıyla okunur.
- Alan isimleri `snake_case` ve anlamlı olsun (`hero_baslik`, `hero_gorsel`).
- Eklenti devre dışıyken site fatal error vermemeli: `get_field()` çağrılarını `function_exists()` ile sar veya helper kullan.
- Boş alanlar için markup basılmamalı (boş başlık etiketi, boş görsel vb.).
- Alan eklentisi ACF Pro değil, **Secure Custom Fields (SCF)**; API aynıdır.

## Repoya girmeyecekler

WordPress core dosyaları, `wp-config.php`, `wp-content/uploads`, `node_modules`, `.env`, işletim sistemi/editör dosyaları. `_design/` repoda tutulur ama canlıya deploy edilmez.

## Deploy edilmeyecekler

`_design/`, `servomotion-site-tum131026/`, `docs/` ve `bin/` repoda tutulur ama canlıya deploy edilmez. Canlı paket `bin/paket.sh` ile üretilir (`dist/servo-<sürüm>.zip`; `dist/` repoya girmez); paket bu klasörleri, `CLAUDE.md`, `README.md` ve git dosyalarını içermez.

## Mimari kararlar

1. **Çok dillilik:** Polylang. Sabit metinler çeviriye hazır; dile göre options ayrımı yalnızca `servo_option()` içindedir.
2. **CSS:** Tasarımın `<style>` blokları içerik değiştirilmeden `assets/css/` altında dosyalara taşınır. Ürün sayfaları ve rehber kendi dosyalarını yükler; flexible content kullanan tüm sayfalar ortak `bloklar.css`'i (`base.css` + `brandfont.css` ardından) yükler. `sozluk.js` ve iletişim aracı script'leri yalnızca ilgili blok sayfadaysa yüklenir.
3. **Header:** İki yapı (standart ve ürün sayfası); aynı WP menüsü iki ayrı walker ile basılır, walker'lar alt menüyü (`navdrop`) destekler.
4. **Inline SVG'ler:** `assets/img/svg/` altında dosya. ACF'de "görsel" alanı: yüklenirse fotoğraf, boşsa varsayılan SVG.
5. **Ürün sayfaları (Core, Shuttle):** Ürüne özel iki sayfa şablonu (CPT değil). Konfigüratör/simülasyon sabitleri (`CFG` dizileri) JS'te kalır; bunlara bağlı markup sabittir, panelden yalnızca bölüm başlığı/açıklaması değişir. Her ürünün alan grubu vardır (`group_servo_core`, `group_servo_shuttle`), her bölüm bir sekmedir; ortak bölümler aynı alan adlarıyla tanımlanır ve `inc/urun.php` yardımcılarıyla okunur. Lazer güvenliği bölümü Site Ayarları > Ürün güvenliği'nden tek yerden yönetilir.
6. **Bülten:** `servomotion/v1/bulten` uç noktası şimdilik 501 döner; servis bağlandığında yalnızca `servo_bulten_kayit()` (`inc/formlar.php`) değişir.
7. **Rehber:** Gutenberg (`inc/rehber.php`). Tasarım bileşenleri block style/desen olarak eklenir; `render_block` filtresi bunları tasarım markup'ına çevirir, `rehber.css` değişmez; `wptexturize` kapalı. İçindekiler H2'lerden üretilir (2'den azsa gösterilmez). Liste filtresi/araması tarayıcıda çalışır, bu yüzden liste ve arşivler tüm yazıları tek sayfada gösterir. Ayarlar Site Ayarları > Rehber'dedir. Tasarımdaki yazılar `wp servo rehber-aktar` ile aktarılır (kaynak dosya adıyla eşleşir, tekrar çalıştırılınca kopya oluşmaz).
8. **URL:** Rehber yazıları `/rehber/yazi-adi/`; bu yüzden tüm CPT ve taxonomy'ler `with_front => false` kaydedilir. Eski `*.html` adresleri `.htaccess` ile 301 yönlenir (`docs/YONLENDIRMELER.md`). Canlı kurulum: `docs/CANLI-KURULUM.md`. Müşteri kılavuzu: `docs/KULLANIM-KILAVUZU.md`.
9. **SEO:** Rank Math. Tema description, OG, canonical ve hreflang basmaz; Organization şemasını Rank Math verir. Tema yalnızca SSS sayfasında SSS CPT'sinden FAQPage JSON-LD üretir (Rank Math FAQ bloğu kullanılmaz). Ayarlar `wp servo faz3` ile kurulur. Host www'suzdur (`https://servomotion.com.tr`).
10. **Fontlar:** Barlow, Barlow Condensed, IBM Plex Mono ve Poppins `assets/fonts/` altında self-host; Google Fonts CDN kullanılmaz.
11. **Kapsam dışı:** `kurulum-notlari.html`, `KURULUM-NOTLARI.md`, `.htaccess`, `robots.txt`, `sitemap.xml` temaya dönüştürülmez. PDF, video, xlsx dosyaları Media Library'ye yüklenir.
12. **Formlar:** Uygulama özeti `servomotion/v1/uygulama-ozeti` ile `wp_mail`'e gider; endpoint tasarımın `window.SERVOMOTION_FORM_ENDPOINT` kancasına `wp_add_inline_script` ile verilir, tasarımın JS'i değişmez. Koruma: `wp_rest` nonce, honeypot, IP başına rate-limit (`servo_form_sinir`). Alıcı Site Ayarları > Formlar'dadır. **Geliştirme modu:** `WP_ENVIRONMENT_TYPE` `local`/`development` iken mailler gönderilmez, Araçlar > Mail kayıtları'na yazılır (`SERVO_MAIL_LOG` ile değiştirilebilir).
13. **CPT ve taxonomy'ler:** Rehber yazıları yerleşik `post` (kategori + `urun_alani`). `uygulama` (tekil sayfalı, `/uygulama/{slug}/`, arşivsiz), `vaka`, `dokuman`, `sss` (+ `sss_grup`), `terim`. `vaka`, `dokuman`, `sss`, `terim` tekil sayfasızdır ve aramada çıkmaz; `urun_alani` ve `sss_grup` arşiv üretmez. Vaka çapaları slug'dan üretilir (`servo_vaka_anchor()`), linkler `servo_vaka_url()` ile basılır.
14. **Flexible content:** Ana sayfa, `uygulama` ve `page.php` kullanan tüm sayfalar `bloklar` alanıyla kurulur; ayrı sayfa şablonları kullanılmaz. Core/Shuttle sabit şablon, rehber Gutenberg, 404/arama/rehber listesi sabit şablondur.
    - Her layout `template-parts/blocks/` altında bir dosyadır ve tasarımdaki bir `<section>`'a karşılık gelir; markup birebir korunur. Onaylı blok listesi: `docs/BLOKLAR.md`.
    - Ortak seçenekler: arka plan, bölüm başlığı (`.section-head` / `.sec-hd`). Çok görünümlü bloklarda conditional logic.
    - Stile bağlı bölüm ID'leri (ör. `#sistemler`) panelde düzenlenemez; serbest çapa alanı ayrıdır; aynı sayfada tekrar eden ID oluşmaz.
    - Her bloğa ve kafa karıştırabilecek alana kısa Türkçe yardım metni (instructions) eklenir.
15. **Marka adı işaretleme:** Panelde metinler düz yazılır; ürün sayfaları, bloklar ve rehberde bilinen ürün/marka adları çıktıda `servo_bdi()` ile `<bdi lang="en">` içine alınır. Terim listesi tek yerde: `servo_marka_adlari()` (`inc/helpers.php`). Tasarımdan görünmez `<bdi>` farkları kabul edilmiştir.
16. **Talep formları ve korumalı dokümanlar:** Dokümanlar ve servis alt sayfalarındaki formlar `talep_formu` bloğuyla kurulur ve `servomotion/v1/talep` uç noktasına gider (`inc/talep.php`); JS kapalıysa `mailto:` yedeği çalışır; stiller `servis.css` içinde `.talep-formu` altına kapsanır. "Talep formuyla" erişimli dosyalar `uploads/servo-korumali/` dizininde durur ve yalnızca imzalı, 7 gün geçerli `?servo-indir=` bağlantısıyla verilir. Site Ayarları > Formlar'daki gönderim ayarı: "otomatik gönder" (varsayılan) / "sadece ekibe bildir"; ekibe bildirim her zaman gider.

## İngilizce sürüm (Polylang)

- **Diller:** `tr` (varsayılan, ön eksiz) ve `en` (`/en/`). Çevrilebilir türler: sayfa, yazı, `uygulama`, `vaka`, `sss`, `terim`; taxonomy'ler: `urun_alani`, `sss_grup`. Ücretsiz Polylang.
- **Yardımcılar (`inc/dil.php`):** `servo_dil()`, `servo_en()`, `servo_kayit_bul( $slug, $tur, $dil )` (`get_page_by_path` yerine bu kullanılır), `servo_js_yolu()`.
- **Site Ayarları:** `options_tr` ve `options_en`; `servo_option()` EN boşsa TR'ye düşer (dile özgü metinler, ör. `servo_kvkk_notu()`, düşmez).
- **EN adres ön ekleri:** yalnızca `/en/` adreslerinde `rehber → guide`, `rehber/kategori → guide/category`, `uygulama → application`; TR adreslere dokunulmaz. Eşleme tek yerde: `servo_en_onekler()`; yeni ön ek eklenirse `wp rewrite flush` çalıştırılır. Site kökü `get_option( 'home' )` ile alınır.
- **Çeviri:** `languages/servomotion.pot` / `servomotion-en_GB.po/.mo`; yönetim paneli dizgeleri Türkçe kalır. Onay bekleyen çeviriler: `docs/EN-CEVIRI-LISTESI.md`.
- **İçerik betiği:** `wp servo faz4 [adım…] [--dry-run]` (`inc/cli-faz4.php`), tekrar çalıştırılınca kopya üretmez.
- **Formlar:** `servomotion/v1/talep` isteği `lang` alır; ziyaretçiye giden mesaj onun dilindedir, ekibe bildirim her zaman TR'dir.
- Core/Shuttle Precisor, iletişim, SSS, sözlük ve KVKK'nın EN sürümleri paketin EN sürümünde TR'den farklıdır; karar bekleyene kadar taslaktır.

## Kontrol listesi

- [ ] Tüm sayfalar referans tasarımla görsel olarak birebir aynı
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
