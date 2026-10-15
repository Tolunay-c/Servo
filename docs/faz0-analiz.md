# Canlı site denetimi: FAZ 0 analizi

Kaynak: `servomotion-canli-denetim.html` (tema kökünde; 12 majör, 10 minör, 8 doğrulanacak madde). Karşılaştırma: `_design/` (revizyon öncesi) ile `servomotion-site-tum131026/` (son teslim paketi, yeni referans). `Revizyonlar.docx` yoktu; analiz rapor ve iki klasör arasındaki farklarla yapıldı. Dosya gelirse farklar onunla teyit edilecek.

Kısaltmalar: **ET** eski tasarım, **AH** aktarım hatamız, **CO** canlı ortam ayarı.

## Kök neden

`_design/` revizyon öncesi sürüm; canlı site buna karşılık geliyor. Raporun çoğu maddesi tek tek hata değil, son sürümün aktarılmamasının sonucu. Pakette raporun anmadığı değişiklikler de var (aşağıda).

## Raporda olmayan ama paketle değişen farklar

1. **CSS ve varlıklar:** `base.css` (menü kuralı), `brandfont.css` (menü logosu animasyonu), `rehber.css` (`.steps` yerleşimi, `.editorial-note`), `index` (`.applead` 3 → 2 sütun), her sayfada `navfit` (menü sığdırma), yeni `sm-servis` ve `sm-cases` blokları. `sm-news` içerik olarak aynı, sadece iletişim sayfasına taşınmış. `sm.svg` ve `core-precisor.jpg` yalnızca meta veri bakımından değişmiş; görsel olarak aynı, işlem gerekmez.
2. **Logo:** SVG işareti `g.smk-m` ile sarılmış ve parlama çubuğu (`rect.smk-b`) eklenmiş.
3. **Mühendislik değerlendirmesi kutusu:** 11 rehber yazısında yeni `div.editorial-note.engineering` (üst etiket, başlık, 3 madde); içindekilere girmez.
4. **Dört yeni form:** Dokümanlar talep formu ve üç servis alt formu (destek, iade, kılavuz) pakette `mailto:` ile çalışıyor.
5. **Metinler:** Rehber listesi başlığı ve girişi, rehber CTA bandı, SSS ve sözlük başlıkları, KVKK metni, servis sayfası (baştan), dokümanlar sayfası, ana sayfada altı uygulama kartı (üçünde örnek kurulum), meta açıklamalarındaki ASCII Türkçe düzeltmeleri.
6. **Kaldırılanlar:** Shuttle sayfasındaki saha fotoğrafı, kurumsal üretim/saha görselleri, ağaç vakası, rehber yazılarındaki imza.
7. **Host:** Pakette canonical, hreflang ve sitemap `www.servomotion.com.tr/....html`. WordPress'te temiz adres ve www'suz host kullanılacak (karar 4).
8. **Sayılar:** `og/` altında 18 görsel var (raporda 10). Türkçe tarafta 33 kapsam içi sayfa (34 dosyadan `kurulum-notlari.html` kapsam dışı), `en/` altında 34 dosya.

## Raporun 30 maddesi

