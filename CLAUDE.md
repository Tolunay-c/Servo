# CLAUDE.md

Bu repo, bir müşteri için hazırlanmış **statik HTML/CSS/JS tasarımın özel bir WordPress temasına dönüştürülmesi** projesidir. Repo kökü temanın kendisidir ve lokal ortamda `wp-content/themes/` altına clone'lanmış olarak çalışır.

## Temel kurallar

1. **`_design/` klasörüne asla dokunma.** Orijinal tasarım orada referans olarak durur. Okuyabilirsin, düzenleyemez, taşıyamaz, silemezsin.
2. **Tasarıma sadık kal.** HTML yapısı, class isimleri, ID'ler, animasyonlar ve responsive davranış birebir korunmalı. Tema çıktısı `_design/` içindeki sayfalarla görsel olarak aynı görünmeli.
3. **CSS ve JS'i yeniden yazma.** Tasarımın kendi dosyalarını `assets/` altına kopyala ve olduğu gibi kullan. Sadece yol (path) düzeltmeleri ve WordPress uyumu için gereken minimum değişiklikleri yap.
4. **Page builder kullanma** (Elementor, WPBakery vb.). Düzenlenebilir içerik ACF ile sağlanır.
5. Emin olmadığın bir yapısal kararda (yeni CPT, alan yapısı, sayfa şablonu) önce bana sor, sonra uygula.
6. **Asla commit veya push yapma; değişiklikleri sadece çalışma ağacında bırak.** Commit'leri proje sahibi atar.

## Klasör yapısı

```
/
├── _design/              # Orijinal tasarım (salt okunur, deploy edilmez)
├── docs/                 # Proje dokümanları (analiz, kılavuz, yönlendirmeler, canlı kurulum; deploy edilmez)
├── bin/                  # Geliştirme betikleri (paket.sh: canlı tema paketi; deploy edilmez)
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
│   ├── bloklar.php       # Flexible content altyapısı (blok döngüsü, bölüm ID'leri, panel filtreleri)
│   ├── urun.php          # Ürün sayfaları (Core, Shuttle): bölüm sırası ve alan çıktı yardımcıları
│   ├── rehber.php        # Rehber: block style/desenler, blok çıktı dönüşümleri, yazı yardımcıları
│   ├── cli.php           # WP-CLI komutları (wp servo rehber-aktar)
│   ├── arama.php         # Site içi arama (blok metinlerinin dizinlenmesi)
│   ├── formlar.php       # REST uç noktaları (uygulama özeti, bülten), geliştirme modu mail kaydı
│   ├── walkers.php       # Menü walker'ları
│   └── helpers.php       # Yardımcı fonksiyonlar
├── template-parts/       # Tekrar eden bölümler (hero, kart, CTA vb.)
│   ├── blocks/           # Flexible content blokları (bkz. docs/BLOKLAR.md)
│   ├── urun/             # Ürün sayfalarının ortak bölümleri (hero, teknik, güvenlik, geçiş, CTA)
│   ├── core/, shuttle/   # Ürüne özel bölümler
│   ├── rehber/           # Rehber listesi ve yazı kartı
│   ├── site/             # Header, footer, bülten, logo
├── page-templates/       # Özel sayfa şablonları
├── functions.php         # Sadece inc/ dosyalarını require eder
├── header.php
├── footer.php
├── front-page.php
├── page.php
├── home.php            # Rehber listesi (yazılar sayfası)
├── single.php
├── single-uygulama.php
├── archive.php
├── index.php
├── 404.php
├── search.php
├── screenshot.png
└── style.css             # Tema başlığı (Theme Name, Text Domain vb.)
```

## Dönüşüm iş akışı

Her adımı ayrı tamamla ve sonunda dur; commit'i proje sahibi atar (bkz. Temel kurallar 6).

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

`_design/`, `docs/` ve `bin/` repoda tutulur ama canlıya deploy edilmez. Canlı paket `bin/paket.sh` ile üretilir (`dist/servo-<sürüm>.zip`; `dist/` repoya girmez); paket bu klasörleri, `CLAUDE.md`, `README.md` ve git dosyalarını içermez.

## Proje kararları

Analiz (`docs/ANALIZ.md`) sonrası onaylanan kararlar:

