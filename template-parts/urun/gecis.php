<?php
/**
 * Ürün sayfaları: diğer ürüne geçiş bandı (section.cross), Core ve Shuttle'da ortak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_ust    = servo_field( 'gecis_ust' );
$servo_baslik = servo_field( 'gecis_baslik' );
$servo_metin  = servo_field( 'gecis_metin' );
$servo_buton  = servo_field( 'gecis_buton' );

if ( ! $servo_baslik && ! $servo_metin && empty( $servo_buton['url'] ) ) {
	return;
}
?>
<section class="cross">
  <div class="wrap cross-in">
    <div>
<?php if ( $servo_ust ) : ?>
      <p class="eyebrow" style="color:#0B0F14;opacity:.6"><?php echo servo_urun_duz( $servo_ust ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
<?php if ( $servo_baslik ) : ?>
      <h2><?php echo servo_urun_duz( $servo_baslik ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
<?php endif; ?>
<?php if ( $servo_metin ) : ?>
      <p><?php echo servo_urun_metin( $servo_metin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
    </div>
<?php if ( ! empty( $servo_buton['url'] ) ) : ?>
    <a class="btn btn-k"<?php echo servo_link_atts( $servo_buton ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo servo_urun_duz( $servo_buton['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
<?php endif; ?>
  </div>
</section>
