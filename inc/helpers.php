<?php
/**
 * Yardımcı fonksiyonlar.
 *
 * Şablonlar SCF/ACF'ye doğrudan değil, bu helper'lar üzerinden erişir.
 * Böylece eklenti kapalıyken site çökmez ve Options alanlarının
 * dile göre ayrılması (Polylang, 2. faz) tek noktadan yönetilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Bir değerin "boş" sayılıp sayılmadığını döndürür.
 *
 * 0 ve '0' boş sayılmaz; teknik değerlerde (ör. 0 mm) anlamlı olabilirler.
 *
 * @param mixed $value Değer.
 * @return bool
 */
function servo_is_empty( $value ) {
	return null === $value || false === $value || '' === $value || array() === $value;
}

/**
 * Sayfa/yazı alanını okur.
 *
 * @param string    $name    Alan adı.
 * @param int|false $post_id Yazı ID'si; false ise geçerli yazı.
 * @param mixed     $default Alan boşsa veya eklenti kapalıysa dönecek değer.
 * @return mixed
 */
function servo_field( $name, $post_id = false, $default = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, $post_id );

	return servo_is_empty( $value ) ? $default : $value;
}

/**
 * Options Page'in post_id değerini döndürür.
 *
 * 2. fazda Polylang ile dile göre ayrım burada yapılır
 * (ör. 'options_' . pll_current_language()). Şimdilik tek dil.
 *
 * @return string
 */
function servo_options_id() {
	return apply_filters( 'servo_options_id', 'option' );
}

/**
 * Site Ayarları (Options Page) alanını okur.
 *
 * Şablonlarda get_field( 'x', 'option' ) yerine her zaman bu kullanılır.
 *
 * @param string $name    Alan adı.
 * @param mixed  $default Alan boşsa veya eklenti kapalıysa dönecek değer.
 * @return mixed
 */
function servo_option( $name, $default = null ) {
	return servo_field( $name, servo_options_id(), $default );
}

/**
 * Görünen telefon numarasından tel: bağlantısı üretir.
 *
 * @param string $phone Görünen numara (ör. '+90 212 577 25 67').
 * @return string Ör. 'tel:+902125772567'.
 */
function servo_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * Geçerli sayfanın ürün sayfası header'ını (.tabs) kullanıp kullanmadığını döndürür.
 *
 * @return bool
 */
function servo_is_product_layout() {
	return in_array( servo_asset_group(), array( 'core', 'shuttle' ), true );
}

/**
 * Belirli bir sayfa şablonunu kullanan ilk yayımlanmış sayfanın ID'sini döndürür.
 *
 * @param string $template Şablon yolu (ör. 'page-templates/referanslar.php').
 * @return int Bulunamazsa 0.
 */
function servo_template_page_id( $template ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $template, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	return $pages ? (int) $pages[0] : 0;
}

/**
 * Bir vakanın referanslar sayfasındaki çapa (id) değerini döndürür.
 *
 * Çapa başlıktan değil post slug'ından üretilir; başlık değişse de linkler
 * kırılmaz. Slug'lar ASCII tutulmalıdır (ör. "lastik").
 *
 * @param int|WP_Post $vaka Vaka ID'si veya nesnesi.
 * @return string
 */
function servo_vaka_anchor( $vaka ) {
	$vaka = get_post( $vaka );

	if ( ! $vaka || 'vaka' !== $vaka->post_type ) {
		return '';
	}

	return sanitize_html_class( rawurldecode( $vaka->post_name ) );
}

/**
 * Bir vakanın referanslar sayfasındaki adresini (#çapa ile) döndürür.
 *
 * Uygulama sayfalarındaki "örnek kurulum" linkleri, relationship alanından
 * gelen vaka ile bu fonksiyonu kullanır.
 *
 * @param int|WP_Post $vaka Vaka ID'si veya nesnesi.
 * @return string Referanslar sayfası veya vaka yoksa boş metin.
 */
function servo_vaka_url( $vaka ) {
	$anchor  = servo_vaka_anchor( $vaka );
	$page_id = servo_template_page_id( 'page-templates/referanslar.php' );

	if ( '' === $anchor || ! $page_id ) {
		return '';
	}

	return get_permalink( $page_id ) . '#' . $anchor;
}

/**
 * Tema içindeki bir dosyanın URL'sini döndürür.
 *
 * @param string $path Tema köküne göre yol (ör. 'assets/img/logo.svg').
 * @return string
 */
function servo_asset_uri( $path ) {
	return get_template_directory_uri() . '/' . ltrim( $path, '/' );
}

/**
 * Tema içindeki bir dosyanın sürüm numarasını (filemtime) döndürür.
 *
 * @param string $path Tema köküne göre yol.
 * @return string|null Dosya yoksa null.
 */
function servo_asset_version( $path ) {
	$file = get_template_directory() . '/' . ltrim( $path, '/' );

	return file_exists( $file ) ? (string) filemtime( $file ) : null;
}

/**
 * assets/img/svg/ altındaki bir SVG dosyasının içeriğini döndürür.
 *
 * Dosyalar temanın kendi, güvenilir dosyalarıdır; kullanıcı girdisi değildir.
 *
 * @param string $name Uzantısız dosya adı (ör. 'logo-isaret').
 * @return string Dosya yoksa boş metin.
 */
function servo_get_svg( $name ) {
	$name = sanitize_file_name( $name );
	$file = get_template_directory() . '/assets/img/svg/' . $name . '.svg';

	if ( '' === $name || ! file_exists( $file ) ) {
		return '';
	}

	return trim( (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}
