# _design/ Analiz Özeti: ServoMotion

> İş akışı 1. adım: analiz. Tarih: 2026-10-06

Bu, ServoMotion'ın (OEM Lazer) endüstriyel lazer konumlandırma ürünlerinin tanıtım sitesi. Site iki dilli: 29 TR sayfa ve 9 EN sayfa.

## 1. Sayfalar

| Sayfa | Ne işe yarıyor |
|---|---|
| `index.html` | Ana sayfa: hero, iki ürün kartı, kazanımlar, sabit referans ile servo farkı, karşılaştırma tablosu, uygulama alanları, hakkında ve CTA |
| `core-precisor.html` | Ürün sayfası (1322 satır). 6 konfigürasyonlu simülasyon, gövde boyu "merdiveni", teknik tablolar ve güvenlik bölümü içeriyor |
| `shuttle-precisor.html` | Ürün sayfası (992 satır). Mekik videosu, interaktif konfigüratör ve teknik tablolar içeriyor |
| `uygulama-lastik / -nonwoven / -ambalaj.html` | Aynı şablonla yapılmış 3 uygulama alanı sayfası |
| `referanslar.html` | 4 vaka çalışması, aynı sayfada `#lastik` gibi çapalarla açılıyor |
| `servis.html` | Servis ve devreye alma: 6 adım, bakım bölümü, 3 adımlı arıza süreci |
| `kurumsal.html` | Hakkımızda: zaman çizelgesi (6 adım) ve marka kartları |
| `iletisim.html` | "Uygulama özeti" aracı, 5 adımlı süreç ve künye |
| `dokumanlar.html` | İndirilebilir PDF'ler (2 veri sayfası, TÜV sertifikası) ve talep üzerine hazırlanan dokümanlar |
| `rehber.html` | Blog listesi. Arama, kategori ve "ürün alanı" filtresi var; 1 öne çıkan yazı ve 10 kart |
| `rehber-*.html` (11 adet) | Blog yazıları: içindekiler (scroll-spy), callout ve keyval kutuları, ilgili yazılar |
| `sss.html` | 6 grupta 18 soru (`<details>`) ve FAQPage JSON-LD |
| `sozluk.html` | 24 terim ve canlı arama |
| `kvkk.html` | Aydınlatma metni ve çerez politikası |
| `404.html` | Tasarımı var |
| `en/*` (9 sayfa) | index, core, shuttle, applications, service, company, contact, faq, glossary. **TR'nin birebir çevirisi değil**, daha sade ve farklı bir yapıda |
| `kurulum-notlari.html` | Müşteriye yazılmış iç kurulum notu, site sayfası değil |

Tasarımda arama sayfası yok. Bu yüzden `search.php`'yi tasarımın genel stiline göre üretmek gerekecek.

## 2. Ortak bölümler

- **Header (2 farklı yapı var):**
  - Standart sayfalarda `<header class="nav">` ve içinde düz `<nav>` + `<a>` listesi.
  - Ürün sayfalarında `<nav class="nav"><div class="nav-in">`, `.tabs/.tab` class'ları ve logonun altında "Lazer Konumlandırma" alt yazısı var. Hero da `<header class="hero">` içinde.
  - Menü öğeleri iki yapıda da aynı (8 öğe).
- **Bülten bandı** (`section.news #bulten`): bütün sayfalarda var. Rehber sayfalarında sadece mailto konusu farklı.
- **Footer:** 4 kolon (`.fcols`) ve alt satır (`.fbase`). Ürün sayfalarında aynı içerik `footer.foot` class'ıyla geliyor. EN footer'ı tek satırlık, farklı bir yapı.
- **CTA bantları:** ana sayfada `section.contact`, ürün sayfalarında `section.cta`, iç sayfalarda `section.cta-band`. Her birinin metni sayfaya özel.
- **İç sayfa hero'su** (`section.phero`): label + h1 (`<em>` ile sarı vurgu) + intro + butonlar + `.pfacts`. Çoğu iç sayfada aynı kalıp.
- **Logo:** SVG işaret ve "Servo**Motion**" yazısı (Poppins, lokal font). Her sayfada inline olarak duruyor.

## 3. Önerilen template-parts

- **Ortak:**
  - `header-standart`, `header-urun`
  - `footer-kolonlar`
  - `bulten`
  - `cta-band`
  - `page-hero` (phero)
  - `logo` (SVG)
- **Ana sayfa:**
  - `home/hero` (strip dahil)
  - `home/urunler`
  - `home/kazanimlar`
  - `home/kategori-farki`
  - `home/karsilastirma`: aynı tablo EN index'te de kullanılıyor
  - `home/uygulama-alanlari`
  - `home/hakkinda`
  - `home/iletisim-cta`
- **Ürün sayfaları:** `urun/rails`, `urun/teknik-tablo`, `urun/guvenlik`, `urun/cross` (öteki ürüne geçiş bandı). Konfigüratör ve simülasyon bölümleri her ürüne özel; şablonda sabit kalmalı.
- **Kartlar:** `kart/rehber-story`, `kart/rehber-featured`, `kart/vaka`, `kart/dokuman`, `kart/uygulama`.
- **Rehber yazısı:** `rehber/toc`, `rehber/ilgili-yazilar`.

