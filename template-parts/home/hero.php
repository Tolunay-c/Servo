<?php
/**
 * Ana sayfa: Hero: başlık, mesaj, butonlar, ürün görseli ve teknik şerit.
 *
 * Markup _design/index.html ile aynıdır. Metin ve görseller 7. adımda
 * ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="hero"><div class="wrap hero-grid"><div><p class="label">SABİT AYAR DEĞİL, REÇETEYE GÖRE KONUMLANDIRMA</p><h1>DOĞRU KONUM<br><em>HIZLI AYAR</em></h1><div class="hero-message"><p class="hero-benefits"><span>Hassas hizalama.</span><span>Hızlı ürün geçişleri.</span><strong>Verimli üretim.</strong></p><p class="hero-signoff"><bdi lang="en">ServoMotion</bdi> lazer konumlandırma sistemleriyle <br>üretiminize yön verin.</p></div><div class="actions"><a class="btn primary" href="#sistemler">SİSTEMLERİ KARŞILAŞTIRIN</a><a class="btn" href="#iletisim">TEKLİF İSTEYİN</a></div></div><figure><img src="<?php echo esc_url( servo_asset_uri( 'assets/img/core-precisor.jpg' ) ); ?>" width="1000" height="915" alt="CORE PRECISOR lazer konumlandırma sistemi — iki kırmızı, ortada bir yeşil referans çizgisi"><figcaption><b>CORE PRECISOR</b><span>Kompakt gövde · Servo kontrollü hareket</span></figcaption></figure></div><div class="wrap"><div class="strip"><div><b>PLC TABANLI KONTROL</b><p>Reçete ve hareket yönetimi</p><span>PROFINET / EtherNet/IP haberleşmesi</span></div><div><b>SERVO HAREKET MİMARİSİ</b><p>CORE: bağımsız ve simetrik hareket<br>SHUTTLE: tek mekikle sıralı yerleştirme</p><span>Enkoder geri beslemeli konum kontrolü</span></div><div><b>KIRMIZI / YEŞİL LAZER</b><p>Ayarlanabilir odak · Çizgisel referans</p><span>12–24 VDC besleme · Standart M12 bağlantı</span></div><div><b>UYGULAMAYA ÖZEL MEKANİK</b><p>CORE: kompakt, kapalı gövde<br>SHUTTLE: üç raylı, kilitli araba yapısı</p><span>Çalışma alanına göre boyutlandırma</span></div></div></div></section>
