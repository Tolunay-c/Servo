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
      <button class="btn btn-y" id="play" style="padding:9px 18px;font-size:14px">Durdur</button>
      <div class="modes" id="segSpeed" style="width:180px;flex:none;grid-template-columns:repeat(3,1fr)" role="group" aria-label="Animasyon hızı">
        <button type="button" data-sp="0.5" aria-pressed="false">0,5×</button>
        <button type="button" data-sp="1" aria-pressed="true">1×</button>
        <button type="button" data-sp="2" aria-pressed="false">2×</button>
      </div>
    </div>

    <div class="cfgwrap" id="cfgwrap"></div>

    <div class="cfglegend">
      <span><i style="background:#FF2D20"></i>Hareketli lazer</span>
      <span><i style="background:#2FE05C"></i>Sabit referans lazeri</span>
      <span><i style="background:#FFD400"></i>Gövde / lineer eksen</span>
    </div>

    <div class="sim" id="sim">
      <div class="sim-hd">
        <span class="code" id="dCode">K1</span>
        <h3 id="dTitle">Tek Kanal · 1 Hareketli Lazer</h3>
        <span class="w" id="dW">1.200 mm çalışma genişliği</span>
      </div>

      <div class="scene"><svg id="svg" viewBox="0 0 1000 324" role="img" aria-label="Konfigürasyon simülasyonu"></svg></div>

      <div class="simlegend">
        <span><i style="background:#FFD400"></i>Servo gövde · kanal</span>
        <span><i style="background:#FF2D20"></i>Hareketli lazer arabası</span>
        <span><i style="background:#2FE05C"></i>Sabit lazer (ayrı braket)</span>
        <span><i style="background:#FF2D20;opacity:.55"></i>Lazer çizgisi</span>
        <span><i style="background:#C9D2DC"></i>Hizalanmış bıçak</span>
      </div>

      <div class="simbar">
        <button class="btn btn-y" id="rep" style="padding:9px 18px;font-size:14px">Tekrar Oynat</button>
        <button class="btn btn-o" id="rst" style="padding:9px 18px;font-size:14px;background:transparent;border-color:#33465A;color:#D6DEE7">Sıfırla</button>
        <div class="prog"><i id="pi"></i></div>
      </div>

      <div class="simstat idle" id="stat">
        <span class="dot"></span><span id="statT">Hazır</span>
        <span class="d" id="statD">Kurulumu başlatmak için Tekrar Oynat'a basın</span>
      </div>

      <div class="simstats">
        <div class="ss"><div class="k">Lazer adedi</div><div class="v" id="oN">1<small>adet</small></div></div>
        <div class="ss"><div class="k">Kanal</div><div class="v" id="oCh">1<small>hat</small></div></div>
        <div class="ss"><div class="k">Dilim sayısı</div><div class="v" id="oSl">4<small>dilim</small></div></div>
        <div class="ss"><div class="k">Konumlanma adımı</div><div class="v" id="oStep">3<small>adım</small></div></div>
        <div class="ss"><div class="k">Örnek hareket süresi</div><div class="v" id="oT">1,8<small>sn</small></div></div>
      </div>

      <p class="simnote">Gösterim şematiktir. Süre, 1.000 mm/s varsayımı ve adım başına 0,25 saniye bekleme ile hesaplanır; gerçek ivmelenme profili, modüller arası mesafe ve operatörün ayar süresi modellenmez. Toplam kurulum süresini göstermez.</p>

      <div class="simdesc">
        <p id="dDesc"></p>
        <dl class="kv">
          <dt>Kanal</dt><dd id="dCh"></dd>
          <dt>Lazer</dt><dd id="dLz"></dd>
          <dt>Hareket</dt><dd id="dMv"></dd>
          <dt>Tipik kullanım</dt><dd id="dUse"></dd>
        </dl>
      </div>
    </div>
  </div>
</section>
