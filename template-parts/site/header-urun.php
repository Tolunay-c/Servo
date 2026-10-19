<?php
/**
 * Tek tip site header'ı: <nav class="nav"> ve .tabs (tüm sayfalarda).
 *
 * Ürün hero'su (<header class="hero">) sayfa şablonundadır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<nav class="nav" aria-label="<?php esc_attr_e( 'Ana menü', 'servomotion' ); ?>"><div class="wrap nav-in"><?php get_template_part( 'template-parts/site/logo', null, array( 'variant' => 'urun' ) ); ?><?php get_template_part( 'template-parts/site/nav-aksiyon', null, array( 'panel' => 'ana-menu' ) ); ?><div class="tabs" id="ana-menu">
<?php
wp_nav_menu(
	array(
		'theme_location' => 'ana_menu',
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 2,
		'fallback_cb'    => false,
		'walker'         => new Servo_Nav_Walker(),
		'servo_variant'  => 'tab',
	)
);
?>
</div></div></nav>
