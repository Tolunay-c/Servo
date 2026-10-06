<?php
/**
 * Ana sayfa: Teknik karşılaştırma tablosu (.cmp).
 *
 * Markup _design/index.html ile aynıdır. Metin ve görseller 7. adımda
 * ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cmp">
  <div class="wrap">
    <div class="sec-hd">
      <div class="rule"></div>
      <p class="tagline">Teknik Karşılaştırma</p>
      <h2>Aynı Referans.<br>Farklı Hareket Mimarisi.</h2>
    </div>
    <div class="tbl-scroll">
      <table class="ctable">
        <thead><tr><th>Kriter</th><th>Core <span lang="en"><bdi lang="en">Precisor</bdi></span></th><th>Shuttle <span lang="en"><bdi lang="en">Precisor</bdi></span></th></tr></thead>
        <tbody>
          <tr><td>Mimari</td>
            <td>Kapalı gövdede servo tahrikli lazer hizalama sistemi
              <span class="sm">6 hareket konfigürasyonu</span></td>
            <td>Üç raylı mekik yapısında çoklu lazer hizalama sistemi
              <span class="sm">Tek servo tahrik, çok sayıda kilitli araba</span></td></tr>
          <tr><td>Aynı anda görünen çizgi</td>
            <td class="no">Gövdedeki lazer adedi kadar
              <span class="sm">Kalan çizgiler reçete adımlarıyla sırayla gösterilir</span></td>
            <td class="yes">Tüm kesim çizgileri
              <span class="sm">Reçete kurulduktan sonra eş zamanlı ve sürekli</span></td></tr>
          <tr><td>Tipik lazer adedi</td><td>1 – 6</td><td>5 – 40+</td></tr>
          <tr><td>Servo eksen sayısı</td><td>Hareketli lazer başına 1 eksen</td><td>Toplam 1 mekik ekseni (Tek tahrik)</td></tr>
          <tr><td>Uygun uygulama</td><td>Az dilimli dilimleme, bıçak hizalama, tek referanslı hatlar</td>
            <td>Çok dilimli dilimleme, kalender ve geniş bant hatları, elastik/kulak besleme</td></tr>
          <tr><td>Kurulum sonrası</td><td>Lazerler bir sonraki reçete konumuna geçer</td>
            <td>Mekik başlangıç konumuna geri döner, tüm çizgiler yerinde kalır</td></tr>
          <tr><td>Gövde uzunluğu</td><td>600 – 4.000 mm
              <span class="sm">Daha uzun hatlar talebe göre tasarlanır</span></td>
            <td>Strok + rezerve alanı + emniyet payı
              <span class="sm">Projeye göre ölçeklendirilir · konfigüratör hesaplar</span></td></tr>
          <tr><td>Ortak altyapı</td><td colspan="2"><div class="infra">
              <div class="inf"><?php echo servo_get_svg( 'ikon-plc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?><span>Endüstriyel PLC ve servo sürücü kontrolü</span></div>
              <div class="inf"><?php echo servo_get_svg( 'ikon-veri-yolu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?><span>Standart saha veri yolu ile hat otomasyonuna entegrasyon</span></div>
              <div class="inf"><?php echo servo_get_svg( 'ikon-panel' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?><span>Dokunmatik operatör paneli</span></div>
              <div class="inf"><?php echo servo_get_svg( 'ikon-lazer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?><span>Endüstriyel çizgi lazer modülleri</span></div>
            </div></td></tr>
        </tbody>
      </table>
    </div>
    <p class="note">Nihai performans ve bağlantı kapsamı; hareket yükü, uygulama mesafesi ve montaj
      koşullarıyla birlikte değerlendirilir. Lazerler hizalama referansı oluşturur; malzeme ve makine
      elemanlarının ayarı operatör tarafından yapılır.</p>
  </div>
</section>
