<?php
/**
 * Blok: Uygulama özeti aracı (section.section.brief).
 *
 * Form markup'ı _design/iletisim.html ile aynıdır; alan ID'leri
 * iletisim-ozet.js ve iletisim-gonder.js'e bağlıdır, değiştirilmemelidir.
 * Gönderim açıkken (inc/formlar.php) ekran dışı bir honeypot alanı eklenir.
 * Script'ler sadece bu blok sayfadaysa yüklenir (inc/enqueue.php).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

echo servo_section_open( 'section brief' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );
?>
<div class="bgrid">
      <div class="bcard">
        <div class="hd"><h3><?php esc_html_e( 'UYGULAMA BİLGİLERİ', 'servomotion' ); ?></h3><span class="n"><?php esc_html_e( '01 / GİRDİ', 'servomotion' ); ?></span></div>
        <div class="bd">
          <div class="fld"><label for="f-hat"><?php esc_html_e( 'Hat / uygulama', 'servomotion' ); ?></label>
            <input id="f-hat" type="text" placeholder="<?php esc_attr_e( 'örn. bobin dilimleme hattı, nonwoven kesim', 'servomotion' ); ?>"></div>
          <div class="two">
            <div class="fld"><label for="f-cizgi"><?php esc_html_e( 'Aynı anda görünmesi gereken çizgi', 'servomotion' ); ?></label>
              <input id="f-cizgi" type="number" min="1" max="60" placeholder="<?php esc_attr_e( 'adet', 'servomotion' ); ?>"></div>
            <div class="fld"><label for="f-strok"><?php esc_html_e( 'Çalışma genişliği / strok', 'servomotion' ); ?></label>
              <input id="f-strok" type="number" min="100" max="20000" placeholder="<?php esc_attr_e( 'mm', 'servomotion' ); ?>"></div>
          </div>
          <div class="two">
            <div class="fld"><label for="f-mesafe"><?php esc_html_e( 'Çalışma mesafesi', 'servomotion' ); ?></label>
              <input id="f-mesafe" type="number" min="100" max="10000" placeholder="<?php esc_attr_e( 'mm', 'servomotion' ); ?>"></div>
            <div class="fld"><label for="f-sik"><?php esc_html_e( 'Reçete değişim sıklığı', 'servomotion' ); ?></label>
              <select id="f-sik">
                <option value=""><?php esc_html_e( 'seçin', 'servomotion' ); ?></option>
                <option><?php esc_html_e( 'Günde birkaç kez', 'servomotion' ); ?></option>
                <option><?php esc_html_e( 'Günde bir', 'servomotion' ); ?></option>
                <option><?php esc_html_e( 'Haftada birkaç kez', 'servomotion' ); ?></option>
                <option><?php esc_html_e( 'Daha seyrek', 'servomotion' ); ?></option>
              </select></div>
          </div>
          <div class="fld"><label for="f-montaj"><?php esc_html_e( 'Montaj yapısı', 'servomotion' ); ?></label>
            <select id="f-montaj">
              <option value=""><?php esc_html_e( 'seçin', 'servomotion' ); ?></option>
              <option><?php esc_html_e( 'Mevcut şaseye monte edilecek', 'servomotion' ); ?></option>
              <option><?php esc_html_e( 'Yeni taşıyıcı / traversi gerekiyor', 'servomotion' ); ?></option>
              <option><?php esc_html_e( 'Henüz belirlenmedi', 'servomotion' ); ?></option>
            </select></div>
          <div class="fld"><label><?php esc_html_e( 'Hazırda olan bilgiler', 'servomotion' ); ?></label>
            <div class="chk">
              <label><input type="checkbox" data-h="Ölçü şeması / teknik resim"> <?php esc_html_e( 'Ölçü şeması veya teknik resim', 'servomotion' ); ?></label>
              <label><input type="checkbox" data-h="PLC ve saha veri yolu bilgisi"> <?php esc_html_e( 'Hattaki PLC ve saha veri yolu bilgisi', 'servomotion' ); ?></label>
              <label><input type="checkbox" data-h="Titreşim gözlemi"> <?php esc_html_e( 'Hattın titreşim durumu hakkında gözlem', 'servomotion' ); ?></label>
              <label><input type="checkbox" data-h="Yüzey ve ortam ışığı bilgisi"> <?php esc_html_e( 'Malzeme yüzeyi ve ortam ışığı bilgisi', 'servomotion' ); ?></label>
              <label><input type="checkbox" data-h="Fotoğraf / video"> <?php esc_html_e( 'Hattan fotoğraf veya video', 'servomotion' ); ?></label>
            </div>
          </div>
          <div class="two">
            <div class="fld"><label for="f-firma"><?php esc_html_e( 'Firma', 'servomotion' ); ?></label>
              <input id="f-firma" type="text" placeholder="<?php esc_attr_e( 'firma adı', 'servomotion' ); ?>"></div>
            <div class="fld"><label for="f-kisi"><?php esc_html_e( 'İlgili kişi ve iletişim', 'servomotion' ); ?></label>
              <input id="f-kisi" type="text" placeholder="<?php esc_attr_e( 'ad soyad · telefon veya e-posta', 'servomotion' ); ?>"></div>
          </div>
          <?php servo_form_honeypot(); ?>
        </div>
      </div>

      <div class="bcard">
        <div class="hd"><h3><?php esc_html_e( 'ÖN DEĞERLENDİRME', 'servomotion' ); ?></h3><span class="n"><?php esc_html_e( '02 / ÇIKTI', 'servomotion' ); ?></span></div>
        <div class="bd">
          <div class="verdict">
            <p class="k"><?php esc_html_e( 'SİSTEM YÖNÜ', 'servomotion' ); ?></p>
            <p class="v" id="v-title"><?php esc_html_e( 'ÇİZGİ SAYISINI GİRİN', 'servomotion' ); ?></p>
            <p id="v-desc"><?php esc_html_e( 'Aynı anda kaç kesim çizgisinin görünmesi gerektiği, iki sistem arasındaki en belirleyici ayrımdır. Bu alanı doldurduğunuzda yönü burada göreceksiniz.', 'servomotion' ); ?></p>
          </div>
          <div class="summary" id="v-sum" aria-live="polite"><?php esc_html_e( 'Özet, alanları doldurdukça burada oluşur.', 'servomotion' ); ?></div>
          <div class="acts">
            <button class="btn dark" type="button" id="b-mail"><?php esc_html_e( 'E-POSTA İLE GÖNDER', 'servomotion' ); ?></button>
            <button class="btn" type="button" id="b-wa"><?php esc_html_e( 'WHATSAPP', 'servomotion' ); ?></button>
            <button class="btn" type="button" id="b-copy"><?php esc_html_e( 'ÖZETİ KOPYALA', 'servomotion' ); ?></button>
          </div>
          <p class="hint" id="b-note"><?php esc_html_e( 'Ön değerlendirme bağlayıcı değildir; nihai sistem seçimi keşifte alınan ölçülerle birlikte yapılır. Lazerler hizalama referansı oluşturur; malzeme ve makine elemanlarının ayarı operatör tarafından yapılır.', 'servomotion' ); ?></p>
        </div>
      </div>
    </div>
<?php
echo '</div></section>';
