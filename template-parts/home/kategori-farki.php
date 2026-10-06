<?php
/**
 * Ana sayfa: Kategori farkı (#kategori): sabit referans ile servo kontrollü referans.
 *
 * Markup _design/index.html ile aynıdır. Metin ve görseller 7. adımda
 * ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="catband" id="kategori">
  <div class="wrap">
    <div class="sec-hd">
      <div class="rule"></div>
      <p class="tagline">Kategori Farkı</p>
      <h2>Sabit Ayar Değil.<br>Reçeteye Göre Konumlandırma.</h2>
    </div>
    <div class="cat2">
      <article class="cat cat-a">
        <p class="k">GELENEKSEL SABİT REFERANS</p>
        <h3>KONUM BİR KURULUM AYARIDIR</h3>
        <p>Lazer, devreye alma sırasında bir kez konumlandırılır. Reçete değiştiğinde çizgiyi
          operatör yeniden ayarlar; sonuç ayarı yapan kişiye, kullanılan ölçüm yöntemine ve o an
          ayrılabilen süreye bağlı kalır.</p>
      </article>
      <article class="cat cat-b">
        <p class="k">SERVO KONTROLLÜ REFERANS</p>
        <h3>KONUM BİR REÇETE VERİSİDİR</h3>
        <p>Ölçü panelden girilir; sistem hedef konumlara kendisi gider. Aynı reçete her
          çağrıldığında çizgiler aynı yere döner, ayar operatörün işi olmaktan çıkar ve
          ürün geçişi hattın kendi süresine bağlı hâle gelir.</p>
      </article>
    </div>
    <div class="catproof">
      <div><b>±1,00<small>mm</small></b><span>Projeksiyon hassasiyeti</span></div>
      <div><b>1000<small>mm/s</small></b><span>Konumlandırma hızı · Core</span></div>
      <div><b>6</b><span>Hareket konfigürasyonu · Core</span></div>
      <div><b>~118<small>sn</small></b><span>Tam kurulum döngüsü · Shuttle</span></div>
    </div>
    <p class="catnote">Değerler; 2.400 mm strok, 20 lazer ve 200 mm çizgi aralığı senaryosu üzerinden verilmiştir.
      Sabit referansın hangi uygulamalarda hâlâ doğru çözüm olduğunu
      <a href="<?php echo esc_url( servo_post_url( 'sabit-referans', 'post' ) ); ?>">Sabit referans lazeri ne işe yarar?</a> yazısında ele aldık.</p>
  </div>
</section>