1. **Çok dillilik:** Polylang kullanılacak. 1. fazda sadece TR yapılır, EN şablonları 2. fazda gelir. Mimari Polylang'e uygun kurulur:
   - Tüm sabit metinler çeviriye hazırdır.
   - Options alanları şablonlarda doğrudan `get_field( 'x', 'option' )` ile değil, her zaman `servo_option()` helper'ıyla okunur. 2. fazda dile göre ayrım (`options_tr` / `options_en`) sadece bu helper değiştirilerek eklenir.
2. **CSS:** Tasarımdaki `<style>` blokları içerik değiştirilmeden `assets/css/` altında dosyalara taşınır. Ürün sayfaları ve rehber kendi dosyalarını yükler; flexible content kullanan tüm sayfalar ortak `bloklar.css`'i yükler (bkz. madde 16).
3. **Header:** İki header yapısı (standart ve ürün sayfası) var. Aynı WP menüsü iki ayrı walker ile basılır. Walker'lar alt menüyü (tasarımdaki `navdrop` yapısı) destekler.
4. **Inline SVG'ler:** Temada `assets/img/svg/` altında dosya olarak tutulur. ACF'de "görsel" alanı açılır: görsel yüklenirse fotoğraf, boşsa varsayılan SVG basılır.
5. **Ürün sayfaları:** Konfigüratör ve simülasyondaki sabitler ve metinler (`CFG` dizileri vb.) JS'te kalır. Hero, KPI, metin bölümleri, teknik tablolar ve CTA ACF'ye bağlanır.
   - Her ürünün kendi alan grubu vardır (`group_servo_core`, `group_servo_shuttle`); her bölüm bir sekmedir. Ortak bölümler iki grupta aynı alan adlarıyla tanımlanır; şablon parçaları `inc/urun.php` yardımcılarıyla okur.
   - Simülasyon (Core), gövde şeması (Core) ve konfigüratör (Shuttle) bölümlerinin JS'e bağlı markup'ı sabittir; panelden sadece bölüm başlığı ve açıklaması değişir.
   - Panelde yardım metinleri ve conditional logic kullanılır: hero giriş biçimi (paragraf / madde listesi), hero görseli (fotoğraf / şema), video ve saha fotoğrafı bölümleri için "Bölümü göster".
   - Lazer güvenliği bölümü iki üründe aynıdır; içeriği Site Ayarları > Ürün güvenliği sekmesinden tek yerden yönetilir. Geçiş bandı ve iletişim bandı ürün başınadır.
6. **Bülten:** Ön yüz şimdilik mevcut davranışla ("e-posta ile yazın") kalır. `servomotion/v1/bulten` REST endpoint'i kayıtlıdır ve şimdilik "bağlı değil" yanıtı (501) döner; servis bağlandığında sadece `servo_bulten_kayit()` (`inc/formlar.php`) değişir.
7. **Rehber içeriği:** Gutenberg kullanılır (`inc/rehber.php`).
   - Tasarım bileşenleri çekirdek bloklara block style ve desen (editörde Modeller > ServoMotion rehber) olarak eklenir: Bilgi kutusu (grup + "Bilgi kutusu başlığı" paragrafı), Değer kutuları (tablo: açıklama | değer | birim), tablo, Numaralı adımlar / Numaralı liste (liste).
   - Ön yüzde `render_block` filtresi bu blokları tasarım markup'ına çevirir (`div.callout > div.t`, `div.keyval`, `div.ascroll > table.atable`, `ol.steps`, `ol.alist`); WordPress'in blok sınıfları kalkar, `<strong>` → `<b>`. `rehber.css` değişmez. Yazı metninde `wptexturize` kapalıdır (tırnaklar tasarımdaki gibi kalır).
   - İçindekiler metindeki H2'lerden üretilir; iki H2'den azsa gösterilmez ve metin tek sütundur (`.pbody.solo`). İlgili yazılar alandan seçilir, boşsa en yeni 3 yazı gelir.
   - Liste filtresi ve araması tarayıcıda çalışır (`rehber-liste.js`); bu yüzden liste ve arşivler tüm yazıları tek sayfada gösterir. Öne çıkan kart: sabitlenmiş yazı, yoksa en yeni yazı. Filtreler kategorilerden ve `urun_alani` terimlerinden üretilir.
   - Liste başlığı, yazı sonu kutusu, yazı altı notu ve varsayılan yazar Site Ayarları > Rehber sekmesindedir. Kart şemaları `assets/img/svg/rehber-*.svg`; yazıda öne çıkan görsel yüklenirse şemanın yerine o gösterilir.
   - Tasarımdaki yazılar `wp servo rehber-aktar [--kaynak=<dizin>] [--dry-run]` komutuyla aktarılır (`inc/cli.php`). Yazılar kaynak dosya adıyla (`_servo_kaynak`) eşleşir; komut tekrar çalıştırılınca kopya oluşmaz, yazılar güncellenir. Site Ayarları > Rehber'deki sadece boş alanlar doldurulur.
