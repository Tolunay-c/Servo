<?php
/**
 * Core Precisor: Hareket konfigürasyonları ve simülasyon (#konfigurasyonlar).
 *
 * Bölüm başlığı alanlardan gelir. Altındaki markup core-sim.js'e bağlıdır
 * (ID'ler ve başlangıç değerleri script tarafından okunur), bu yüzden sabittir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfgsec" id="konfigurasyonlar">
  <div class="wrap">
    <?php servo_urun_sec_hd( 'konfig' ); ?>

    <div class="cfgbar">
      <button class="btn btn-y" id="play" style="padding:9px 18px;font-size:14px"><?php esc_html_e( 'Durdur', 'servomotion' ); ?></button>
      <div class="modes" id="segSpeed" style="width:180px;flex:none;grid-template-columns:repeat(3,1fr)" role="group" aria-label="<?php echo esc_attr_x( 'Animasyon hızı', 'aria-label', 'servomotion' ); ?>">
        <button type="button" data-sp="0.5" aria-pressed="false"><?php esc_html_e( '0,5×', 'servomotion' ); ?></button>
        <button type="button" data-sp="1" aria-pressed="true">1×</button>
        <button type="button" data-sp="2" aria-pressed="false">2×</button>
      </div>
    </div>

    <div class="cfgwrap" id="cfgwrap"></div>

    <div class="cfglegend">
      <span><i style="background:#FF2D20"></i><?php esc_html_e( 'Hareketli lazer', 'servomotion' ); ?></span>
      <span><i style="background:#2FE05C"></i><?php esc_html_e( 'Sabit referans lazeri', 'servomotion' ); ?></span>
      <span><i style="background:#FFD400"></i><?php esc_html_e( 'Gövde / lineer eksen', 'servomotion' ); ?></span>
    </div>

    <div class="sim" id="sim">
      <div class="sim-hd">
        <span class="code" id="dCode">K1</span>
        <h3 id="dTitle"><?php esc_html_e( 'Tek Kanal · 1 Hareketli Lazer', 'servomotion' ); ?></h3>
        <span class="w" id="dW"><?php esc_html_e( '1.200 mm çalışma genişliği', 'servomotion' ); ?></span>
      </div>

      <div class="scene"><svg id="svg" viewBox="0 0 1000 324" role="img" aria-label="<?php echo esc_attr_x( 'Konfigürasyon simülasyonu', 'aria-label', 'servomotion' ); ?>"></svg></div>

      <div class="simlegend">
        <span><i style="background:#FFD400"></i><?php esc_html_e( 'Servo gövde · kanal', 'servomotion' ); ?></span>
        <span><i style="background:#FF2D20"></i><?php esc_html_e( 'Hareketli lazer arabası', 'servomotion' ); ?></span>
        <span><i style="background:#2FE05C"></i><?php esc_html_e( 'Sabit lazer (ayrı braket)', 'servomotion' ); ?></span>
        <span><i style="background:#FF2D20;opacity:.55"></i><?php esc_html_e( 'Lazer çizgisi', 'servomotion' ); ?></span>
        <span><i style="background:#C9D2DC"></i><?php esc_html_e( 'Hizalanmış bıçak', 'servomotion' ); ?></span>
      </div>

      <div class="simbar">
        <button class="btn btn-y" id="rep" style="padding:9px 18px;font-size:14px"><?php esc_html_e( 'Tekrar Oynat', 'servomotion' ); ?></button>
        <button class="btn btn-o" id="rst" style="padding:9px 18px;font-size:14px;background:transparent;border-color:#33465A;color:#D6DEE7"><?php esc_html_e( 'Sıfırla', 'servomotion' ); ?></button>
        <div class="prog"><i id="pi"></i></div>
      </div>

      <div class="simstat idle" id="stat">
        <span class="dot"></span><span id="statT"><?php esc_html_e( 'Hazır', 'servomotion' ); ?></span>
        <span class="d" id="statD"><?php esc_html_e( 'Kurulumu başlatmak için Tekrar Oynat\'a basın', 'servomotion' ); ?></span>
      </div>

      <div class="simstats">
        <div class="ss"><div class="k"><?php esc_html_e( 'Lazer adedi', 'servomotion' ); ?></div><div class="v" id="oN">1<small><?php esc_html_e( 'adet', 'servomotion' ); ?></small></div></div>
        <div class="ss"><div class="k"><?php esc_html_e( 'Kanal', 'servomotion' ); ?></div><div class="v" id="oCh">1<small><?php esc_html_e( 'hat', 'servomotion' ); ?></small></div></div>
        <div class="ss"><div class="k"><?php esc_html_e( 'Dilim sayısı', 'servomotion' ); ?></div><div class="v" id="oSl">4<small><?php esc_html_e( 'dilim', 'servomotion' ); ?></small></div></div>
        <div class="ss"><div class="k"><?php esc_html_e( 'Konumlanma adımı', 'servomotion' ); ?></div><div class="v" id="oStep">3<small><?php esc_html_e( 'adım', 'servomotion' ); ?></small></div></div>
        <div class="ss"><div class="k"><?php esc_html_e( 'Örnek hareket süresi', 'servomotion' ); ?></div><div class="v" id="oT">1,8<small><?php esc_html_e( 'sn', 'servomotion' ); ?></small></div></div>
      </div>

      <p class="simnote"><?php esc_html_e( 'Gösterim şematiktir. Süre, 1.000 mm/s varsayımı ve adım başına 0,25 saniye bekleme ile hesaplanır; gerçek ivmelenme profili, modüller arası mesafe ve operatörün ayar süresi modellenmez. Toplam kurulum süresini göstermez.', 'servomotion' ); ?></p>

      <div class="simdesc">
        <p id="dDesc"></p>
        <dl class="kv">
          <dt><?php esc_html_e( 'Kanal', 'servomotion' ); ?></dt><dd id="dCh"></dd>
          <dt><?php esc_html_e( 'Lazer', 'servomotion' ); ?></dt><dd id="dLz"></dd>
          <dt><?php esc_html_e( 'Hareket', 'servomotion' ); ?></dt><dd id="dMv"></dd>
          <dt><?php esc_html_e( 'Tipik kullanım', 'servomotion' ); ?></dt><dd id="dUse"></dd>
        </dl>
      </div>
    </div>
  </div>
</section>
