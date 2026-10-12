<?php
/**
 * Ana sayfa şablonu.
 *
 * İçerik, sayfanın "Sayfa blokları" (flexible content) alanından gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main><?php servo_render_blocks(); ?></main>
<?php
get_footer();
