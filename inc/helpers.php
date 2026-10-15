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
 * Options Page'in post_id değerini döndürür: dile göre "options_tr" veya "options_en".
 *
 * @param string|null $dil tr | en (boşsa geçerli dil).
 * @return string
 */
function servo_options_id( $dil = null ) {
	$dil = $dil ? $dil : servo_dil();

	return apply_filters( 'servo_options_id', 'options_' . $dil, $dil );
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
	$deger = servo_field( $name, servo_options_id(), null );

	// İngilizce alan boşsa Türkçe değer kullanılır (telefon, e-posta, adres gibi çevrilmeyen alanlar tek yerde durur).
	if ( servo_is_empty( $deger ) && 'tr' !== servo_dil() ) {
		$deger = servo_field( $name, servo_options_id( 'tr' ), null );
	}

	return servo_is_empty( $deger ) ? $default : $deger;
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
 * Görünen WhatsApp numarasından wa.me bağlantısı üretir.
 *
 * @param string $phone Görünen numara (ör. '+90 544 636 12 00').
 * @return string Ör. 'https://wa.me/905446361200'.
 */
function servo_whatsapp_href( $phone ) {
	return 'https://wa.me/' . preg_replace( '/[^0-9]/', '', (string) $phone );
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
 * Slug'ı verilen sayfanın veya kaydın adresini döndürür.
 *
 * Tasarımdaki sabit .html bağlantılarının WordPress karşılığıdır. Kayıt henüz
 * yoksa beklenen kalıcı bağlantı döndürülür (ör. /uygulama/lastik/).
 *
 * @param string $slug      Slug (sayfalarda tam yol, ör. 'core-precisor').
 * @param string $post_type 'page', 'post' (rehber) veya 'uygulama'.
 * @return string
 */
function servo_post_url( $slug, $post_type = 'page' ) {
	$post = servo_kayit_bul( $slug, $post_type );

	if ( $post && 'publish' === get_post_status( $post ) ) {
		return get_permalink( $post );
	}

	$bases = array(
		'post'     => 'rehber/',
		'uygulama' => 'uygulama/',
	);
	$base  = isset( $bases[ $post_type ] ) ? $bases[ $post_type ] : '';

	return home_url( '/' . $base . $slug . '/' );
}

/**
 * Media Library'deki bir dosyanın ek (attachment) ID'sini dosya adından bulur.
 *
 * Tasarımdaki video, PDF ve saha fotoğrafları temaya değil Media Library'ye
 * yüklenir. 7. adımda bu dosyalar ACF alanlarına bağlanacak; o zamana kadar
 * şablonlar dosyayı adıyla bulur.
 *
 * @param string $filename Dosya adı (ör. 'servomotion-shuttle-poster.jpg').
 * @return int Bulunamazsa 0.
 */
function servo_media_id( $filename ) {
	static $cache = array();

	if ( isset( $cache[ $filename ] ) ) {
		return $cache[ $filename ];
	}

	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_wp_attached_file',
					'value'   => '/' . $filename,
					'compare' => 'LIKE',
				),
			),
		)
	);

	$cache[ $filename ] = $ids ? (int) $ids[0] : 0;

	return $cache[ $filename ];
}

/**
 * Media Library'deki bir dosyanın adresini dosya adından bulur.
 *
 * @param string $filename Dosya adı.
 * @return string Bulunamazsa boş metin.
 */
