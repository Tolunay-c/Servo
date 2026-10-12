<?php
/**
 * Shuttle Precisor: üç raylı mimari (section.rails), kartlar ve adım satırları.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="rails">
  <div class="wrap">
<?php
servo_urun_sec_hd( 'mimari' );
servo_urun_kartlar( 'mimari', 'div' );
servo_urun_dongu( 'mimari' );
?>
  </div>
</section>
