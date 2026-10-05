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

	return (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}
