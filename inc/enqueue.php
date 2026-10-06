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
 * Geçerli sayfanın hangi tasarım asset grubunu kullandığını döndürür.
 *
 * Gruplar tasarımdaki sayfaların <head> yapısına karşılık gelir:
 * - home     : index.html (kendi gömülü stilleri, base.css yok)
 * - core     : core-precisor.html
 * - shuttle  : shuttle-precisor.html
 * - iletisim : iletisim.html
 * - kurumsal : kurumsal.html
 * - rehber   : rehber.html ve rehber-*.html (rehber.css)
 * - ic-sayfa : diğer tüm iç sayfalar (base.css + ortak gömülü stil)
 *
 * @return string
 */
function servo_asset_group() {
	if ( is_front_page() ) {
		return 'home';
	}

	$templates = array(
		'page-templates/core-precisor.php'    => 'core',
		'page-templates/shuttle-precisor.php' => 'shuttle',
		'page-templates/iletisim.php'         => 'iletisim',
		'page-templates/kurumsal.php'         => 'kurumsal',
	);

	foreach ( $templates as $template => $group ) {
		if ( is_page_template( $template ) ) {
			return $group;
		}
	}

	if ( is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() ) {
		return 'rehber';
	}

	return 'ic-sayfa';
}

/**
 * Ön yüz asset'lerini yükler.
 *
 * Yükleme sırası tasarımdaki sırayla aynıdır: fontlar (Google Fonts <link>
 * yerine), harici CSS dosyaları, ardından sayfanın gömülü stili. JS'ler
 * tasarımdaki <script> sırasıyla footer'da yüklenir.
 */
function servo_enqueue_assets() {
	$group = servo_asset_group();

	servo_enqueue_style( 'servo-fonts', 'assets/css/fonts.css' );

	switch ( $group ) {
		case 'home':
			servo_enqueue_style( 'servo-home', 'assets/css/home.css', array( 'servo-fonts' ) );
			break;

		case 'core':
			servo_enqueue_style( 'servo-core', 'assets/css/core.css', array( 'servo-fonts' ) );
			servo_enqueue_script( 'servo-core-sim', 'assets/js/core-sim.js' );
			servo_enqueue_script( 'servo-core-govde', 'assets/js/core-govde.js' );
			break;

		case 'shuttle':
			servo_enqueue_style( 'servo-shuttle', 'assets/css/shuttle.css', array( 'servo-fonts' ) );
			servo_enqueue_script( 'servo-shuttle-konfig', 'assets/js/shuttle-konfig.js' );
			break;

		case 'rehber':
			servo_enqueue_style( 'servo-rehber', 'assets/css/rehber.css', array( 'servo-fonts' ) );
			servo_enqueue_style( 'servo-brandfont', 'assets/css/brandfont.css', array( 'servo-rehber' ) );
			break;

		default: // ic-sayfa, iletisim, kurumsal.
			servo_enqueue_style( 'servo-base', 'assets/css/base.css', array( 'servo-fonts' ) );
			servo_enqueue_style( 'servo-brandfont', 'assets/css/brandfont.css', array( 'servo-base' ) );
			servo_enqueue_style( 'servo-' . $group, 'assets/css/' . $group . '.css', array( 'servo-brandfont' ) );

			if ( is_page_template( 'page-templates/sozluk.php' ) ) {
				servo_enqueue_script( 'servo-sozluk', 'assets/js/sozluk.js' );
			}
			break;
	}

	// Bülten: rehber sayfalarında bülten kodu sayfa script'inin içindedir.
	if ( 'rehber' === $group ) {
		if ( is_singular( 'post' ) ) {
			servo_enqueue_script( 'servo-rehber-yazi', 'assets/js/rehber-yazi.js' );
		} else {
			servo_enqueue_script( 'servo-rehber-liste', 'assets/js/rehber-liste.js' );
		}
	} else {
		servo_enqueue_script( 'servo-bulten', 'assets/js/bulten.js' );
	}

	if ( 'iletisim' === $group ) {
		servo_enqueue_script( 'servo-iletisim-ozet', 'assets/js/iletisim-ozet.js' );
	}

	servo_enqueue_script( 'servo-nav', 'assets/js/nav.js' );

	if ( 'iletisim' === $group ) {
		// Form uç noktası 9. adımda wp_add_inline_script ile bu dosyadan önce verilecek.
		servo_enqueue_script( 'servo-iletisim-gonder', 'assets/js/iletisim-gonder.js' );
	}
}
add_action( 'wp_enqueue_scripts', 'servo_enqueue_assets' );
