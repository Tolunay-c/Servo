<?php
/**
 * Uygulama alanı tekil şablonu.
 *
 * İçerik, kaydın "Sayfa blokları" (flexible content) alanından gelir.
 * "Kendi sayfası var" kapalı kayıtlar 404 döner (inc/bloklar.php).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main><?php
while ( have_posts() ) :
	the_post();
	servo_render_blocks();
endwhile;
?>
</main>
<?php
get_footer();