| Madde | Kaynak | Çözüm | Etkilenenler |
|---|---|---|---|
| M01 EN yok | ET | FAZ 4 (Polylang) | `en/` 34 sayfa, menü, `servo_option()` |
| M02 örnek sayfa | CO | Sayfa ID 2 ve taslak "Gizlilik Politikası" (ID 3) kalıcı silinir | İçerik, WP-CLI betiği |
| M03 Başarı Hikayeleri | ET | Sayfa 8 `basari-hikayeleri` olur, 301 `/referanslar/` → yeni adres, menü ve footer etiketi | Sayfa 8, menüler, `YONLENDIRMELER.md`, `.htaccess` |
| M04 ağaç vakası | ET | Vaka 64 ve görsel 77 silinir; sayfa 6 kurulumla yeniden kurulur (iki sütun) | `vaka` CPT, sayfa 8, 12 meta kaydı |
| M05 3 uygulama yok | ET + AH | Uygulama 55–57 yalnız kart olarak var (`detay_sayfasi=0`, içerik boş, başlıklarda bozuk karakter, slug `film`). Bloklarla doldurulur, slug `film-folyo`, ana sayfa kartlarına bağlantı | Uygulama 55–57, ana sayfa blokları, footer |
| M06 servis alt sayfaları | ET | 3 yeni sayfa + yeniden yazılmış servis sayfası; `servis_kanallari` ve `talep_formu` blokları | Yeni sayfalar, servis sayfası, `servis.css` |
| M07 doküman indirme | ET (kapsam rapordan dar) | Pakette yalnız veri sayfaları ve sertifika formla isteniyor; kurulum ve uygulama notları açık kalıyor | `dokuman` CPT, `formlar.php`, 3 PDF |
| M08 uygulama notu PDF'leri | ET | 12 PDF yüklenir, buton kendi notuna bağlanır (M07 ile çelişmez, açık kalıyorlar) | Uygulama 17–19, 55–57, Medya |
| M09 imza | ET (+ bizim varsayılanımız) | `options_rehber_yazar` varsayılanı ve `inc/rehber.php` yazar alanı üst bilgiden çıkar | `inc/rehber.php`, `single.php`, 11 yazı |
| M10 yazı tarihleri | ET | 11 yazı 23 Eylül 2026 olur, şema aynı tarihi kullanır | 11 yazı, Rank Math şeması |
| M11 kurumsal görseller | ET | İki görsel bloğu kaldırılır; grup markaları alan adı gösterir | Sayfa 10 |
| M12 www/www'suz | CO | `.htaccess` 301, WordPress adresi www'suz | `.htaccess`, `YONLENDIRMELER.md` |
| m01 footer etiketi | ET | Menü öğesi 38: "Sahadan altı uygulama" (karar 1) | Footer menüsü |
| m02 Shuttle başlığı | CO | Sayfa bazlı Rank Math başlığı/açıklaması paketten verilir | Rank Math, bkz. m08 |
| m03 ProfiNet | ET | PROFINET olarak tekleştirilir | İçerik (15 meta, 4 yazı), `helpers.php` |
| m04 boş tablo hücresi | **Yanlış teşhis** | "Ortak altyapı" satırı `colspan=2`; paket ve yerel aynı | Yok |
| m05 arama kutusu | **Kısmen yanlış** | Rehber araması yerelde var; SSS'te ne `_design/` ne paket arama içeriyor (karar 5: eklenmeyecek) | Yok |
| m06 bülten e-postası | **Şüpheli** | Yerelde alan var; FAZ 5'te Playwright ile bakılır | Yok / canlıda kontrol |
| m07 PDF düğmeleri | ET | Paket değiştirmemiş; M07 ile doğru hâle gelir | Ürün sayfası düğme metni |
| m08 lang/OG | CO + AH | `html lang` yerelde var; OG yok çünkü Rank Math sihirbazı çalıştırılmamış. OG görselleri yüklenir, Rank Math alanları betikle doldurulur | Rank Math, Medya |
| m09 generator | CO + AH | `wp_head`'den kaldırılır; iki adımlı doğrulama eklenti ayarı (canlı kurulum notu) | `inc/setup.php`, `CANLI-KURULUM.md` |
| m10 çapalar | AH | FAZ 3'te denetlenir | Bloklar |
| D01–D03 JS bileşenleri | **Büyük olasılıkla yanlış teşhis** | Paketteki script'ler `_design/` ile aynı, JS ile çiziliyor; FAZ 5'te test | Yok |
| D04–D07 | Aynı | Rehber filtre, SSS akordeon, iletişim aracı, video: FAZ 5'te test | Yok |
| D08 menü | AH | `navfit` ve EN düğmesiyle yeniden test | `navfit.css`, `brandfont.css` |

## Yeni yapılar (onaylı)

- `servis_kanallari` bloğu (kart ızgarası), `talep_formu` bloğu (doküman, destek, iade, kılavuz), `vaka_listesi` bloğuna "iki sütun" görünümü.
- `dokuman` CPT'sine erişim alanı (açık / formla); formla erişilenler korumalı dizinde, doğrudan erişime ve arama motorlarına kapalı.
- Ortak REST uç noktası `servomotion/v1/talep`.
- Gutenberg: "Mühendislik değerlendirmesi" block style'ı ve deseni.
- Rank Math sayfa bazlı meta için WP-CLI betiği. Yeni CPT yok.

## Kararlar

