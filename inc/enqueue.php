<?php
/**
 * CSS/JS yükleme.
 *
 * Tüm asset'ler buradan, filemtime() sürümüyle yüklenir. Sayfaya özel
 * dosyalar ilgili şablonda koşullu olarak eklenir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tema içindeki bir CSS dosyasını yükler. Dosya yoksa hiçbir şey yapmaz,
 * böylece 404 veren asset oluşmaz.
 *
 * @param string   $handle Handle (servo- önekli).
 * @param string   $path   Tema köküne göre yol (ör. 'assets/css/base.css').
 * @param string[] $deps   Bağımlılıklar.
 */
function servo_enqueue_style( $handle, $path, $deps = array() ) {
	$version = servo_asset_version( $path );

	if ( null === $version ) {
		return;
	}

	wp_enqueue_style( $handle, servo_asset_uri( $path ), $deps, $version );
}

/**
 * Tema içindeki bir JS dosyasını footer'da yükler. Dosya yoksa hiçbir şey yapmaz.
 *
 * @param string   $handle Handle (servo- önekli).
 * @param string   $path   Tema köküne göre yol (ör. 'assets/js/nav.js').
 * @param string[] $deps   Bağımlılıklar.
 */
function servo_enqueue_script( $handle, $path, $deps = array() ) {
	$version = servo_asset_version( $path );

	if ( null === $version ) {
		return;
	}

	wp_enqueue_script( $handle, servo_asset_uri( $path ), $deps, $version, array( 'in_footer' => true ) );
}

/**
 * Ön yüz asset'lerini yükler.
 *
 * Ortak ve sayfaya özel dosyalar 3. adımda (asset'ler) buraya eklenecek.
 */
function servo_enqueue_assets() {
}
add_action( 'wp_enqueue_scripts', 'servo_enqueue_assets' );
