<?php
/**
 * SCF/ACF ayarları: Local JSON ve Options Page.
 *
 * Secure Custom Fields, ACF ile aynı API'yi kullanır. Bu dosyadaki her şey
 * eklenti kapalıyken sessizce devre dışı kalır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Alan gruplarının kaydedileceği Local JSON klasörü.
 *
 * @return string
 */
function servo_acf_json_save_point() {
	return get_template_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'servo_acf_json_save_point' );

/**
 * Alan gruplarının yükleneceği Local JSON klasörleri.
 *
 * @param string[] $paths Mevcut yollar.
 * @return string[]
 */
function servo_acf_json_load_point( $paths ) {
	unset( $paths[0] );
	$paths[] = get_template_directory() . '/acf-json';

	return $paths;
}
add_filter( 'acf/settings/load_json', 'servo_acf_json_load_point' );

/**
 * Site Ayarları Options Page'ini kaydeder.
 *
 * post_id, servo_options_id() ile verilir; 2. fazda dile göre ayrılacak.
 */
function servo_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Site Ayarları', 'servomotion' ),
			'menu_title' => __( 'Site Ayarları', 'servomotion' ),
			'menu_slug'  => 'servo-site-ayarlari',
			'capability' => 'manage_options',
			'post_id'    => servo_options_id(),
			'position'   => 59,
			'icon_url'   => 'dashicons-admin-settings',
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'servo_register_options_page' );
