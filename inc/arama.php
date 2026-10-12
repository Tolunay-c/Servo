<?php
/**
 * Site içi arama.
 *
 * Flexible content sayfalarının metni post_content'te değil blok alanlarında
 * durduğu için WordPress'in araması bu sayfaları sadece başlıklarından bulur.
 * Bu yüzden her sayfanın blokları kaydedilirken düz metne çevrilir ve gizli
 * bir meta alanında (_servo_arama_metni) tutulur; arama sorgusu bu alana da
 * bakar. Bloklar kayıtlardan (SSS, sözlük, doküman, vaka, uygulama) içerik
 * çektiği için bu kayıtlardan biri değiştiğinde tüm blok sayfaları yeniden
 * dizinlenir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Arama metninin tutulduğu meta anahtarı.
 */
const SERVO_ARAMA_META = '_servo_arama_metni';

/**
 * Bir yazının bloklarını (ürün sayfalarında bölümlerini) basıp düz metne çevirir.
 *
 * @param int $post_id Yazı ID'si.
 * @return string Blok yoksa boş metin.
 */
function servo_arama_metni( $post_id ) {
	$sablonlar = servo_urun_sablonlari();
	$sablon    = (string) get_page_template_slug( $post_id );

	ob_start();
	if ( isset( $sablonlar[ $sablon ] ) ) {
		// Ürün sayfaları alanları geçerli yazıdan okur.
		global $post;
		$onceki = $post;
		$post   = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $post );
		servo_urun_bolumleri( $sablonlar[ $sablon ] );
		$post = $onceki; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( $post ) {
			setup_postdata( $post );
		}
		$has_blocks = true;
	} else {
		$has_blocks = servo_render_blocks( $post_id );
	}
	$html = (string) ob_get_clean();

	if ( ! $has_blocks ) {
		return '';
	}

	// Form, SVG ve görünmeyen içerik aranmaz.
	$html = (string) preg_replace( '#<(script|style|svg|form|select)\b.*?</\1>#is', ' ', $html );
	$text = html_entity_decode( wp_strip_all_tags( str_replace( '<', ' <', $html ) ), ENT_QUOTES, 'UTF-8' );

	return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Bir yazının arama metnini günceller.
 *
 * @param int $post_id Yazı ID'si.
 */
function servo_arama_dizinle( $post_id ) {
	$text = servo_arama_metni( $post_id );

	if ( '' === $text ) {
		delete_post_meta( $post_id, SERVO_ARAMA_META );
		return;
	}

	update_post_meta( $post_id, SERVO_ARAMA_META, wp_slash( $text ) );
}

/**
 * Blok alanı olan tüm sayfa ve uygulama kayıtlarını ve ürün sayfalarını yeniden dizinler.
 */
function servo_arama_tumunu_dizinle() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'page', 'uygulama' ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => 'bloklar', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);

	foreach ( array_keys( servo_urun_sablonlari() ) as $sablon ) {
		$ids = array_merge(
			$ids,
			get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'     => $sablon, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			)
		);
	}

	foreach ( $ids as $id ) {
		servo_arama_dizinle( $id );
	}
}

/**
 * Kayıt kaydedildiğinde arama metnini günceller.
 *
 * SCF alanları save_post'un 10. önceliğinde kaydedilir; bu yüzden 20'de çalışır.
 *
 * @param int     $post_id Yazı ID'si.
 * @param WP_Post $post    Yazı.
 */
function servo_arama_kayit( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( 'page' === $post->post_type ) {
		servo_arama_dizinle( $post_id );
	} elseif ( in_array( $post->post_type, array( 'uygulama', 'vaka', 'dokuman', 'sss', 'terim' ), true ) ) {
		servo_arama_tumunu_dizinle();
	}
}
add_action( 'save_post', 'servo_arama_kayit', 20, 2 );

