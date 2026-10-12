<?php
/**
 * Ürün sayfaları: lazer güvenliği ve uygunluk (Core ve Shuttle'da aynı).
 *
 * İçerik iki ürün için tek yerden, Site Ayarları > Ürün güvenliği
 * sekmesinden gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_etiket   = servo_option( 'guvenlik_etiket' );
$servo_baslik   = servo_option( 'guvenlik_baslik' );
$servo_maddeler = (array) servo_option( 'guvenlik_maddeler', array() );

if ( ! $servo_baslik && ! $servo_maddeler ) {
	return;
}
?>
<section class="section" style="background:#fff"><div class="wrap">
<div class="section-head"><?php if ( $servo_etiket ) : ?><p class="label"><?php echo esc_html( $servo_etiket ); ?></p><?php endif; ?><?php if ( $servo_baslik ) : ?><h2><?php echo servo_urun_duz( $servo_baslik ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2><?php endif; ?></div>
<?php if ( $servo_maddeler ) : ?>
<div class="symlist">
	<?php foreach ( $servo_maddeler as $servo_m ) : ?>
  <div class="sym"><b><?php echo servo_urun_duz( $servo_m['baslik'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></b><?php if ( $servo_m['metin'] ) : ?><p><?php echo servo_urun_metin( $servo_m['metin'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?></div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
</div></section>
