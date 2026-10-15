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
	$path    = servo_js_yolu( $path );
	$version = servo_asset_version( $path );

	if ( null === $version ) {
		return;
	}

	wp_enqueue_script( $handle, servo_asset_uri( $path ), $deps, $version, array( 'in_footer' => true ) );
}

/**
 * Geçerli sayfanın hangi tasarım asset grubunu kullandığını döndürür.
 *
 * - core    : Core Precisor şablonu (core.css)
 * - shuttle : Shuttle Precisor şablonu (shuttle.css)
 * - rehber  : rehber listesi, rehber yazıları ve yazı arşivleri (rehber.css)
 * - bloklar : diğer her şey; flexible content sayfaları, 404 ve arama
 *             (base.css + brandfont.css + bloklar.css)
 *
 * @return string
 */
function servo_asset_group() {
	if ( is_page_template( 'page-templates/core-precisor.php' ) ) {
		return 'core';
	}

	if ( is_page_template( 'page-templates/shuttle-precisor.php' ) ) {
		return 'shuttle';
	}

	if ( is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() ) {
		return 'rehber';
	}

	return 'bloklar';
}

/**
 * Ön yüz asset'lerini yükler.
 *
 * Yükleme sırası tasarımdaki sırayla aynıdır: fontlar (Google Fonts <link>
 * yerine), harici CSS dosyaları, ardından sayfanın stilleri. JS'ler
 * tasarımdaki <script> sırasıyla footer'da yüklenir. Sözlük ve uygulama
 * özeti script'leri sadece ilgili blok sayfadaysa yüklenir. Menü sığdırma
 * stilleri (navfit.css) tasarımdaki gibi her sayfada en son yüklenir.
 */
function servo_enqueue_assets() {
	$group = servo_asset_group();
	$ozet  = 'bloklar' === $group && servo_page_has_block( 'uygulama_ozeti' );
	$talep = 'bloklar' === $group && servo_page_has_block( 'talep_formu' );

	$son = 'servo-fonts';

	servo_enqueue_style( 'servo-fonts', 'assets/css/fonts.css' );

	switch ( $group ) {
		case 'core':
			servo_enqueue_style( 'servo-core', 'assets/css/core.css', array( 'servo-fonts' ) );
			servo_enqueue_script( 'servo-core-sim', 'assets/js/core-sim.js' );
			servo_enqueue_script( 'servo-core-govde', 'assets/js/core-govde.js' );
			$son = 'servo-core';
			break;

		case 'shuttle':
			servo_enqueue_style( 'servo-shuttle', 'assets/css/shuttle.css', array( 'servo-fonts' ) );
			servo_enqueue_script( 'servo-shuttle-konfig', 'assets/js/shuttle-konfig.js' );
			$son = 'servo-shuttle';
			break;

		case 'rehber':
			servo_enqueue_style( 'servo-rehber', 'assets/css/rehber.css', array( 'servo-fonts' ) );
			servo_enqueue_style( 'servo-brandfont', 'assets/css/brandfont.css', array( 'servo-rehber' ) );

			// rehber.html'in <style id="servomotion-brand-font"> bloğunda brandfont.css'te
			// olmayan tek kural; tasarımda sadece liste sayfasında var.
			if ( ! is_singular() ) {
				wp_add_inline_style( 'servo-brandfont', '.nav .brand:hover .smk{color:#fff}' );
			}
			$son = 'servo-brandfont';
			break;

		default:
			servo_enqueue_style( 'servo-base', 'assets/css/base.css', array( 'servo-fonts' ) );
			servo_enqueue_style( 'servo-brandfont', 'assets/css/brandfont.css', array( 'servo-base' ) );
			servo_enqueue_style( 'servo-bloklar', 'assets/css/bloklar.css', array( 'servo-brandfont' ) );
			$son = 'servo-bloklar';

			if ( is_singular() && servo_field( 'iki_yana_yasla', get_queried_object_id(), false ) ) {
				servo_enqueue_style( 'servo-hizalama', 'assets/css/hizalama.css', array( 'servo-bloklar' ) );
				$son = 'servo-hizalama';
			}

			// Servis kanalları, talep formu ve doküman blokları (servis.css).
			if ( servo_page_has_block( 'servis_kanallari' ) || servo_page_has_block( 'talep_formu' ) ) {
				servo_enqueue_style( 'servo-servis', 'assets/css/servis.css', array( $son ) );
				$son = 'servo-servis';
			}

			if ( servo_page_has_block( 'sozluk' ) ) {
				servo_enqueue_script( 'servo-sozluk', 'assets/js/sozluk.js' );
			}
			break;
	}

	servo_enqueue_style( 'servo-navfit', 'assets/css/navfit.css', array( $son ) );

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

	if ( $ozet ) {
		servo_enqueue_script( 'servo-iletisim-ozet', 'assets/js/iletisim-ozet.js' );
	}

	if ( $talep ) {
		servo_enqueue_script( 'servo-talep', 'assets/js/talep.js' );
	}

	servo_enqueue_script( 'servo-nav', 'assets/js/nav.js' );

	if ( $ozet ) {
		servo_enqueue_script( 'servo-iletisim-gonder', 'assets/js/iletisim-gonder.js' );

		// Form uç noktası (inc/formlar.php); alıcı adresi yoksa verilmez.
		$form = servo_form_script();
		if ( '' !== $form ) {
			wp_add_inline_script( 'servo-iletisim-gonder', $form, 'before' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'servo_enqueue_assets' );
