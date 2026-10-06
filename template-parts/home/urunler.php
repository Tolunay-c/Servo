<?php
/**
 * Ana sayfa: Ürün ailesi (#sistemler): Core ve Shuttle kartları.
 *
 * Markup _design/index.html ile aynıdır. Metin ve görseller 7. adımda
 * ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section" id="sistemler"><div class="wrap"><div class="section-head"><p class="label"><bdi lang="en">SERVOMOTION</bdi> ÜRÜN AİLESİ</p><h2>YÜKSEK HASSASİYETLİ<br>LAZER KONUMLANDIRMA ÇÖZÜMLERİ</h2><p>Hassasiyetin, hızın ve esnekliğin ön planda olduğu endüstriyel hatlar için geliştirilmiş yenilikçi konumlandırma mimarileri.</p></div><div class="products">
<article class="product"><div class="visual"><img src="<?php echo esc_url( servo_asset_uri( 'assets/img/core-precisor.jpg' ) ); ?>" width="1000" height="915" alt="CORE PRECISOR kapalı gövde" loading="lazy"></div><div class="product-content"><span class="label">KOMPAKT GÖVDE · ESNEK HAREKET</span><h3>CORE PRECISOR</h3><p>CORE PRECISOR, kompakt gövdesi ve esnek hareket seçenekleriyle üretim hattınızın hizalama gereksinimlerine uyum sağlar. Lazerleri bağımsız veya simetrik olarak konumlandırarak malzeme yerleştirme ve makine ayarlarında hassas, tekrarlanabilir referanslar oluşturur.</p><div class="specs"><span><b>6</b>hareket konfigürasyonu</span><span><b>600–4.000 mm</b>gövde uzunluğu</span></div><a class="btn" href="<?php echo esc_url( servo_post_url( 'core-precisor' ) ); ?>" aria-label="CORE PRECISOR ürün detayları">ÜRÜN DETAYLARI</a></div></article>
<article class="product"><div class="visual"><?php echo servo_get_svg( 'shuttle-sema' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?></div><div class="product-content"><span class="label">MEKİK MİMARİSİ · ÇOKLU KONUMLANDIRMA</span><h3>SHUTTLE PRECISOR</h3><p>SHUTTLE PRECISOR, çok sayıda lazer referansı gerektiren uygulamalar için tek servo eksenle konumlandırma sunar. Mekik, lazer arabalarını sırayla hedeflerine taşır; konumlarında kilitlenen arabalar, tüm referans çizgilerinin çalışma boyunca birlikte kullanılmasını sağlar.</p><div class="specs"><span><b>5–40+</b>lazer arabası*</span><span><b>1 servo</b>üç raylı mimari</span></div><a class="btn" href="<?php echo esc_url( servo_post_url( 'shuttle-precisor' ) ); ?>" aria-label="SHUTTLE PRECISOR ürün detayları">ÜRÜN DETAYLARI</a></div></article></div><p class="note">* SHUTTLE PRECISOR lazer kapasitesi, hareket mesafesi ve rezerve alanı uygulama gereksinimlerine göre belirlenir.</p></div></section>
