# Canlı site denetimi: görev tanımı

Canlı site denetimi yapıldı. Rapor: `docs/servomotion-canli-denetim.html` (12 majör, 10 minör, 8 doğrulanacak madde: M01–M12, m01–m10, D01–D08).

Raporun kök neden tespiti: `_design/` klasöründeki tasarım, revizyonlar uygulanmadan önceki sürüm. Son teslim paketi repo kökündeki `servomotion-site-tum131026/` klasöründe (EN sayfalar, OG görselleri, PDF'ler, video, fotoğraflar, fontlar).

## Önce yapılacak küçük işler

1. Rapor şu an tema kökünde duruyor. `docs/servomotion-canli-denetim.html` konumuna taşı ve paket.sh'teki geçici hariç tutmayı kaldır (docs/ zaten hariç).
2. `servomotion-site-tum131026/` klasörünün WordPress kökündeki (`htdocs/servo/`) kopyasını sil; tema kökündeki tek kopya kalsın. Canlı paket WordPress kökünü zip'lediği için orada kalmamalı.
3. `Revizyonlar.docx` şu an yok. FAZ 0'ı onsuz yap: raporda geçen revizyon maddeleri (madde numaralarıyla anılıyor) ve `_design/` ile `servomotion-site-tum131026/` arasındaki farklar yeterli. Dosya gelirse docs/ altına koyacağım, o zaman farkları onunla teyit et.

## Kurallar

- `servomotion-site-tum131026/` yeni referans tasarım. `_design/` ve `servomotion-site-tum131026/` salt okunur, hiçbir dosyaya dokunma. (CLAUDE.md ve paket.sh güncellemesi yapıldı.)
- Tüm işler önce yerelde yapılacak. Commit atma.
- Her fazın sonunda değişen dosyaları ve yerel veritabanında yaptığın içerik değişikliklerini listele, tarayıcıda neye bakmam gerektiğini söyle ve dur.
- İçerik değişikliklerini (sayfa, blok, yazı, menü, medya) elle değil, tekrar çalıştırılabilir WP-CLI betikleriyle yap. Canlıya taşırken aynı sonucu garanti etmek istiyorum. Betik tekrar çalıştırıldığında kopya oluşturmasın.
- Mevcut mimari kararlar (flexible content blokları, SCF, Site Ayarları, marka adı filtresi, REST form altyapısı) geçerli. Yeni ihtiyaçları bu yapının içinde çöz; yeni blok veya CPT gerekiyorsa FAZ 0'da öner.

## FAZ 0: Analiz (hiçbir şeyi değiştirme)

`_design/` ile `servomotion-site-tum131026/` arasındaki farkları çıkar. Raporun 30 maddesinin her biri için şunları yaz:

- Neden kaynaklandığı: eski tasarım / bizim aktarım hatamız / canlı ortam ayarı
- Nasıl çözüleceği
- Hangi dosyaları ve hangi içeriği etkileyeceği

Ayrıca:

- Raporda olmayan ama son pakette değişmiş başka farklar varsa listele.
- Raporun yanlış teşhis koyduğu maddeler varsa (ör. aslında çalışan bir JS bileşeni, yerelde var olan bir arama kutusu) gerekçesiyle belirt.
- Gereken yeni blokları, CPT'leri ve alanları öner.

Planı sun ve onayımı bekle.

## Onaydan sonra fazlar (bu sırayla)

### FAZ 1: İçerik ve sayfa eksikleri (TR)

- M02: örnek sayfa kalıcı olarak silinsin
- M03: Başarı Hikayeleri; `/referanslar/` → `/basari-hikayeleri/` 301, menü, footer ve başlıklar
- M04: ağaç/kereste vakası kaldırılsın, sayfa güncel paketteki kurulumlarla
- M05: film-folyo, tekstil, etiket uygulama sayfaları; ana sayfadaki altı karta bağlantı ve örnek kurulum; footer altı uygulama
- M06: servis alt sayfaları (destek hattı, iade ve sevkiyat, kılavuzlar) ve servis sayfasından bağlantılar
- M09: rehber yazılarından imza satırı kaldırılsın
- M10: rehber yazı tarihleri 23 Eylül 2026 (sayfada ve yapılandırılmış veride)
- M11: kurumsal sayfadaki üretim/saha görselleri; grup markalarında alan adı gösterimi
- m01, m02, m03, m04

### FAZ 2: Dokümanlar

- M07: doğrudan PDF indirme kaldırılsın, yerine talep formu; talep edilen doküman e-postayla gönderilsin. PDF'ler doğrudan erişime ve arama motorlarına kapalı bir dizinde dursun. Mevcut REST form altyapısını (nonce, honeypot, rate-limit, geliştirme modu) kullan.
- M08: uygulama notu PDF'leri yüklensin, her uygulama sayfasındaki buton kendi notuna bağlansın. (M07'deki form kuralıyla çelişip çelişmediğini FAZ 0'da belirt; revizyon iki farklı şey istiyor olabilir.)
- m07

### FAZ 3: Teknik ve SEO

- m08: `html lang`, Open Graph ve Twitter etiketleri; Rank Math ile çakışmadan. OG görselleri yüklensin, sayfalara bağlansın.
- m09: generator meta etiketi kaldırılsın
- M12: www → www'suz 301; `.htaccess` kural bloğuna ekle. `docs/YONLENDIRMELER.md`'yi `/referanslar/` ve yeni sayfalar dahil güncelle.
- m05: rehber ve SSS arama alanları (önce yerelde gerçekten eksik mi kontrol et)
- m06: bülten bandı; son paketteki hâline göre karar öner
- m10: tüm sayfa içi çapaları kontrol et, eksik id'leri tamamla

### FAZ 4: İngilizce sürüm (M01)

Kararlaştırdığımız gibi Polylang (ücretsiz) ile, `servomotion-site-tum131026/en/` yapısına göre. Menüye TR/EN düğmesi, hreflang etiketleri.

Başlamadan önce kapsamı özetleyip onayımı al: sayfa listesi, Site Ayarları/Options alanlarının dile göre nasıl ayrılacağı (`servo_option()` noktası), EN PDF'ler, EN rehber içerikleri var mı, menü taşması (D08).

### FAZ 5: Doğrulama

- D01–D08'in hepsini Playwright ile yerelde test et (D08 için 1440, 1366, 1024 ve 390 px).
- Raporun 30 maddesinin her biri için "kapandı / kapanmadı / canlıda kontrol edilecek" tablosu ver.

### FAZ 6: Canlı paket

- Önceki taşımadaki gibi `servo-canli.sql` ve `servo-canli.zip`'i yeniden üret (adresler `https://servomotion.com.tr`, `wp-config.php` yer tutuculu, `servomotion-site-tum131026/` ve diğer geliştirme dosyaları hariç).
- Canlıda yeniden yapılması gereken ayarları (admin şifresi, SMTP, Rank Math sihirbazı, Polylang ayarları vb.) `docs/CANLI-KURULUM.md`'ye ekle.

FAZ 0 ile başla.