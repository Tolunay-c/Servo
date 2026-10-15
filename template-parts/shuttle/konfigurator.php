<?php
/**
 * Shuttle Precisor: Konfigüratör (#konfigurator).
 *
 * Bölüm başlığı alanlardan gelir. Altındaki markup shuttle-konfig.js'e bağlıdır
 * (ID'ler ve başlangıç değerleri script tarafından okunur), bu yüzden sabittir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cfgsec" id="konfigurator">
  <div class="wrap">
    <?php servo_urun_sec_hd( 'konfig' ); ?>

    <div class="cgrid">
      <div>
        <section class="card">
          <div class="card-hd"><h3><?php esc_html_e( 'Senaryo', 'servomotion' ); ?></h3></div>
          <div class="card-bd">
            <div class="modes">
              <button id="mA" aria-pressed="true"><?php esc_html_e( 'A · Adet girdi', 'servomotion' ); ?></button>
              <button id="mB" aria-pressed="false"><?php esc_html_e( 'B · Aralık girdi', 'servomotion' ); ?></button>
            </div>
            <p class="mnote" id="mnote"></p>
          </div>
        </section>

        <section class="card">
          <div class="card-hd"><h3><?php esc_html_e( 'Parametreler', 'servomotion' ); ?></h3><span class="hint"><?php esc_html_e( 'mm cinsinden', 'servomotion' ); ?></span></div>
          <div class="card-bd">
            <div class="fld">
              <div class="fld-hd"><label for="L"><?php esc_html_e( 'Strok mesafesi · L', 'servomotion' ); ?></label><span class="badge" data-role="in"><?php esc_html_e( 'girdi · mm', 'servomotion' ); ?></span></div>
              <div class="ctrl">
                <input type="range" id="Lr" min="600" max="6000" step="10" value="2400" aria-label="<?php echo esc_attr_x( 'Strok mesafesi', 'aria-label', 'servomotion' ); ?>">
                <input type="number" id="L" min="200" max="20000" step="10" value="2400">
              </div>
            </div>
            <div class="fld" id="fN">
              <div class="fld-hd"><label for="N"><?php esc_html_e( 'Gövdedeki lazer · n', 'servomotion' ); ?></label><span class="badge" id="bN" data-role="in"><?php esc_html_e( 'girdi · adet', 'servomotion' ); ?></span></div>
              <div class="ctrl">
                <input type="range" id="Nr" min="2" max="60" step="1" value="16" aria-label="<?php echo esc_attr_x( 'Lazer adedi', 'aria-label', 'servomotion' ); ?>">
                <input type="number" id="N" min="2" max="200" step="1" value="16">
              </div>
            </div>
            <div class="fld" id="fP">
              <div class="fld-hd"><label for="P"><?php esc_html_e( 'Çizgiler arası · p', 'servomotion' ); ?></label><span class="badge" id="bP"><?php esc_html_e( 'hesaplanan · mm', 'servomotion' ); ?></span></div>
              <div class="ctrl">
                <input type="range" id="Pr" min="60" max="600" step="1" value="156" aria-label="<?php echo esc_attr_x( 'Çizgiler arası mesafe', 'aria-label', 'servomotion' ); ?>">
                <input type="number" id="P" min="10" max="5000" step="0.5" value="156">
              </div>
            </div>
          </div>
        </section>
      </div>

      <div>
        <section class="card">
          <div class="stats">
            <div class="st hero2"><div class="k"><?php esc_html_e( 'Çizgiler arası · p', 'servomotion' ); ?></div><div class="v" id="oP">156<small>mm</small></div></div>
            <div class="st"><div class="k"><?php esc_html_e( 'Aktif / rezerve', 'servomotion' ); ?></div><div class="v" id="oN">16<small>/ 0</small></div></div>
            <div class="st"><div class="k"><?php esc_html_e( 'Arabalar arası boşluk', 'servomotion' ); ?></div><div class="v" id="oX">96<small>mm</small></div></div>
            <div class="st"><div class="k"><?php esc_html_e( 'Gereken ray boyu', 'servomotion' ); ?></div><div class="v" id="oR">2.600<small>mm</small></div></div>
          </div>
          <div class="status" id="stat"></div>
          <div class="stage-wrap"><div id="stage"></div></div>
          <div class="legend">
            <span><i style="background:#4FA3E3"></i><?php esc_html_e( 'Güç barası', 'servomotion' ); ?></span>
            <span><i style="background:#3D4C5E"></i><?php esc_html_e( 'Lazer arabası', 'servomotion' ); ?></span>
            <span><i style="background:#33D183"></i><?php esc_html_e( 'Fren kilitli', 'servomotion' ); ?></span>
            <span><i style="background:#F5A623"></i><?php esc_html_e( 'Fren açık · mekikte', 'servomotion' ); ?></span>
            <span><i style="background:#FF2D20"></i><?php esc_html_e( 'Lazer çizgisi', 'servomotion' ); ?></span>
            <span><i style="background:#2E3A48"></i><?php esc_html_e( 'Rezerve · lazer kapalı', 'servomotion' ); ?></span>
          </div>
          <div class="bar">
            <button class="btn btn-y" id="go" style="padding:9px 18px;font-size:14px"><?php esc_html_e( 'Kurulumu Oynat', 'servomotion' ); ?></button>
            <button class="btn btn-o" id="rst" style="padding:9px 18px;font-size:14px;border-color:#33465A"><?php esc_html_e( 'Sıfırla', 'servomotion' ); ?></button>
            <div class="modes" id="segSpeed" style="width:156px;flex:none;grid-template-columns:repeat(3,1fr)" role="group" aria-label="<?php echo esc_attr_x( 'Simülasyon hızı', 'aria-label', 'servomotion' ); ?>">
              <button data-s="1" aria-pressed="false" style="padding:8px 4px">1×</button>
              <button data-s="4" aria-pressed="true" style="padding:8px 4px">4×</button>
              <button data-s="12" aria-pressed="false" style="padding:8px 4px">12×</button>
            </div>
            <div class="prog"><i id="pi"></i></div>
          </div>
          <div class="now" id="now"><span class="dot"></span><span id="nowT"><?php esc_html_e( 'Hazır', 'servomotion' ); ?></span>
            <span class="d" id="nowD"><?php esc_html_e( 'Parametreleri girip kurulumu oynatın', 'servomotion' ); ?></span></div>
        </section>
        <p class="note" style="margin-left:2px"><?php esc_html_e( 'Hesap, eşit aralıklı yerleşim varsayar. Değişken dilim genişlikli reçeteler de desteklenir; bu durumda sınır, en dar iki çizgi arasındaki mesafedir.', 'servomotion' ); ?></p>
      </div>
    </div>
  </div>
</section>
