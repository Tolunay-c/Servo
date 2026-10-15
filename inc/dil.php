<?php
/**
 * Çok dillilik (Polylang): dil yardımcıları, dil düğmesi, EN adres ön ekleri ve EN script'leri.
 *
 * Türkçe varsayılan dildir ve adreslerinde ön ek yoktur; İngilizce /en/ altındadır.
 * Tema Polylang kapalıyken de çalışır (her şey Türkçe kalır).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Geçerli dilin kısa adı (tr | en).
 *
 * Ön yüzde Polylang'in dili, yönetim ekranında (ve WP-CLI'de) Türkçe. İçerik
 * betikleri dili `servo_dil_zorla()` ile belirler.
 *
 * @return string
 */
function servo_dil() {
	global $servo_dil_zorla;

	if ( ! empty( $servo_dil_zorla ) ) {
		return $servo_dil_zorla;
	}

	if ( ! is_admin() && function_exists( 'pll_current_language' ) ) {
		$dil = pll_current_language();

		if ( is_string( $dil ) && '' !== $dil ) {
			return $dil;
		}
	}

	return 'tr';
}

/**
 * Dili geçici olarak sabitler (içerik betikleri ve testler için). Boş metin sabitlemeyi kaldırır.
 *
 * @param string $dil tr | en | ''.
 */
function servo_dil_zorla( $dil ) {
	global $servo_dil_zorla;

	$servo_dil_zorla = $dil;
}

/**
 * Sayfa ön yüzü İngilizce mi?
 *
 * @return bool
 */
function servo_en() {
	return 'en' === servo_dil();
}

/**
 * Kayıt ID'lerinden verilen dildekileri döndürür.
 *
 * Polylang'in `lang` sorgu değişkeni WP-CLI'de çalışmadığı (ve Polylang'in dil
 * taksonomisi bu bağlamda kayıtlı olmadığı) için dil, kayıt başına okunur.
 *
 * @param int[]  $idler Kayıt ID'leri.
 * @param string $dil   Dil kısa adı.
 * @return int[]
 */
function servo_dil_filtre( $idler, $dil ) {
	if ( ! function_exists( 'pll_get_post_language' ) ) {
		return array_map( 'intval', (array) $idler );
	}

	return array_values(
		array_filter(
			array_map( 'intval', (array) $idler ),
			function ( $id ) use ( $dil ) {
				return pll_get_post_language( $id ) === $dil;
			}
		)
	);
}

/**
 * Slug'ı verilen sayfayı (veya yazıyı) geçerli dilde bulur.
 *
 * Polylang aynı slug'ı iki dilde kullanmaya izin verir (ör. core-precisor);
 * get_page_by_path() hangisini döndüreceğini garanti etmez.
 *
 * @param string      $slug      Slug.
 * @param string      $post_type Kayıt türü.
 * @param string|null $dil       Dil (boşsa geçerli dil).
 * @return WP_Post|null
 */
function servo_kayit_bul( $slug, $post_type = 'page', $dil = null ) {
	$dil  = $dil ? $dil : servo_dil();
	$bul  = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$bul  = servo_dil_filtre( $bul, $dil );

	return $bul ? get_post( $bul[0] ) : null;
}

/**
 * Tema çevirisini (languages/servomotion-<locale>.mo) yükler.
 *
 * Kaynak metinler Türkçe olduğu için tr_TR'de çeviri dosyası yoktur. Dil değişince
 * (Polylang'in dili belirlemesi, switch_to_locale) çeviri yeniden yüklenir.
 *
 * @param string|null $locale Yerel ayar (boşsa geçerli).
 */
