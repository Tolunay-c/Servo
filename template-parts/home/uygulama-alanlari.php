<?php
/**
 * Ana sayfa: Endüstriyel uygulamalar (#uygulama-alanlari).
 *
 * Markup _design/index.html ile aynıdır. Metin ve görseller 7. adımda
 * ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section" id="uygulama-alanlari" style="background:white"><div class="wrap">
<div class="section-head"><p class="label">ENDÜSTRİYEL UYGULAMALAR</p>
<h2>FARKLI PROSESLER İÇİN<br>ORTAK HİZALAMA YAKLAŞIMI</h2>
<p><bdi lang="en">ServoMotion</bdi> sistemleri; malzeme yerleştirme, kenar hizalama ve makine ayarı süreçlerinde konum referansı oluşturur. Lazer yerleşimi ve hareket mimarisi; proses geometrisi, çalışma mesafesi ve reçete gereksinimlerine göre belirlenir.</p></div>

<div class="needrow">
  <div class="need"><b>Ürün değişiminde ayar hazırlığı</b>
    <p>Reçete her değiştiğinde çizgilerin yeniden ölçülüp ayarlanması, hattın duruş süresini uzatır.</p></div>
  <div class="need"><b>Kenar ve merkez hizalama</b>
    <p>Malzemenin kenarı ya da ekseni, yerleştirme ve kesim kalitesini doğrudan belirler.</p></div>
  <div class="need"><b>Çoklu referans yönetimi</b>
    <p>Aynı anda birden çok çizgi gerektiğinde referansların tümünün tek reçeteden yönetilmesi gerekir.</p></div>
</div>

<div class="applead">
  <article>
    <div class="appvis"><?php echo servo_get_svg( 'uygulama-lastik' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>LASTİK VE KOMPOZİT MALZEMELER</h3>
    <p class="appsym">Takviye katmanlarının serim genişliği üründen ürüne değişir; her geçişte kenar ve
      simetri referansının yeniden kurulması gerekir. Referans çizgileri, uygulama yüzeyinin geometrisine
      göre düzenlenir.</p>
    <p class="appkey"><em>Belirleyici olan</em>Katman genişliği, simetri ekseni, uygulama yüzeyinin
      geometrisi ve çalışma mesafesi.</p>
    <p class="appscale">ÖRNEK KURULUM<br><b>Core Precisor</b> · K4 çift kanal (1 sabit + 2 hareketli) · <b>1.600 mm</b> strok</p>
    <a class="appnote" href="<?php echo esc_url( servo_post_url( 'lastik', 'uygulama' ) ); ?>">Uygulama sayfasını görün →</a>
  </article>
  <article>
    <div class="appvis"><?php echo servo_get_svg( 'uygulama-nonwoven' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>HİJYENİK ÜRÜNLER VE NONWOVEN</h3>
    <p class="appsym">Çok katmanlı yapıda her katmanın kenar ve eksen referansının tutarlı olması gerekir;
      ürün ölçüsü ise sık değişir. Ölçü değiştiğinde hizalama konumları reçeteye göre yeniden oluşturulur.</p>
    <p class="appkey"><em>Belirleyici olan</em>Katman sayısı, çalışma genişliği, eşzamanlı çizgi sayısı ve
      dokunmamış yüzeyin optik davranışı.</p>
    <p class="appscale">ÖRNEK KURULUM<br><b>Shuttle Precisor</b> · <b>26 lazer</b> · <b>2.300 mm</b> strok</p>
    <a class="appnote" href="<?php echo esc_url( servo_post_url( 'nonwoven', 'uygulama' ) ); ?>">Uygulama sayfasını görün →</a>
  </article>
  <article>
    <div class="appvis"><?php echo servo_get_svg( 'uygulama-ambalaj' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>AMBALAJ VE KAĞIT</h3>
    <p class="appsym">Farklı ebatlar arasındaki geçişte kılavuzların ve işlem elemanlarının ayarı hattı
      bekletir. Ortak bir konum referansı, ebatlar arasında aynı ayar disiplinini korur.</p>
    <p class="appkey"><em>Belirleyici olan</em>Ebat çeşitliliği, reçete değişim sıklığı ve eşzamanlı
      görünmesi gereken çizgi sayısı.</p>
    <p class="appscale">ÖRNEK KURULUM<br><b>Shuttle Precisor</b> · <b>16 lazer</b> · <b>1.800 mm</b> strok</p>
    <a class="appnote" href="<?php echo esc_url( servo_post_url( 'ambalaj', 'uygulama' ) ); ?>">Uygulama sayfasını görün →</a>
  </article>
</div>

<p class="rowlab2">Diğer uygulama alanları</p>
<div class="application-grid">
  <article><div class="appvis"><?php echo servo_get_svg( 'uygulama-film' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>FİLM VE FOLYO</h3>
    <p>Parlak ve saydam yüzeylerde çizginin okunabilirliği, yüzeyden ve ortam aydınlatmasından etkilenir.
      <span style="display:block;margin-top:8px;font-size:14.5px;color:#64758a">Belirleyici olan: yüzey optiği, lazer rengi ve çalışma mesafesi.</span></p></article>
  <article><div class="appvis"><?php echo servo_get_svg( 'uygulama-tekstil' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>TEKSTİL VE TEKNİK KUMAŞLAR</h3>
    <p>Serim ve katman yerleştirmede referansın kumaşın tanımlı eksenine göre kurulması gerekir.
      <span style="display:block;margin-top:8px;font-size:14.5px;color:#64758a">Belirleyici olan: serim genişliği, katman düzeni ve montaj yüksekliği.</span></p></article>
  <article><div class="appvis"><?php echo servo_get_svg( 'uygulama-etiket' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><h3>ETİKET VE YAPIŞKANLI BANT</h3>
    <p>Dar çizgi aralıkları ve sık reçete değişimi, ayar hazırlığını üretimin kendisinden uzun hâle getirebilir.
      <span style="display:block;margin-top:8px;font-size:14.5px;color:#64758a">Belirleyici olan: çizgiler arası mesafe, eşzamanlı çizgi sayısı ve mekanik yerleşim.</span></p></article>
</div>

<p class="appfoot" style="margin-top:26px">Bu yaklaşımın üç farklı segmentteki gerçek kurulumları için <a href="<?php echo esc_url( servo_post_url( 'referanslar' ) ); ?>">vaka çalışmalarına</a> bakabilirsiniz.</p><p class="appfoot" style="margin-top:26px">Montaj, devreye alma ve sonrasındaki destek kapsamı için <a href="<?php echo esc_url( servo_post_url( 'servis' ) ); ?>">servis ve devreye alma sayfasına</a> göz atın.</p>
<p class="appfoot">Listede kendi prosesinizi görmüyorsanız: hizalama ihtiyacı çoğu zaman sektörden bağımsızdır.
  <a href="<?php echo esc_url( servo_post_url( 'iletisim' ) ); ?>">Uygulamanızı birlikte değerlendirelim →</a></p>
</div></section>