1. Footer: "Sahadan altı uygulama".
2. Doküman talebi: kullanıcıya PDF eki değil, 7 gün geçerli indirme bağlantısı e-postayla gider; ekibe talep bildirimi düşer. Site Ayarları > Formlar'da "otomatik gönder / sadece ekibe bildir" seçeneği olur, varsayılan otomatik gönder.
3. Servis formları `talep_formu` bloğuyla REST'e taşınır (`servomotion/v1/talep`).
4. www'suz host; sitemap, canonical ve hreflang www'suz.
5. SSS'e arama eklenmez.
6. FAZ 1'in başına FAZ 1a eklenir (varlık ve CSS güncellemesi + Mühendislik değerlendirmesi block style ve deseni).

## Fazlar

FAZ 1a (varlık/CSS), FAZ 1 (içerik ve sayfa eksikleri), FAZ 2 (dokümanlar ve formlar), FAZ 3 (teknik ve SEO), FAZ 4 (EN), FAZ 5 (doğrulama), FAZ 6 (canlı paket). Ayrıntı: `denetim-gorevi.md`.

## FAZ 1 betiği ve notlar

- İçerik değişiklikleri `wp servo faz1 [<adim>...] [--dry-run]` ile yapılır (`inc/cli-faz1.php`); her adım tekrar çalıştırılabilir, kopya oluşturmaz. Adımlar: temizlik, profinet, svg, rehber, kurumsal, shuttle, vakalar, uygulamalar, basari, servis, anasayfa, metinler, menuler, arama.
- **FAZ 6 için:** WordPress kökündeki (`htdocs/servo/`) `servomotion-site-tum131026/` kopyası canlı pakete (`servo-canli.zip`) **kesinlikle dahil edilmez**; zip'lemeden önce hariç tutulur (kopya silinmiş olsa bile hariç tutma kuralı korunur). Tema içindeki kopya da pakete girmez (`bin/paket.sh`).
- Kılavuzlar sayfasında paket, EMC uygunluk sertifikasını doğrudan indirilebilir olarak listeliyor; dokümanlar sayfası ise aynı dosyayı formla istenen doküman olarak sayıyor. Çelişkiyi M07 kararına göre çözmek için kılavuzlar listesinde sertifika "TALEP" rozetiyle dokümanlar sayfasındaki talep formuna bağlandı.
- `/referanslar/` → `/basari-hikayeleri/` yönlendirmesi FAZ 3'te `.htaccess` ve `docs/YONLENDIRMELER.md` ile eklenecek (WordPress sayfa slug değişikliğini kendiliğinden yönlendirmiyor).

## FAZ 2 notları

- `wp servo faz2 [<adim>...] [--dry-run]` (`inc/cli-faz2.php`): koruma, notlar, dokumanlar, sayfalar, duymeler, arama. Her adım tekrar çalıştırılabilir.
- Yedekler repo dışında `~/servo-yedekler/` altındadır (her faz öncesi tarihli alınır).
- **KVKK metni:** KVKK sayfası "sitede sunucuya veri gönderen bir form bulunmuyor" diyor; uygulama özeti, doküman talebi ve servis formları artık sunucuya gönderiyor. Metin hukuki gözden geçirme gerektirir; paket bu paragrafı değiştirmediği için tema dokunmadı.
- İngilizce veri sayfaları ve sertifika dışı doküman PDF'leri (kurulum notları vb. EN) FAZ 4'te İngilizce sayfalarla bağlanır; EN uygulama notları (6 PDF) Medya'dadır.

## FAZ 3 notları