8. **URL ve yönlendirmeler:**
   - Rehber yazılarının adresi `/rehber/yazi-adi/` olur. Bu yapı Ayarlar > Kalıcı bağlantılar'da `/rehber/%postname%/`, kategori temeli `rehber/kategori` olarak ayarlanır (kategori adresi `/rehber/kategori/secim/`; tema bunu zorlamaz, bkz. `docs/CANLI-KURULUM.md`). Bu yüzden tüm CPT ve taxonomy'ler `with_front => false` ile kaydedilir.
   - Eski `*.html` adresleri `.htaccess` içinde 301 ile yönlendirilir. Eşleşme listesi ve kural bloğu: `docs/YONLENDIRMELER.md` (tasarımın `blog-*` kuralları dahil; EN adresleri 2. faza kadar 302). Canlı kurulum ayarları: `docs/CANLI-KURULUM.md`. Müşteri kılavuzu: `docs/KULLANIM-KILAVUZU.md`.
9. **SEO:** Rank Math kullanılır. Tema description, OG, canonical ve hreflang basmaz. Organization şemasını Rank Math verir. Tema sadece SSS sayfasında, SSS CPT'sinden FAQPage JSON-LD üretir (Rank Math FAQ bloğu kullanılmaz, çift şema olmaz).
10. **Fontlar:** Barlow, Barlow Condensed, IBM Plex Mono ve Poppins `assets/fonts/` altında self-host edilir. Google Fonts CDN kullanılmaz.
11. **Kapsam dışı:** `kurulum-notlari.html`, `KURULUM-NOTLARI.md`, `.htaccess`, `robots.txt`, `sitemap.xml` temaya dönüştürülmez. PDF, video ve xlsx dosyaları Media Library'ye yüklenir.
12. **Önek ve text domain:** Fonksiyon öneki `servo_`, text domain `servomotion`.
13. **Alan eklentisi:** ACF Pro yerine Secure Custom Fields (SCF) kullanılır. API aynıdır (repeater, options page, relationship dahil).
14. **Formlar:** Uygulama özeti aracı `servomotion/v1/uygulama-ozeti` uç noktasına gönderir ve özet `wp_mail` ile gider (`inc/formlar.php`). Endpoint adresi `wp_add_inline_script` ile tasarımın `window.SERVOMOTION_FORM_ENDPOINT` kancasına verilir, tasarımın JS'i değişmez.
    - Alıcı Site Ayarları > Formlar'dadır; boşsa İletişim e-postası kullanılır. İkisi de boşsa adres verilmez ve tasarımın e-posta/WhatsApp akışı çalışır.
    - Koruma: adresteki `wp_rest` nonce'u, ekran dışı honeypot alanı (`#f-web`; değeri adres nesnesinin `toString`'i ile gönderim anında sorguya eklenir) ve IP başına rate-limit (15 dakikada 5, `servo_form_sinir` filtresi). İlgili kişi alanı zorunludur; içinde e-posta varsa `Reply-To` olur.
    - **Geliştirme modu:** `WP_ENVIRONMENT_TYPE` `local` veya `development` iken tüm site mailleri gönderilmez, veritabanına kaydedilir (son 50) ve Araçlar > Mail kayıtları'nda görünür. Canlıda (`production`) kapalıdır; `SERVO_MAIL_LOG` sabitiyle elle açılıp kapatılabilir.
