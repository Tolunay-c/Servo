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
          <div class="card-hd"><h3>Senaryo</h3></div>
          <div class="card-bd">
            <div class="modes">
              <button id="mA" aria-pressed="true">A · Adet girdi</button>
              <button id="mB" aria-pressed="false">B · Aralık girdi</button>
            </div>
            <p class="mnote" id="mnote"></p>
          </div>
        </section>

        <section class="card">
          <div class="card-hd"><h3>Parametreler</h3><span class="hint">mm cinsinden</span></div>
          <div class="card-bd">
            <div class="fld">
              <div class="fld-hd"><label for="L">Strok mesafesi · L</label><span class="badge" data-role="in">girdi · mm</span></div>
              <div class="ctrl">
                <input type="range" id="Lr" min="600" max="6000" step="10" value="2400" aria-label="Strok mesafesi">
                <input type="number" id="L" min="200" max="20000" step="10" value="2400">
              </div>
            </div>
            <div class="fld" id="fN">
              <div class="fld-hd"><label for="N">Gövdedeki lazer · n</label><span class="badge" id="bN" data-role="in">girdi · adet</span></div>
              <div class="ctrl">
                <input type="range" id="Nr" min="2" max="60" step="1" value="16" aria-label="Lazer adedi">
                <input type="number" id="N" min="2" max="200" step="1" value="16">
              </div>
            </div>
            <div class="fld" id="fP">
              <div class="fld-hd"><label for="P">Çizgiler arası · p</label><span class="badge" id="bP">hesaplanan · mm</span></div>
              <div class="ctrl">
                <input type="range" id="Pr" min="60" max="600" step="1" value="156" aria-label="Çizgiler arası mesafe">
                <input type="number" id="P" min="10" max="5000" step="0.5" value="156">
              </div>
            </div>
          </div>
        </section>
      </div>

      <div>
        <section class="card">
          <div class="stats">
            <div class="st hero2"><div class="k">Çizgiler arası · p</div><div class="v" id="oP">156<small>mm</small></div></div>
            <div class="st"><div class="k">Aktif / rezerve</div><div class="v" id="oN">16<small>/ 0</small></div></div>
            <div class="st"><div class="k">Arabalar arası boşluk</div><div class="v" id="oX">96<small>mm</small></div></div>
            <div class="st"><div class="k">Gereken ray boyu</div><div class="v" id="oR">2.600<small>mm</small></div></div>
          </div>
          <div class="status" id="stat"></div>
          <div class="stage-wrap"><div id="stage"></div></div>
          <div class="legend">
            <span><i style="background:#4FA3E3"></i>Güç barası</span>
            <span><i style="background:#3D4C5E"></i>Lazer arabası</span>
            <span><i style="background:#33D183"></i>Fren kilitli</span>
            <span><i style="background:#F5A623"></i>Fren açık · mekikte</span>
            <span><i style="background:#FF2D20"></i>Lazer çizgisi</span>
            <span><i style="background:#2E3A48"></i>Rezerve · lazer kapalı</span>
          </div>
          <div class="bar">
            <button class="btn btn-y" id="go" style="padding:9px 18px;font-size:14px">Kurulumu Oynat</button>
            <button class="btn btn-o" id="rst" style="padding:9px 18px;font-size:14px;border-color:#33465A">Sıfırla</button>
            <div class="modes" id="segSpeed" style="width:156px;flex:none;grid-template-columns:repeat(3,1fr)" role="group" aria-label="Simülasyon hızı">
              <button data-s="1" aria-pressed="false" style="padding:8px 4px">1×</button>
              <button data-s="4" aria-pressed="true" style="padding:8px 4px">4×</button>
              <button data-s="12" aria-pressed="false" style="padding:8px 4px">12×</button>
            </div>
            <div class="prog"><i id="pi"></i></div>
          </div>
          <div class="now" id="now"><span class="dot"></span><span id="nowT">Hazır</span>
            <span class="d" id="nowD">Parametreleri girip kurulumu oynatın</span></div>
        </section>
        <p class="note" style="margin-left:2px">Hesap, eşit aralıklı yerleşim varsayar. Değişken dilim genişlikli reçeteler de
          desteklenir; bu durumda sınır, en dar iki çizgi arasındaki mesafedir.</p>
      </div>
    </div>
  </div>
</section>