- `wp servo faz3 [<adim>...] [--dry-run]` (`inc/cli-faz3.php`): og, rankmath, seo, tarihler. Yerelde Rank Math'in ön yüzü, hesap bağlantısı atlanarak (`rank_math_registration_skip`) açıldı; OG, Twitter, canonical, açıklama ve şema çıktısı yerelde doğrulandı.
- **FAZ 6 için:** `readme.html`, `license.txt` ve `wp-config-sample.php` WordPress sürümünü ele verir; canlı zip'e dahil edilmez.
- Yerelde `robots.txt` çalışmaz (WordPress bu kuralı yalnızca kökte kurulu sitede ekler); canlıda kontrol edilir.
- m05 (rehber araması çalışıyor, SSS'e eklenmeyecek), m06 (bülten alanı yerelde görünüyor ve çalışıyor) ve m10 (tüm çapalar yerelde var) yerelde doğrulandı; canlıda yeniden bakılır.


---

# CLAUDE.md'den taşınan ayrıntılar

Aşağıdakiler CLAUDE.md sadeleştirilirken oraya taşınan, tamamlanmış fazlara ait ayrıntılar, uygulama notları ve karar gerekçeleridir. Kalıcı kurallar ve mimari kararlar CLAUDE.md'de kalır.

## Dönüşüm iş akışı (tamamlandı)

Her adım ayrı tamamlanır ve sonunda durulur; commit'i proje sahibi atar.

1. **Analiz:** `_design/` içindeki tüm sayfalar incelendi; ortak bölümler (header, footer, CTA), sayfaya özel bölümler ve tekrar eden yapılar çıkarıldı, özet sunuldu ve onay alındı.
2. **İskelet:** `style.css`, `functions.php`, `inc/` dosyaları ve boş şablonlar.
3. **Asset'ler:** CSS, JS, görsel ve fontlar `assets/` altına kopyalandı, `inc/enqueue.php` ile yüklendi.
4. **Header / Footer:** `header.php` ve `footer.php`; `wp_head()`, `wp_body_open()`, `wp_footer()`; menüler `wp_nav_menu` ile dinamik, tasarım markup'ı için custom walker.
5. **Ana sayfa:** `front-page.php`, her bölüm `template-parts/` altında.
6. **Diğer sayfalar:** her tasarım sayfası için uygun şablon.
7. **Dinamik içerik:** sabit metin ve görseller ACF alanlarına, tekrar eden içerikler CPT'ye bağlandı.
8. **Blog / arşiv / tekil:** tasarımda yoksa tasarımın genel stiline uygun sade sürümler.
9. **Formlar:** Contact Form 7 yerine `admin-post.php`/REST ile özel handler (karar proje sahibine soruldu).
10. **Kontrol:** kontrol listesi uygulandı.

## Proje kararlarının ayrıntıları

### 1. Çok dillilik
Polylang kullanıldı. 1. fazda yalnızca TR, EN 2. fazda. Mimari baştan Polylang'e uygun: sabit metinler çeviriye hazır; options alanları şablonlarda doğrudan `get_field( 'x', 'option' )` ile değil `servo_option()` ile okunur. 2. fazda dile göre ayrım (`options_tr` / `options_en`) yalnızca bu helper değiştirilerek eklendi.

### 2. CSS
Tasarımdaki `<style>` blokları içerik değiştirilmeden `assets/css/` altında dosyalara taşındı. Ürün sayfaları ve rehber kendi dosyalarını yükler; flexible content kullanan sayfalar ortak `bloklar.css`'i yükler.

### 3. Header
İki header yapısı (standart ve ürün sayfası). Aynı WP menüsü iki ayrı walker ile basılır; walker'lar alt menüyü (tasarımdaki `navdrop`) destekler.

### 4. Inline SVG'ler
`assets/img/svg/` altında dosya olarak tutulur. ACF'de "görsel" alanı açılır: yüklenirse fotoğraf, boşsa varsayılan SVG basılır.

### 5. Ürün sayfaları
Konfigüratör ve simülasyondaki sabitler ve metinler (`CFG` dizileri vb.) JS'te kalır. Hero, KPI, metin bölümleri, teknik tablolar ve CTA ACF'ye bağlanır.
- Her ürünün kendi alan grubu vardır (`group_servo_core`, `group_servo_shuttle`); her bölüm bir sekmedir. Ortak bölümler iki grupta aynı alan adlarıyla tanımlanır; şablon parçaları `inc/urun.php` yardımcılarıyla okur.
- Simülasyon (Core), gövde şeması (Core) ve konfigüratör (Shuttle) bölümlerinin JS'e bağlı markup'ı sabittir; panelden yalnızca bölüm başlığı ve açıklaması değişir.
- Panelde yardım metinleri ve conditional logic kullanılır: hero giriş biçimi (paragraf / madde listesi), hero görseli (fotoğraf / şema), video ve saha fotoğrafı bölümleri için "Bölümü göster".
- Lazer güvenliği bölümü iki üründe aynıdır; içeriği Site Ayarları > Ürün güvenliği sekmesinden yönetilir. Geçiş bandı ve iletişim bandı ürün başınadır.

### 6. Bülten
Ön yüz mevcut davranışla ("e-posta ile yazın") kalır. `servomotion/v1/bulten` REST endpoint'i kayıtlıdır ve şimdilik "bağlı değil" yanıtı (501) döner; servis bağlandığında yalnızca `servo_bulten_kayit()` (`inc/formlar.php`) değişir.

### 7. Rehber içeriği
- Tasarım bileşenleri çekirdek bloklara block style ve desen (editörde Modeller > ServoMotion rehber) olarak eklendi: Bilgi kutusu (grup + "Bilgi kutusu başlığı" paragrafı), Değer kutuları (tablo: açıklama | değer | birim), tablo, Numaralı adımlar / Numaralı liste (liste).
- Ön yüzde `render_block` filtresi bunları tasarım markup'ına çevirir (`div.callout > div.t`, `div.keyval`, `div.ascroll > table.atable`, `ol.steps`, `ol.alist`); WordPress blok sınıfları kalkar, `<strong>` → `<b>`. `rehber.css` değişmez. Yazı metninde `wptexturize` kapalıdır.
- İçindekiler metindeki H2'lerden üretilir; iki H2'den azsa gösterilmez ve metin tek sütundur (`.pbody.solo`). İlgili yazılar alandan seçilir, boşsa en yeni 3 yazı gelir.
- Liste filtresi ve araması tarayıcıda çalışır (`rehber-liste.js`); bu yüzden liste ve arşivler tüm yazıları tek sayfada gösterir. Öne çıkan kart: sabitlenmiş yazı, yoksa en yeni yazı. Filtreler kategorilerden ve `urun_alani` terimlerinden üretilir.
- Liste başlığı, yazı sonu kutusu, yazı altı notu ve varsayılan yazar Site Ayarları > Rehber sekmesindedir. Kart şemaları `assets/img/svg/rehber-*.svg`; öne çıkan görsel yüklenirse şemanın yerine o gösterilir.
- Tasarımdaki yazılar `wp servo rehber-aktar [--kaynak=<dizin>] [--dry-run]` ile aktarıldı (`inc/cli.php`). Yazılar kaynak dosya adıyla (`_servo_kaynak`) eşleşir; tekrar çalıştırınca kopya oluşmaz, yazılar güncellenir. Site Ayarları > Rehber'deki yalnızca boş alanlar doldurulur.

### 8. URL ve yönlendirmeler
Rehber yazıları `/rehber/yazi-adi/`; Ayarlar > Kalıcı bağlantılar'da `/rehber/%postname%/`, kategori temeli `rehber/kategori` (tema zorlamaz, bkz. `docs/CANLI-KURULUM.md`). Bu yüzden tüm CPT ve taxonomy'ler `with_front => false` ile kayıtlıdır. Eski `*.html` adresleri `.htaccess` içinde 301 ile yönlendirilir (liste ve kural bloğu `docs/YONLENDIRMELER.md`; tasarımın `blog-*` kuralları dahil; EN adresleri 2. faza kadar 302).

### 9. SEO
- Rank Math ayarları, sayfa bazlı başlık/açıklama ve paylaşım görselleri (`og/` → Medya, 1200×630) `wp servo faz3` ile kuruldu (`inc/cli-faz3.php`). Rank Math'in ön yüzü hesap bağlantısı olmadan çalışsın diye kayıt "atla" olarak işaretlendi.
- `inc/seo.php`: sayfaların `og:type` değeri `website`, rehber yazılarınınki `article`; rehber yazılarının Article şemasında yazar olarak kurum yer alır. Tema WordPress `generator` etiketini basmaz.
- Host www'suzdur (`https://servomotion.com.tr`); canonical, site haritası ve hreflang buna göre üretilir.

### 11. Kapsam dışı
`kurulum-notlari.html`, `KURULUM-NOTLARI.md`, `.htaccess`, `robots.txt`, `sitemap.xml` temaya dönüştürülmedi. PDF, video ve xlsx dosyaları Media Library'ye yüklenir.

### 14. Formlar
Uygulama özeti aracı `servomotion/v1/uygulama-ozeti` uç noktasına gönderir, özet `wp_mail` ile gider (`inc/formlar.php`). Endpoint adresi `wp_add_inline_script` ile tasarımın `window.SERVOMOTION_FORM_ENDPOINT` kancasına verilir; tasarımın JS'i değişmez.
- Alıcı Site Ayarları > Formlar'dadır; boşsa İletişim e-postası kullanılır. İkisi de boşsa adres verilmez ve tasarımın e-posta/WhatsApp akışı çalışır.
- Koruma: `wp_rest` nonce'u, ekran dışı honeypot alanı (`#f-web`; değeri adres nesnesinin `toString`'i ile gönderim anında sorguya eklenir), IP başına rate-limit (15 dakikada 5, `servo_form_sinir` filtresi). İlgili kişi alanı zorunludur; içinde e-posta varsa `Reply-To` olur.
- **Geliştirme modu:** `WP_ENVIRONMENT_TYPE` `local` veya `development` iken tüm site mailleri gönderilmez, veritabanına kaydedilir (son 50) ve Araçlar > Mail kayıtları'nda görünür. Canlıda kapalıdır; `SERVO_MAIL_LOG` sabitiyle elle değiştirilebilir.

