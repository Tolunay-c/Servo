<?php
/**
 * Core Precisor: özellikler (.fgrid > .fbox).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<?php $servo_kutular = (array) servo_field( 'secim_kutular', false, array() ); ?>
<section>
  <div class="wrap">
    <?php servo_urun_sec_hd( 'secim' ); ?>
<?php if ( $servo_kutular ) : ?>
    <div class="fgrid">
	<?php foreach ( $servo_kutular as $servo_k ) : ?>
      <div class="fbox"><div class="t"><?php echo servo_urun_duz( $servo_k['etiket'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><div class="v"><?php echo servo_urun_metin( $servo_k['deger'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php if ( $servo_k['metin'] ) : ?><p><?php echo servo_urun_metin( $servo_k['metin'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?></div>
	<?php endforeach; ?>
    </div>
<?php endif; ?>
  </div>
</section>