function servo_media_url( $filename ) {
	$id = servo_media_id( $filename );

	return $id ? (string) wp_get_attachment_url( $id ) : '';
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
 * Vaka çalışmaları sayfası Site Ayarları'ndan seçilir. İlgili bağlantılar
 * bloğundaki vaka linkleri bu fonksiyonu kullanır.
 *
 * @param int|WP_Post $vaka Vaka ID'si veya nesnesi.
 * @return string Referanslar sayfası veya vaka yoksa boş metin.
 */
function servo_vaka_url( $vaka ) {
	$anchor  = servo_vaka_anchor( $vaka );
	$page_id = (int) servo_option( 'vaka_sayfasi', 0 );

	if ( '' === $anchor || ! $page_id ) {
		return '';
	}

	return get_permalink( $page_id ) . '#' . $anchor;
}

/**
 * Ekran okuyucuların İngilizce okuması gereken ürün ve marka adları.
 *
 * Panelde metinler düz yazılır; servo_bdi() bu adları çıktıda otomatik
 * işaretler (ürün sayfaları, sayfa blokları ve rehber yazıları). Değer işaretleme biçimidir:
 * - 'bdi'  : <bdi lang="en">Ad</bdi>
 * - 'span' : <span lang="en"><bdi lang="en">Ad</bdi></span> (tasarımda "Precisor" böyle)
 *
 * Tasarımda "Core" ve "Shuttle" işaretin dışında kalır, sadece "Precisor"
 * işaretlenir. Büyük/küçük harf duyarlıdır (ör. "PRECISOR" işaretlenmez).
 * Yeni ad eklemek için bu listeye bir satır eklemek yeterlidir.
 *
 * @return array<string,string> Ad => biçim.
 */
function servo_marka_adlari() {
	return apply_filters(
		'servo_marka_adlari',
		array(
			'ServoMotion'                     => 'bdi',
			'Precisor'                        => 'span',
			'ProfiNet'                        => 'span',
			'PROFINET'                        => 'bdi',
			'TÜV Rheinland LGA Products GmbH' => 'bdi',
			'servomotion.com.tr'              => 'bdi',
		)
	);
}

/**
 * HTML metindeki ürün ve marka adlarını <bdi lang="en"> ile işaretler.
 *
 * Sadece etiket dışındaki metne dokunur (öznitelikler, bağlantı adresleri
 * değişmez); zaten <bdi> içinde olan adları ikinci kez işaretlemez; SVG,
 * script, form seçenekleri gibi etiketlerin içindeki metne dokunmaz. Adın
 * önünde veya arkasında harf, rakam ya da "@" varsa (ör. e-posta adresi,
 * daha uzun bir kelime) işaretlemez.
 *
 * @param string $html Escape edilmiş veya kses'ten geçmiş HTML.
 * @return string
 */
function servo_bdi( $html ) {
	$adlar = servo_marka_adlari();
	$html  = (string) $html;

	if ( ! $adlar || '' === $html ) {
		return $html;
	}

	$sirali = array_keys( $adlar );
	usort(
		$sirali,
		function ( $a, $b ) {
			return mb_strlen( $b ) - mb_strlen( $a );
		}
	);

	$desen = '/(?<![\p{L}\p{N}@.\/-])(' . implode( '|', array_map( 'preg_quote', $sirali, array_fill( 0, count( $sirali ), '/' ) ) ) . ')(?![\p{L}\p{N}@])/u';
	$parca = preg_split( '/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	// Bu etiketlerin içindeki metne dokunulmaz (bdi geçersiz olur veya görünmez metindir).
	$atla = array( 'bdi', 'svg', 'script', 'style', 'textarea', 'option', 'select', 'title' );
	$ic   = 0;

	foreach ( $parca as $i => $p ) {
		if ( '' !== $p && '<' === $p[0] ) {
			if ( preg_match( '#^<(/?)([a-z0-9]+)#i', $p, $m ) && in_array( strtolower( $m[2] ), $atla, true ) && '/>' !== substr( $p, -2 ) ) {
				$ic = '/' === $m[1] ? max( 0, $ic - 1 ) : $ic + 1;
			}
			continue;
		}

		if ( $ic ) {
			continue;
		}

		$parca[ $i ] = preg_replace_callback(
			$desen,
			function ( $m ) use ( $adlar ) {
				$etiket = '<bdi lang="en">' . $m[1] . '</bdi>';

				return 'span' === $adlar[ $m[1] ] ? '<span lang="en">' . $etiket . '</span>' : $etiket;
			},
			$p
		);
	}

	return implode( '', $parca );
}

/**
 * Site Ayarları'ndaki bir bağlantı listesini (repeater, alt alan: link) okur.
 *
 * @param string  $name     Repeater alan adı.
 * @param array[] $defaults Liste boşsa (veya SCF kapalıysa) dönecek bağlantılar (url, title).
 * @return array[] Link dizileri (url, title, target).
 */
function servo_option_links( $name, $defaults = array() ) {
	$links = array();

	foreach ( (array) servo_option( $name, array() ) as $row ) {
		if ( ! empty( $row['link']['url'] ) ) {
			$links[] = $row['link'];
		}
	}

	return $links ? $links : $defaults;
}

/**
 * SSS gruplarını ve her grubun sorularını sırasıyla döndürür.
 *
 * Gruplar "Sıra" alanına (sss_grup term meta: sira), sorular sayfa
 * sırasına (menu_order) göre dizilir. Sorusu olmayan grup döndürülmez.
 *
 * @param int[] $term_ids Gösterilecek grupların ID'leri; boşsa tümü.
 * @return array[] Her öğe: array( 'term' => WP_Term, 'sorular' => WP_Post[] ).
 */
function servo_sss_gruplari( $term_ids = array() ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'sss_grup',
			'hide_empty' => true,
			'include'    => array_filter( array_map( 'absint', (array) $term_ids ) ),
		)
	);

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		function ( $a, $b ) {
			return (int) get_term_meta( $a->term_id, 'sira', true ) - (int) get_term_meta( $b->term_id, 'sira', true );
		}
	);

	$gruplar = array();

	foreach ( $terms as $term ) {
		$sorular = get_posts(
			array(
				'post_type'        => 'sss',
				'posts_per_page'   => -1,
				'orderby'          => array(
					'menu_order' => 'ASC',
					'date'       => 'ASC',
				),
				'tax_query'        => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy'         => 'sss_grup',
						'terms'            => $term->term_id,
						'include_children' => false,
					),
				),
				'suppress_filters' => false,
			)
		);

		if ( $sorular ) {
			$gruplar[] = array(
				'term'    => $term,
				'sorular' => $sorular,
			);
		}
	}

	return $gruplar;
}

/**
 * Sözlük terimlerini döndürür.
 *
 * @param string $siralama 'elle' (sayfa sırası, menu_order) veya 'alfabe'.
 * @return WP_Post[]
 */
function servo_terimler( $siralama = 'elle' ) {
	$orderby = 'alfabe' === $siralama
		? array( 'title' => 'ASC' )
		: array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		);

	return get_posts(
		array(
			'post_type'        => 'terim',
			'posts_per_page'   => -1,
			'orderby'          => $orderby,
			'suppress_filters' => false,
		)
	);
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