### 15. CPT ve taxonomy'ler
- `uygulama` tekil adresi `/uygulama/{slug}/`; `has_archive` false. `urun_alani` ve `sss_grup` ön yüzde arşiv üretmez. `vaka`, `dokuman`, `sss` ve `terim` tekil sayfasızdır ve aramada çıkmaz.
- Referanslar sayfasında her vakanın `id`'si post slug'ından üretilir (`servo_vaka_anchor()`). Uygulama sayfalarındaki vaka linkleri vakayı relationship alanından alır ve `servo_vaka_url()` ile basar; başlık değişse de çapa kırılmaz.
- Ürünler CPT değil, 2 ayrı sayfa şablonudur. Kurumsal ve servis listeleri flexible content blokları içinde repeater ile yapılır.

### 16. Flexible content
- Ana sayfa, `uygulama` CPT'si ve `page.php` kullanan tüm sayfalar (referanslar, servis, kurumsal, iletişim, dokümanlar, SSS, sözlük, KVKK) `bloklar` alanıyla kurulur; ayrı sayfa şablonları (kurumsal, iletişim, referanslar, sözlük) kullanılmaz. Core/Shuttle Precisor sabit şablon kalır (içerik SCF alanlarında); rehber yazıları Gutenberg; 404, arama ve rehber listesi sabit şablondur.
- Her layout `template-parts/blocks/` altında bir dosyadır ve tasarımdaki bir `<section>`'a karşılık gelir; markup birebir korunur. Çok benzeyen bölümler tek blokta "görünüm" seçeneğiyle birleştirilir (onaylı liste: `docs/BLOKLAR.md`).
- **CSS:** `home.css`'in `base.css` tekrarı dışındaki kuralları, `ic-sayfa.css`, `iletisim.css` ve `kurumsal.css` değerleri değiştirilmeden bu sırayla tek `bloklar.css` dosyasında birleştirildi; tüm flexible sayfalarda `base.css` + `brandfont.css` ardından yüklenir. `sozluk.js` ve iletişim aracı script'leri yalnızca ilgili blok sayfadaysa yüklenir.
- **Panel:** Çok görünümlü bloklarda conditional logic; kapalı layout başlığında bloğun başlık alanı gösterilir (`acf/fields/flexible_content/layout_title`).
- **Bölüm ID'leri:** Stile bağlı ID'ler (ör. `#sistemler`) panelde düzenlenemez, blok varsayılanıyla sabit basılır; serbest çapa alanı ayrıdır. Çakışmada sonraki bloğun ID'sine sıra eki verilir.
- Her bloğa ve kafa karıştırabilecek alanlara kısa Türkçe yardım metni (instructions) eklendi. Vaka çalışmaları sayfası Site Ayarları'ndan seçilir.

