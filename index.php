<?php
/**
 * Yedek şablon.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main>
	<?php
	while ( have_posts() ) :
		the_post();
		the_title( '<h2>', '</h2>' );
		the_excerpt();
	endwhile;
	?>
</main>
<?php
get_footer();
