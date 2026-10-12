<?php
/**
 * Varsayılan sayfa şablonu.
 *
 * İçerik, sayfanın "Sayfa blokları" (flexible content) alanından gelir.
 * Henüz blok eklenmemiş sayfalarda başlık ve editör içeriği sade biçimde
 * gösterilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main><?php
while ( have_posts() ) :
	the_post();

	if ( ! servo_render_blocks() ) :
		?>
<section class="section"><div class="wrap"><div class="section-head"><h2><?php the_title(); ?></h2></div><?php the_content(); ?></div></section>
		<?php
	endif;
endwhile;
?>
</main>
<?php
get_footer();