15. **CPT ve taxonomy'ler:**
    - Rehber yazıları yerleşik `post` tipindedir: kategori + `urun_alani` taxonomy'si.
    - `uygulama` (tekil sayfalı), `vaka`, `dokuman`, `sss` (+ `sss_grup` taxonomy'si), `terim` CPT'leri kullanılır.
    - `uygulama` tekil adresi `/uygulama/{slug}/` olur. `has_archive` false'tur, `/uygulama/` arşiv üretmez.
    - `urun_alani` ve `sss_grup` ön yüzde arşiv üretmez. `vaka`, `dokuman`, `sss` ve `terim` tekil sayfasızdır ve aramada çıkmaz.
    - Referanslar sayfasında her vakanın `id`'si post slug'ından üretilir (`servo_vaka_anchor()`). Uygulama sayfalarındaki vaka linkleri vakayı relationship alanından alır ve `servo_vaka_url()` ile basar. Böylece başlık değişse de çapa kırılmaz.
    - Ürünler CPT değil, 2 ayrı sayfa şablonudur.
    - Kurumsal ve servis listeleri flexible content blokları içinde repeater ile yapılır.
16. **Flexible content (sayfa blokları):** Müşterinin içerik değişiklikleri için geliştiriciye dönmemesi hedeflenir.
    - Ana sayfa, `uygulama` CPT'si ve `page.php` kullanan tüm sayfalar (referanslar, servis, kurumsal, iletişim, dokümanlar, SSS, sözlük, KVKK) SCF flexible content (`bloklar` alanı) ile kurulur. Ayrı sayfa şablonları (kurumsal, iletişim, referanslar, sözlük) kullanılmaz.
    - Core Precisor ve Shuttle Precisor sabit şablon kalır; içerikleri 7. adımda SCF alanlarına bağlanır. Rehber yazıları Gutenberg ile kalır. 404, arama ve rehber listesi sabit şablondur.
    - Her layout `template-parts/blocks/` altında bir dosyadır ve tasarımdaki bir `<section>`'a karşılık gelir; markup birebir korunur. Bloklar geneldir, birden fazla sayfada kullanılabilir. Birbirine çok benzeyen bölümler tek blokta "görünüm" seçeneğiyle birleştirilir (onaylı blok listesi ve sayfa eşleşmesi: `docs/BLOKLAR.md`).
    - Ortak blok seçenekleri: arka plan (varsayılan / beyaz / koyu), bölüm başlığı (standart `.section-head` / çizgili `.sec-hd`).
    - **CSS:** `home.css`'in `base.css` tekrarı dışındaki kuralları, `ic-sayfa.css`, `iletisim.css` ve `kurumsal.css` değerleri değiştirilmeden bu sırayla tek `bloklar.css` dosyasında birleştirilir ve tüm flexible sayfalarda (`base.css` + `brandfont.css` ardından) yüklenir. JS koşullu kalır: `sozluk.js` ve iletişim aracı script'leri sadece ilgili blok sayfadaysa yüklenir.
    - **Panel:** Çok görünümlü bloklarda SCF conditional logic kullanılır; her görünümde sadece ilgili alanlar görünür. Kapalı layout'ların başlığında bloğun başlık alanı gösterilir (`acf/fields/flexible_content/layout_title`).
    - **Bölüm ID'leri:** Stile bağlı ID'ler (ör. `#sistemler`) panelde düzenlenemez, blok varsayılanıyla sabit basılır. Serbest çapa alanı ayrıdır. Aynı sayfada tekrar eden ID oluşmaz; çakışmada sonraki bloğun ID'sine sıra eki verilir.
    - **Yardım metinleri:** Her bloğa ve kafa karıştırabilecek alanlara teknik bilgi gerektirmeyen kısa Türkçe yardım metni (instructions) eklenir.
    - Vaka çalışmaları sayfası Site Ayarları'ndan seçilir; `servo_vaka_url()` bu sayfayı kullanır.
17. **Ürün ve marka adlarının işaretlenmesi:** Panelde metinler `<bdi>` gibi etiketler olmadan düz yazılır. Ürün sayfalarında, sayfa bloklarında ve rehber yazılarında (liste, kart, yazı metni) bilinen ürün/marka adları çıktıda `servo_bdi()` ile otomatik olarak `<bdi lang="en">` içine alınır. SVG, script ve form seçeneklerinin içine dokunulmaz.
    - Terim listesi tek yerdedir: `servo_marka_adlari()` (`inc/helpers.php`, `servo_marka_adlari` filtresiyle genişletilebilir). Biçim tasarımı izler: "Precisor" ve "ProfiNet" `<span lang="en"><bdi lang="en">…</bdi></span>`, diğerleri `<bdi lang="en">…</bdi>`. "Core" ve "Shuttle" işaretin dışında kalır.
    - Tasarım aynı terimi her yerde aynı biçimde işaretlemediği için tasarımdan görünmez markup farkları vardır ve kabul edilmiştir: ürün sayfalarında 3, blok sayfalarında 32 fazladan / 2 eksik, rehberde 66 fazladan / 7 eksik `<bdi>`. Tasarım CSS'i `bdi[lang="en"]` stilini sıfırladığı için görünüm değişmez.

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