# ServoMotion sitesi — kurulum notları

Ayrıntılı ve işaretlenebilir sürüm: **kurulum-notlari.html**
Takip listesi: **servomotion-kurulum-kontrol-listesi.xlsx**

## WordPress kurulu sunucuya yüklerken
Bu site düz HTML'dir; WordPress, PHP ve veritabanı gerektirmez. Alan adının kökünde
WordPress duruyorsa ana sayfayı WordPress açmaya devam eder. Üç yol:

1. **Statik siteye geçiş** — WordPress dosyaları yedeklenip kökten kaldırılır, paket
   `public_html` içine açılır. En hızlı ve en güvenli seçenek.
2. **Önce alt klasörde deneme** — paket `/yeni/` gibi bir klasöre açılır, kontrol edilir,
   sonra köke taşınır.
3. **WordPress'te kalmak** — sayfaların Elementor ile yeniden kurulması gerekir;
   konfigüratör ve simülasyon ancak "özel HTML" bileşenine gömülerek çalışır.

Klasör izinleri 755, dosya izinleri 644.

## Yüklemeden önce
1. **Alan adı** — tüm adresler `https://www.servomotion.com.tr/` biçimine göre yazıldı.
2. **.htaccess** — paketteki `.htaccess` dosyasını da site köküne kopyalayın: https zorunlu,
   www'suz adres www'ya yönlenir, eski `blog-*.html` adresleri `rehber-*.html`'e taşınır,
   404 sayfası tanımlıdır. Sunucu Nginx ise aynı kuralların Nginx karşılığı yazılmalıdır.
3. **Eski dosyalar** — sunucuda `blog*.html` varsa silin; yazılar artık `rehber-*.html`.
4. **PDF'ler** — veri sayfaları HTML dosyalarıyla aynı klasörde olmalı.
5. **Form ucu (isteğe bağlı)** — `iletisim.html` içindeki
   `window.SERVOMOTION_FORM_ENDPOINT` alanı boşken de site eksiksiz çalışır.
6. **Yazı tipleri** — Barlow ailesi Google Fonts'tan geliyor; kendi sunucunuza
   almak gizlilik ve hız açısından daha temiz olur. Marka yazısının Poppins
   dosyaları `fonts/` altında, sitede yerel olarak servis ediliyor.
7. **Search Console** — yayına aldıktan sonra `sitemap.xml` adresini tanıtın.

## Belgeler
`servomotion-tuv-uygunluk-sertifikasi.pdf` — TÜV Rheinland uygunluk sertifikası
(EN 55011 / EN 55032, sert. no. AK 50498967 0001). Dokümanlar sayfasından ve
her iki dildeki ürün sayfalarının güvenlik bölümünden indirilebiliyor.

## Fotoğraflar
`foto/` klasöründeki dosyalar yer tutucudur. Gerçek saha fotoğraflarını **aynı adla**
koyduğunuzda sayfalarda kendiliğinden yerine geçer:
`vaka-lastik.jpg` · `vaka-nonwoven.jpg` · `vaka-ambalaj.jpg` ·
`kurumsal-uretim.jpg` · `kurumsal-saha.jpg` · `shuttle-kurulum.jpg`
(1600×1000 px, yatay.)

## Bekleyen maddeler
- Saha fotoğrafları (yer tutucular hazır, aynı adla değiştirin)
- Tomruk dilimleme vakası için strok değeri