/**
 * Arama sorgusunu blok metnini de kapsayacak şekilde yeniden kurar.
 *
 * Her kelime başlıkta, özette, içerikte veya arama metninde geçmelidir
 * (WordPress'in varsayılan davranışı gibi). "-kelime" biçimi o kelimeyi
 * içermeyenleri getirir. Kendi sayfası kapalı uygulama kayıtları sonuçlara
 * girmez.
 *
 * @param string   $search Sorgunun arama kısmı.
 * @param WP_Query $query  Sorgu.
 * @return string
 */
function servo_arama_sql( $search, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return $search;
	}

	$terms = (array) $query->get( 'search_terms' );

	if ( ! $terms ) {
		return $search;
	}

	global $wpdb;

	$sql = '';

	foreach ( $terms as $term ) {
		$exclude = '-' === substr( $term, 0, 1 ) && strlen( $term ) > 1;
		$like    = '%' . $wpdb->esc_like( $exclude ? substr( $term, 1 ) : $term ) . '%';

		if ( $exclude ) {
			$sql .= $wpdb->prepare(
				" AND {$wpdb->posts}.post_title NOT LIKE %s AND {$wpdb->posts}.post_excerpt NOT LIKE %s AND {$wpdb->posts}.post_content NOT LIKE %s AND {$wpdb->posts}.ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s )",
				$like,
				$like,
				$like,
				SERVO_ARAMA_META,
				$like
			);
		} else {
			$sql .= $wpdb->prepare(
				" AND ( {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR {$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s ) )",
				$like,
				$like,
				$like,
				SERVO_ARAMA_META,
				$like
			);
		}
	}

	$sql .= " AND {$wpdb->posts}.ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'detay_sayfasi' AND meta_value = '0' )";

	if ( ! is_user_logged_in() ) {
		$sql .= " AND ( {$wpdb->posts}.post_password = '' )";
	}

	return $sql;
}
add_filter( 'posts_search', 'servo_arama_sql', 10, 2 );

/**
 * Arama sonucu için kısa özet döndürür.
 *
 * Blok sayfalarında arama metninden, aranan kelimenin geçtiği yerin
 * çevresi alınır; diğerlerinde yazının özeti kullanılır.
 *
 * @param int|WP_Post $post  Yazı.
 * @param int         $words Kelime sayısı.
 * @return string Düz metin.
 */
function servo_arama_ozet( $post, $words = 28 ) {
	$post = get_post( $post );
	$text = (string) get_post_meta( $post->ID, SERVO_ARAMA_META, true );

	if ( '' === $text ) {
		return wp_trim_words( get_the_excerpt( $post ), $words, '…' );
	}

	// Küçük harfe çevirirken "İ"nin eklediği birleşik noktayı atar; böylece
	// konumlar asıl metinle aynı kalır ve "film" "FİLM" içinde bulunur.
	$lower = str_replace( "\u{0307}", '', mb_strtolower( $text, 'UTF-8' ) );

	foreach ( (array) get_query_var( 'search_terms' ) as $term ) {
		$pos = mb_strpos( $lower, str_replace( "\u{0307}", '', mb_strtolower( ltrim( (string) $term, '-' ), 'UTF-8' ) ) );

		if ( false !== $pos && $pos > 120 ) {
			$start = mb_strrpos( mb_substr( $text, 0, $pos - 60 ), ' ' );
			$text  = '…' . mb_substr( $text, false === $start ? $pos - 60 : $start + 1 );
			break;
		}
	}

	return wp_trim_words( $text, $words, '…' );
}

/**
 * Site Ayarları kaydedildiğinde tüm sayfaları yeniden dizinler.
 *
 * İletişim bilgileri, ürün güvenliği gibi ortak içerikler Site Ayarları'ndan gelir.
 *
 * @param int|string $post_id Kaydedilen nesne ('options' için options sayfası).
 */
function servo_arama_ayarlar( $post_id ) {
	if ( servo_options_id() === $post_id || 'options' === $post_id ) {
		servo_arama_tumunu_dizinle();
	}
}
add_action( 'acf/save_post', 'servo_arama_ayarlar', 20 );
