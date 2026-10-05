<?php
/**
 * Tema kurulumu: theme supports, menüler, görsel boyutları.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tema desteklerini, menü konumlarını ve görsel boyutlarını tanımlar.
 */
function servo_setup() {
	load_theme_textdomain( 'servomotion', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'ana_menu'    => __( 'Ana menü', 'servomotion' ),
			'footer_menu' => __( 'Footer menüsü (üst öğeler kolon başlığı)', 'servomotion' ),
		)
	);

	// Ana sayfa / ürün hero görseli (tasarımda 1000×915, kırpılmaz).
	add_image_size( 'servo-hero', 1000, 915, false );
	// Saha fotoğrafları (tasarımda 1600×1000, yatay).
	add_image_size( 'servo-foto', 1600, 1000, true );
}
add_action( 'after_setup_theme', 'servo_setup' );

/**
 * Yeni görsel boyutlarını medya ekleme ekranında seçilebilir yapar.
 *
 * @param array $sizes Mevcut boyutlar.
 * @return array
 */
function servo_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'servo-hero' => __( 'Hero görseli', 'servomotion' ),
			'servo-foto' => __( 'Saha fotoğrafı', 'servomotion' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'servo_image_size_names' );