function servo_ceviri_yukle( $locale = null ) {
	$locale = $locale ? $locale : determine_locale();

	unload_textdomain( 'servomotion' );

	$mo = get_template_directory() . '/languages/servomotion-' . $locale . '.mo';

	if ( 'tr_TR' !== $locale && is_readable( $mo ) ) {
		load_textdomain( 'servomotion', $mo, $locale );
	}
}
add_action( 'after_setup_theme', 'servo_ceviri_yukle', 20 );
add_action(
	'pll_language_defined',
	function ( $slug, $dil ) {
		servo_ceviri_yukle( is_object( $dil ) && isset( $dil->locale ) ? $dil->locale : null );
	},
	10,
	2
);
add_action( 'switch_locale', 'servo_ceviri_yukle' );
add_action(
	'restore_previous_locale',
	function ( $locale ) {
		servo_ceviri_yukle( $locale );
	}
);

/* ------------------------------------------------------------------
 * Dil öznitelikleri, Site Ayarları
 * ---------------------------------------------------------------- */

/**
 * <html lang>: Türkçe için "tr" (tasarımdaki gibi), İngilizce için "en-GB".
 *
 * @param string $cikti language_attributes metni.
 * @return string
 */
function servo_dil_ozniteligi( $cikti ) {
	return str_replace( 'lang="tr-TR"', 'lang="tr"', $cikti );
}
add_filter( 'language_attributes', 'servo_dil_ozniteligi', 99 );

/* ------------------------------------------------------------------
 * Dil düğmesi (TR / EN)
 * ---------------------------------------------------------------- */

/**
 * Geçerli sayfanın diğer dildeki adresi.
 *
 * Çevirisi varsa çeviri; yoksa diğer dilin ana sayfası. Sadece İngilizce olan
 * "Uygulamalar" sayfasında Türkçe ana sayfanın #uygulama-alanlari bölümüne gider.
 *
 * @return array{0:string,1:string,2:string}|null Adres, dil kodu, dil adı.
 */
function servo_dil_dugmesi() {
	if ( ! function_exists( 'pll_the_languages' ) || ! function_exists( 'pll_home_url' ) ) {
		return null;
	}

	$hedef = servo_en() ? 'tr' : 'en';
	$url   = '';

	if ( servo_en() && is_page() ) {
		$ceviri = pll_get_post( get_queried_object_id(), 'tr' );
		if ( ! $ceviri && 'applications' === get_queried_object()->post_name ) {
			$url = pll_home_url( 'tr' ) . '#uygulama-alanlari';
		}
	}

	if ( '' === $url ) {
		$diller = pll_the_languages( array( 'raw' => 1, 'hide_if_no_translation' => 0 ) );
		$url    = is_array( $diller ) && isset( $diller[ $hedef ]['url'] ) ? $diller[ $hedef ]['url'] : pll_home_url( $hedef );
	}

	return array( $url, 'en' === $hedef ? 'EN' : 'TR', 'en' === $hedef ? 'English' : 'Türkçe' );
}

/**
 * Ana menünün sonuna TR/EN düğmesini ekler (tasarımdaki <a class="langlink">).
 *
 * @param string   $items Menü öğeleri.
 * @param stdClass $args  wp_nav_menu argümanları.
 * @return string
 */
function servo_dil_dugmesi_ekle( $items, $args ) {
	if ( empty( $args->theme_location ) || 'ana_menu' !== $args->theme_location ) {
		return $items;
	}

	$d = servo_dil_dugmesi();

	if ( ! $d ) {
		return $items;
	}

	return $items . '<a class="langlink" href="' . esc_url( $d[0] ) . '" hreflang="' . esc_attr( strtolower( $d[1] ) ) . '" lang="' . esc_attr( strtolower( $d[1] ) ) . '" title="' . esc_attr( $d[2] ) . '">' . esc_html( $d[1] ) . '</a>';
}
add_filter( 'wp_nav_menu_items', 'servo_dil_dugmesi_ekle', 10, 2 );

/* ------------------------------------------------------------------
 * İngilizce adres ön ekleri (dar kapsam: yalnızca /en/ adresleri)
 * ---------------------------------------------------------------- */

