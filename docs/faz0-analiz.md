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

