<?php
/**
 * Ürün sayfaları: iletişim bandı (section.cta#iletisim), Core ve Shuttle'da ortak.
 *
 * E-posta ve telefon Site Ayarları'ndan gelir; başlık, metin, e-posta konusu
 * ve ek butonlar ürünün kendi alanlarındandır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_eposta   = servo_option( 'eposta' );
$servo_telefon  = servo_option( 'telefon' );
$servo_konu     = servo_field( 'cta_konu' );
$servo_baslik   = servo_field( 'cta_baslik' );
$servo_metin    = servo_field( 'cta_metin' );
$servo_butonlar = (array) servo_field( 'cta_butonlar', false, array() );
?>
<section class="cta" id="iletisim">
  <div class="wrap">
<?php if ( $servo_baslik ) : ?>
    <h2><?php echo servo_urun_duz( $servo_baslik ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
<?php endif; ?>
<?php if ( $servo_metin ) : ?>
    <p><?php echo servo_urun_metin( $servo_metin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
    <div class="btns" style="justify-content:center">
<?php if ( $servo_eposta ) : ?>
      <a class="btn btn-y" href="<?php echo esc_url( 'mailto:' . $servo_eposta . ( $servo_konu ? '?subject=' . rawurlencode( $servo_konu ) : '' ) ); ?>"><?php echo esc_html( strtoupper( $servo_eposta ) ); ?></a>
<?php endif; ?>
<?php if ( $servo_telefon ) : ?>
      <a class="btn btn-o" href="<?php echo esc_url( servo_tel_href( $servo_telefon ) ); ?>"><?php echo esc_html( $servo_telefon ); ?></a>
<?php endif; ?>
<?php
foreach ( $servo_butonlar as $servo_b ) :
	if ( empty( $servo_b['link']['url'] ) ) {
		continue;
	}
	?>
      <a class="btn btn-o"<?php echo servo_link_atts( $servo_b['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo servo_urun_duz( $servo_b['link']['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
<?php endforeach; ?>
    </div>
  </div>
</section>
