<?php
/**
 * Ürün sayfaları: teknik özellikler (section#teknik > .spec-grid), Core ve Shuttle'da ortak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section id="teknik">
  <div class="wrap">
    <?php servo_urun_sec_hd( 'teknik' ); ?>
    <?php servo_urun_teknik_tablolar(); ?>
    <?php $servo_not = servo_field( 'teknik_not', false, '' ); ?>
    <?php if ( $servo_not ) : ?>
    <p class="appfoot" style="margin-top:26px"><?php echo servo_urun_metin( $servo_not ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
    <?php endif; ?>
  </div>
</section>