### 17. Ürün ve marka adlarının işaretlenmesi
- Terim listesi `servo_marka_adlari()` (`inc/helpers.php`, `servo_marka_adlari` filtresiyle genişletilebilir). Biçim tasarımı izler: "Precisor" ve "ProfiNet" `<span lang="en"><bdi lang="en">…</bdi></span>`, diğerleri `<bdi lang="en">…</bdi>`. "Core" ve "Shuttle" işaretin dışında kalır. SVG, script ve form seçeneklerinin içine dokunulmaz.
- Tasarım aynı terimi her yerde aynı biçimde işaretlemediği için görünmez markup farkları kabul edildi: ürün sayfalarında 3, blok sayfalarında 32 fazladan / 2 eksik, rehberde 66 fazladan / 7 eksik `<bdi>`. Tasarım CSS'i `bdi[lang="en"]` stilini sıfırladığı için görünüm değişmez.

### 18. Talep formları ve korumalı dokümanlar
- Formlar `talep_formu` bloğuyla kurulur, `servomotion/v1/talep` uç noktasına gider (nonce, honeypot, IP başına rate-limit, geliştirme modu mail kaydı). JS kapalıysa ya da alıcı adresi yoksa tasarımdaki `mailto:` yedeği çalışır. Form stilleri `servis.css` içinde `.talep-formu` altına kapsanır (iletişim sayfasının `.fld`/`.two` kurallarıyla çakışmaz).
- Veri sayfaları ve uygunluk sertifikası "Talep formuyla" erişimlidir: dosyalar `uploads/servo-korumali/` dizininde durur (doğrudan erişim ve arama motorları kapalı), yalnızca imzalı, 7 gün geçerli `?servo-indir=` bağlantısıyla verilir. Kurulum ve uygulama notları doğrudan indirilebilir (Medya).
- Site Ayarları > Formlar > "Doküman talebi: gönderim": "otomatik gönder" (varsayılan) ziyaretçiye indirme bağlantılarını e-postalar; "sadece ekibe bildir" yalnızca ekibe bildirim yollar. Ekibe bildirim her zaman gider.

