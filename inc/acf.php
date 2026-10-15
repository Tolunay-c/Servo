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
 * Site Ayarları Options Page'lerini kaydeder: Türkçe (options_tr) ve İngilizce (options_en).
 *
 * İngilizce sayfada boş bırakılan alanlar Türkçe değeri kullanır (servo_option()).
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
			'post_id'    => servo_options_id( 'tr' ),
			'position'   => 59,
			'icon_url'   => 'dashicons-admin-settings',
			'redirect'   => false,
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title'  => 'Site Settings (EN)',
			'menu_title'  => 'Site Settings (EN)',
			'menu_slug'   => 'servo-site-settings-en',
			'parent_slug' => 'servo-site-ayarlari',
			'capability'  => 'manage_options',
			'post_id'     => servo_options_id( 'en' ),
		)
	);
}

/**
 * İngilizce ayarlar sayfasının üstüne kısa bir yönlendirme notu koyar.
 */
function servo_en_ayar_notu() {
	$ekran = get_current_screen();

	if ( ! $ekran || false === strpos( (string) $ekran->id, 'servo-site-settings-en' ) ) {
		return;
	}

	echo '<div class="notice notice-info"><p>' . esc_html__( 'İngilizce sitenin ayarları. Boş bıraktığınız alanlar Türkçe ayarlardaki değeri kullanır; telefon, e-posta, adres gibi çevrilmeyen alanları sadece "Site Ayarları" sayfasında doldurmanız yeterlidir.', 'servomotion' ) . '</p></div>';
}
add_action( 'admin_notices', 'servo_en_ayar_notu' );
add_action( 'acf/init', 'servo_register_options_page' );
