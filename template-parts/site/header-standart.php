<?php
/**
 * Standart header: <header class="nav">.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="nav"><div class="wrap"><?php get_template_part( 'template-parts/site/logo' ); ?><nav aria-label="<?php esc_attr_e( 'Ana menü', 'servomotion' ); ?>">
<?php
wp_nav_menu(
	array(
		'theme_location' => 'ana_menu',
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 2,
		'fallback_cb'    => false,
		'walker'         => new Servo_Nav_Walker(),
	)
);
?>
</nav></div></header>