/**
 * İngilizce adreslerde Türkçe ön ekler değişir; Türkçe adreslere dokunulmaz.
 *
 * - rehber yazıları : /en/rehber/<slug>/        → /en/guide/<slug>/
 * - rehber kategorisi: /en/rehber/kategori/<t>/  → /en/guide/category/<t>/
 * - uygulamalar      : /en/uygulama/<slug>/      → /en/application/<slug>/
 *
 * Hem yeniden yazma kuralları (gelen istek) hem de bağlantı üreten filtreler
 * (giden adres) aynı eşlemeyi kullanır. Kurallar yalnızca "en/" ile başlayan
 * anahtarlarda değişir.
 *
 * @return array<string,string> Eski ön ek => yeni ön ek (eğik çizgisiz, "en/" ile).
 */
function servo_en_onekler() {
	return array(
		'en/rehber/kategori' => 'en/guide/category',
		'en/rehber'          => 'en/guide',
		'en/uygulama'        => 'en/application',
	);
}

/**
 * Polylang'in yeniden yazma kuralı anahtarlarındaki dil öneki "(en)/" biçimindedir
 * (dil, sorguya $matches[1] olarak geçer); "en/" biçimi de aynı eşlemeye dahildir.
 *
 * @return array<string,string> Eski anahtar öneki => yeni anahtar öneki.
 */
function servo_en_kural_onekleri() {
	$out = array();

	foreach ( servo_en_onekler() as $eski => $yeni ) {
		$out[ '(' . substr( $eski, 0, 2 ) . ')' . substr( $eski, 2 ) ] = '(' . substr( $yeni, 0, 2 ) . ')' . substr( $yeni, 2 );
		$out[ $eski ] = $yeni;
	}

	return $out;
}

/**
 * Yeniden yazma kurallarındaki "en/…" ön eklerini değiştirir.
 *
 * @param array $kurallar Kurallar (regex => sorgu).
 * @return array
 */
function servo_en_kurallar( $kurallar ) {
	$yeni = array();

	foreach ( (array) $kurallar as $regex => $sorgu ) {
		foreach ( servo_en_kural_onekleri() as $eski => $yeni_onek ) {
			if ( 0 === strpos( $regex, $eski . '/' ) || $regex === $eski ) {
				$regex = $yeni_onek . substr( $regex, strlen( $eski ) );
				break;
			}
		}
		$yeni[ $regex ] = $sorgu;
	}

	return $yeni;
}
add_filter( 'rewrite_rules_array', 'servo_en_kurallar', 999 );

/**
 * Üretilen bir adresteki /en/ ön ekini değiştirir.
 *
 * @param string $url Adres.
 * @return string
 */
function servo_en_adres( $url ) {
	// home_url() ön yüzde Polylang'le dil öneki alır; kök adres için seçenek okunur.
	$ana = trailingslashit( get_option( 'home' ) );

	foreach ( servo_en_onekler() as $eski => $yeni ) {
		$eski_tam = $ana . $eski . '/';
		if ( 0 === strpos( $url, $eski_tam ) ) {
			return $ana . $yeni . '/' . substr( $url, strlen( $eski_tam ) );
		}
	}

	return $url;
}
add_filter( 'post_link', 'servo_en_adres', 99 );
add_filter( 'post_type_link', 'servo_en_adres', 99 );
add_filter( 'term_link', 'servo_en_adres', 99 );

/**
 * Aynı slug'ın farklı dillerde kullanılmasına izin verir (ücretsiz Polylang'de yok).
 *
 * WordPress slug'ı benzersiz yapar (core-precisor → core-precisor-2). Çakışan
 * kayıtların hepsi kaydın dilinden farklı bir dildeyse özgün slug korunur.
 * Dil atanmamış kayıtlarda ve Türkçe kayıtlar arasında davranış değişmez.
 *
 * @param string $slug          Benzersizleştirilmiş slug.
 * @param int    $post_id       Kayıt.
 * @param string $post_status   Durum.
 * @param string $post_type     Tür.
 * @param int    $post_parent   Üst kayıt.
 * @param string $original_slug İstenen slug.
 * @return string
 */