## İngilizce sürüm: uygulama ayrıntıları

- **Dil yardımcıları (`inc/dil.php`):** `servo_dil()`, `servo_en()`, `servo_kayit_bul( $slug, $tur, $dil )` (kaydı dil filtresiyle slug'dan bulur; `get_page_by_path` yerine), `servo_js_yolu()` (EN'de `assets/js/en/` varsa o script), `<html lang>` (`tr` / `en-GB`), menüdeki TR/EN düğmesi (`a.langlink`).
- **Aynı slug iki dilde:** ücretsiz Polylang izin vermez; `servo_slug_paylas` (`wp_unique_post_slug`) farklı dillerdeki çakışmalarda özgün slug'ı korur, `servo_ana_sorgu_dil_filtresi` (`the_posts`) aynı slug'lı kayıtlardan yalnızca geçerli dilinkini bırakır.
- **EN adres ön ekleri:** *Gelen istek:* `rewrite_rules_array` filtresi (öncelik 999) `servo_en_kurallar()` ile yalnızca `en/` (Polylang anahtarlarında `(en)/`) ile başlayan kural anahtarlarındaki ön eki değiştirir; kalıcı bağlantı kaydı ve Polylang dil işlemlerinde de çalışır. *Giden adres:* `post_link`, `post_type_link` ve `term_link` filtreleri `servo_en_adres()` ile üretilen adresin başındaki ön eki değiştirir. Site kökü `home_url()` ile değil `get_option( 'home' )` ile alınır (ön yüzde `home_url()` dil öneki alır).
- **EN ana sayfa:** Polylang EN ön sayfayı `/en/home/` olarak sunar; `/en/` buraya yönlenir. `applications` sayfası yalnızca EN'dedir; dil düğmesi TR ana sayfadaki `#uygulama-alanlari`'na gider.
- **Çeviri dosyaları:** `languages/servomotion.pot` / `servomotion-en_GB.po/.mo`; `servo_ceviri_yukle()` ziyaretçi dilinde yükler. Yönetim paneli dizgeleri Türkçe kalır. Paketten çıkarılamayan çeviriler `docs/EN-CEVIRI-LISTESI.md`'dedir (müşteri onayı için).
- **İçerik betiği:** `wp servo faz4 [adım…] [--dry-run]` (`inc/cli-faz4.php`; adımlar: dil, ayarlar, js, medya, vakalar, uygulamalar, sayfalar, rehber, ayar_metinleri, tani, menuler, arama). TR içerikle bağlar, tekrar çalıştırılınca kopya üretmez. EN menüler "Main menu EN" / "Footer menu EN"dir ve Polylang menü eşlemesine yazılır.
- **Formlar:** `servomotion/v1/talep` isteği `lang` alır; ziyaretçiye giden mesaj ve e-posta onun dilindedir, ekibe bildirim her zaman TR'dir. Doküman CPT'sinde `dil` (`tr` / `en` / `ortak`) ve `talep_etiketi_en` alanları vardır.
- **EN sayfalar:** Core Precisor, Shuttle Precisor, iletişim, SSS, sözlük ve KVKK paketin EN sürümünde TR'den farklı yapıdadır (yeni sayfa şablonu / blok gerektirir); karar bekleyene kadar taslaktır.