## 4. Tekrar eden içerikler ve CPT önerisi

| İçerik | Öneri | Gerekçe |
|---|---|---|
| Rehber yazıları | **Yerleşik `post`**, kategori (verim/secim) ve yeni **`urun_alani` taxonomy'si** (core/shuttle/genel) | Filtreler bu iki eksene göre çalışıyor. Blog arşivi ve tekil sayfa doğal olarak oturur |
| Uygulama alanları | **CPT `uygulama`** (single ile) | 3 sayfa aynı şablonu kullanıyor. Ana sayfa kartları da buradan beslenir. Film/tekstil/etiket gibi "diğer" alanlar detay sayfası olmadan girilebilir |
| Vaka çalışmaları | **CPT `vaka`** (single yok, tek sayfada listeleniyor) | Uygulama sayfaları `referanslar#lastik` çapasına link veriyor |
| Dokümanlar | **CPT `dokuman`** (PDF dosya alanı) | Hem doküman sayfasında hem ürün sayfalarının güvenlik bölümünde kullanılıyor |
| SSS | **CPT `sss`** ve **`sss_grup` taxonomy'si** | 6 grup var. FAQPage JSON-LD otomatik üretilebilir |
| Sözlük | **CPT `terim`** | 24 terim ve "alternatif adlar" alanı var |
| Ürünler | CPT yerine **2 ayrı sayfa şablonu** | Sadece 2 ürün var ve iki sayfa birbirinden tamamen farklı |
| Kurumsal zaman çizelgesi, marka kartları, servis adımları, iletişim süreci | ACF **repeater** | Tek sayfaya ait, birkaç öğelik listeler |

## 5. ACF yapısı

**Options Page ("Site Ayarları"):**

- Telefonlar (sabit ve WhatsApp), faks, e-postalar (servomotion ve OEM)
- Adres ve harita linki
- Firma unvanı, vergi dairesi/no
- Sosyal medya (LinkedIn, Instagram), kardeş marka linkleri
- Footer alt metni
- Bülten başlığı ve metni
- Varsayılan CTA metni
- Logo alt yazısı

**Sayfa bazlı alan grupları (örnek alan adları):**

- **Ana sayfa:**
  - `hero_etiket`, `hero_baslik`, `hero_vurgu`, `hero_maddeler` (repeater), `hero_gorsel`, `hero_butonlar`
  - `strip_ogeleri` (repeater)
  - `urunler_baslik`, `kazanimlar` (repeater)
  - `kategori_*`, `kategori_kanitlar` (repeater)
  - `karsilastirma_satirlari` (repeater)
  - `hakkinda_*`, `cta_*`
- **Ürün sayfaları:** hero + `kpi` (repeater), `strip`, `rails` (repeater), `teknik_tablolar` (repeater içinde repeater), `guvenlik_*`, `cta_*`.
- **Uygulama (CPT):** `hero_*`, `pfacts` (repeater), `belirtiler`, `parametre_tablosu`, `ornek_kurulum`, `ilgili_linkler`, `kart_ozet`, `kart_belirleyici`.
- **Vaka:** `etiket`, `lead`, `sistem`, `konfigurasyon`, `strok`, `hassasiyet`, `baslangic_durumu`, `kurulan_yapi`, `foto`.
- **Doküman:** `dosya`, `meta_satiri`, `tur` (veri sayfası / sertifika / talep üzerine).
- **Rehber yazısı:** `okuma_suresi`, `intro`, `keyval` (repeater), `kart_gorseli`, `ilgili_yazilar` (relationship). İçindekiler, içerikteki h2'lerden otomatik üretilir.
- **Servis, Kurumsal, İletişim, Dokümanlar, KVKK, 404:** hero, bölüm başlıkları ve kendi repeater'ları.

## 6. Harici bağımlılıklar

- **JS kütüphanesi hiç yok.** jQuery, Swiper, GSAP vb. kullanılmamış; hepsi vanilla JS. Dolayısıyla jQuery eklenmeyecek.
- **Fontlar:**
  - Google Fonts CDN'den Barlow (400–700), Barlow Condensed (500–700) ve IBM Plex Mono (400/500).
  - Poppins Regular ve Bold yerel olarak `fonts/` altında.
