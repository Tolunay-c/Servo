<?php
/**
 * Core Precisor: Gövde boyu merdiveni (#govde).
 *
 * Bölüm başlığı alanlardan gelir. Altındaki markup core-govde.js'e bağlıdır
 * (ID'ler ve başlangıç değerleri script tarafından okunur), bu yüzden sabittir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfgsec" id="govde">
  <div class="wrap">
    <?php servo_urun_sec_hd( 'govde' ); ?>

    <div class="hsg">
      <div class="hsg-hd">
        <span class="code" id="hCode">L4</span>
        <h3><?php esc_html_e( 'Sistem Gövdesi', 'servomotion' ); ?></h3>
        <span class="w" id="hW"><?php esc_html_e( '2.600 mm', 'servomotion' ); ?></span>
      </div>

      <div class="hstage">
        <div class="prof">
          <div class="pbody" id="pbody">
            <span class="pcap l"></span><span class="pcap r"></span>
            <span class="prail"></span>
            <span class="pzone" id="pzone"></span>
            <span class="pcart" id="pcart"></span>
          </div>
        </div>
        <div class="dims" id="dims">
          <span class="dline" id="dTot" style="top:16px"></span>
          <span class="dlab" id="lTot" style="top:24px"></span>
          <span class="dline" id="dStr" style="top:0"></span>
          <span class="dlab sm" id="lStr" style="top:-14px"></span>
          <span class="dlab sm" id="lMl" style="top:-14px"></span>
          <span class="dlab sm" id="lMr" style="top:-14px"></span>
        </div>
      </div>

      <div class="hbar">
        <button class="btn btn-o" id="hPrev" style="background:transparent;border-color:#33465A;color:#D6DEE7"><?php esc_html_e( '‹ Önceki', 'servomotion' ); ?></button>
        <button class="btn btn-y" id="hAuto"><?php esc_html_e( 'Adım Adım Göster', 'servomotion' ); ?></button>
        <button class="btn btn-o" id="hNext" style="background:transparent;border-color:#33465A;color:#D6DEE7"><?php esc_html_e( 'Sonraki ›', 'servomotion' ); ?></button>
        <span class="hstep" id="hStep">4 / 6</span>
      </div>

      <div class="ladder" id="ladder"></div><p class="simnote"><?php esc_html_e( 'Ölçü gösterimi, her uçta 80 mm pay kabul edilen örnek bir yerleşimdir. Kullanılabilir hareket mesafesi; lazer modülü boyutu, uç sınırları ve seçilen konfigürasyona göre projede kesinleştirilir.', 'servomotion' ); ?></p>

    </div>
  </div>
</section>