function servo_slug_paylas( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug ) {
	if ( $slug === $original_slug || ! function_exists( 'pll_get_post_language' ) ) {
		return $slug;
	}

	$dil = pll_get_post_language( $post_id );

	if ( ! $dil ) {
		return $slug;
	}

	global $wpdb;

	$cakisan = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s AND post_parent = %d AND ID != %d AND post_status NOT IN ('trash','auto-draft')",
			$original_slug,
			$post_type,
			$post_parent,
			$post_id
		)
	);

	foreach ( $cakisan as $id ) {
		$d = pll_get_post_language( (int) $id );
		if ( ! $d || $d === $dil ) {
			return $slug;
		}
	}

	return $original_slug;
}
add_filter( 'wp_unique_post_slug', 'servo_slug_paylas', 10, 6 );

/**
 * Aynı slug'ı paylaşan iki dildeki kayıttan, ana sorguda yalnızca geçerli dildekini bırakır.
 *
 * Slug paylaşımı (servo_slug_paylas) iki dilde aynı adlı kayıt yarattığında WordPress'in
 * ad (name/pagename) sorgusu ikisini birden döndürebilir; ön yüz yalnızca doğru dilin
 * kaydını göstermelidir.
 *
 * @param WP_Post[] $posts Sonuçlar.
 * @param WP_Query  $q     Sorgu.
 * @return WP_Post[]
 */
function servo_ana_sorgu_dil_filtresi( $posts, $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! is_array( $posts ) || count( $posts ) < 2 || ! function_exists( 'pll_get_post_language' ) ) {
		return $posts;
	}

	$dil = $q->get( 'lang' );
	$dil = is_string( $dil ) && '' !== $dil ? $dil : ( function_exists( 'pll_current_language' ) ? pll_current_language() : '' );

	if ( ! $dil ) {
		return $posts;
	}

	$ayni = array_values(
		array_filter(
			$posts,
			function ( $p ) use ( $dil ) {
				return pll_get_post_language( $p->ID ) === $dil;
			}
		)
	);

	return $ayni ? $ayni : $posts;
}
add_filter( 'the_posts', 'servo_ana_sorgu_dil_filtresi', 10, 2 );

/**
 * Sayfa sorgusu: iki dilde aynı slug'lı sayfa (ör. core-precisor) varsa geçerli dilin sayfası seçilir.
 *
 * WP_Query sayfayı get_page_by_path() ile bulur ve dil gözetmeden ilk kaydı alır; bu yüzden
 * the_posts süzgeci yetmez, sorgu doğrudan dilin sayfa ID'sine çevrilir.
 *
 * @param WP_Query $q Sorgu.
 */
function servo_sayfa_dil_sorgusu( $q ) {
	$slug = $q->get( 'pagename' );

	if ( is_admin() || ! $q->is_main_query() || ! is_string( $slug ) || '' === $slug || false !== strpos( $slug, '/' ) || ! function_exists( 'pll_current_language' ) ) {
		return;
	}

	$dil = pll_current_language();
	$p   = $dil ? servo_kayit_bul( $slug, 'page', $dil ) : null;

	if ( $p ) {
		$q->set( 'pagename', '' );
		$q->set( 'page_id', $p->ID );
	}
}
add_action( 'pre_get_posts', 'servo_sayfa_dil_sorgusu' );

/* ------------------------------------------------------------------
 * İngilizce script'ler
 * ---------------------------------------------------------------- */

/**
 * Script yolu: İngilizce sayfada tasarımın İngilizce script'i (assets/js/en/) varsa o kullanılır.
 *
 * @param string $yol Tema köküne göre yol (ör. 'assets/js/core-sim.js').
 * @return string
 */
function servo_js_yolu( $yol ) {
	if ( servo_en() ) {
		$en = preg_replace( '#^assets/js/#', 'assets/js/en/', $yol );

		if ( null !== servo_asset_version( $en ) ) {
			return $en;
		}
	}

	return $yol;
}