- **CSS:**
  - Dosya olarak `assets/base.css`, `brandfont.css` ve `rehber.css` var (rehber.css, base.css'i de içinde barındırıyor).
  - Stillerin büyük kısmı ise **sayfa içi `<style>` bloklarında**: ana sayfa 27k, core 35k, shuttle 23k, iç sayfaların ortak bloğu 5,4k, iletişim ve kurumsal kendi blokları, EN ortak bloğu.
- **Sayfa JS'leri:**
  - core simülasyonu (~20k) ve gövde merdiveni
  - shuttle konfigüratörü (~15k)
  - iletişim özet aracı (`?konu=` parametresini okuyor)
  - rehber filtresi, sözlük araması, içindekiler scroll-spy
  - bülten, dropdown menü
- **Medya:** 4,8 MB'lık mp4 video ve poster, 6 PDF, 1 xlsx, 9 yer tutucu fotoğraf, OG görselleri.

## 7. Formlar

1. **Uygulama özeti (iletişim, TR/EN):** şu an mailto, WhatsApp ve "kopyala" ile çalışıyor. Tasarımın JS'inde zaten bir `window.SERVOMOTION_FORM_ENDPOINT` kancası var; adres doluysa özeti JSON olarak POST ediyor.
   - **Öneri:** `register_rest_route` ile özel bir endpoint yazıp `wp_mail` ile göndermek. Nonce, honeypot ve rate-limit eklenir.
   - Endpoint adresi `wp_add_inline_script` ile verilirse **tasarımın JS'ine hiç dokunmadan** çalışır. JSON gövdesi `admin-post.php`'ye uymadığı için REST daha temiz.
   - CF7 burada uygun değil, çünkü bu bir form değil, interaktif bir araç.
2. **Bülten formu:** şu an hiçbir listeye bağlı değil, sadece "e-posta ile yazın" mesajı gösteriyor. Hangi listeye bağlanacağı karar bekliyor (aşağıda 6. madde).

## 8. Karar gerektiren noktalar

1. **EN sürüm:** EN, 9 sayfalık ve TR'den farklı yapıda bir site. Üç seçenek var:
   - **(a)** Polylang ile çok dilli yapı. EN sayfaları ayrı şablon ve alanlar ister.
   - **(b)** EN'yi ayrı sayfa şablonlarıyla `/en/` altına kurmak (eklentisiz).
   - **(c)** EN'yi şimdilik kapsam dışı bırakmak.

   Öneri: (a).
2. **Gömülü CSS:** "CSS'i yeniden yazma" kuralına uyarak `<style>` blokları içerik değiştirilmeden dosyalara taşınır: `home.css`, `core.css`, `shuttle.css`, `ic-sayfa.css`, `iletisim.css`, `kurumsal.css`, `en.css`. Her biri ilgili şablonda koşullu enqueue edilir.
3. **İki header yapısı:** aynı WP menüsü iki ayrı walker ile basılır: standart `<a>` ve ürün sayfalarında `.tab`. Dropdown (`navdrop`) için CSS ve JS hazır ama hiçbir sayfada kullanılmamış. Alt menü gerekirse walker bunu destekleyebilir.
4. **Inline SVG illüstrasyonlar:** rehber kartları, uygulama görselleri, Shuttle şeması ve ikonlar inline SVG. Öneri: SVG'ler temada `assets/img/svg/` altında tutulur, ACF'de "görsel" alanı açılır. Görsel yüklenirse fotoğraf gösterilir, boşsa varsayılan SVG.
5. **Ürün sayfalarında panelden düzenleme ne kadar derin olsun?** Konfigüratör ve simülasyondaki sabitler ve açıklama metinleri JS dizilerinin içinde (`CFG`). Öneri: bunlar JS'te kalsın; hero, KPI, metin bölümleri, teknik tablolar ve CTA ACF'ye bağlansın.
6. **Bülten:** üç seçenek var:
   - Mailchimp/Brevo entegrasyonu
   - Kendi kayıt tablomuz ve admin listesi (KVKK onayıyla)
   - Mevcut "e-posta ile yazın" davranışı
7. **Rehber yazı içeriği:** öneri Gutenberg kullanmak ve `.callout`, `.keyval`, `.atable` için block pattern/stil tanımlamak. Ayrıca 11 yazının ilk içeriği import mu edilsin, elle mi girilsin?
8. **URL'ler ve yönlendirmeler:** eski `*.html` adreslerinden yeni permalink'lere 301 gerekecek (`.htaccess`'te ayrıca `blog-*` kuralları da var). Rehber için taban `/rehber/yazi-adi/` olsun mu? Yönlendirmeler sunucuda mı, Redirection eklentisiyle mi yapılsın?
9. **SEO:** tasarımda her sayfaya elle yazılmış description, OG, canonical ve hreflang etiketleri var. Öneri: bunları Yoast veya Rank Math yönetsin, tema basmasın. Organization ve FAQPage JSON-LD'yi ise tema Options ve SSS CPT'sinden üretsin. Hangi SEO eklentisi kullanılacak?
10. **Google Fonts:** kurulum notu da KVKK ve hız için önerdiği için Barlow ve IBM Plex Mono `assets/fonts/` altına self-host edilsin.
11. **Kapsam dışı önerilenler:** `kurulum-notlari.html`, `KURULUM-NOTLARI.md`, `.htaccess`, `robots.txt`, `sitemap.xml`. Sitemap'i WP veya SEO eklentisi üretir. PDF, video ve xlsx dosyaları temaya değil Media Library'ye yüklenmeli.
12. **Fonksiyon öneki ve text domain:** `tema_` yerine `servo_`, text domain olarak `servomotion` önerilir.
13. **Ek eklenti:** ACF Pro mu, ücretsiz ACF mi? Options Page ve repeater için Pro gerekiyor.
