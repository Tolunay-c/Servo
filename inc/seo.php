<?php
/**
 * SEO uyarlamaları (Rank Math ile birlikte).
 *
 * Başlık, açıklama, OG ve canonical Rank Math'ten gelir (CLAUDE.md, madde 9); burada
 * sadece çıktıyı tasarımla (servomotion-site-tum131026/) hizalayan küçük ayarlar var:
 * sayfaların og:type değeri "website", rehber yazılarının "article" olur; rehber
 * yazılarının Article şemasında yazar kişi yerine kurum (ServoMotion) yer alır
 * ve başlık (headline) sayfa başlığındaki site adı eki olmadan yazılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rehber yazıları dışındaki sayfaların OpenGraph türü "website" olur.
 *
 * @param string $type Rank Math'in belirlediği tür.
 * @return string
 */
function servo_og_type( $type ) {
	return is_singular( 'post' ) ? $type : 'website';
}
add_filter( 'rank_math/opengraph/type', 'servo_og_type' );

/**
 * Rehber yazısı şemasında yazar kişi (kullanıcı adı) yerine kurum kullanılır.
 *
 * @param array $data Şema grafiği.
 * @return array
 */
function servo_rank_math_json_ld( $data ) {
	if ( ! is_singular( 'post' ) || ! is_array( $data ) ) {
		return $data;
	}

	$kurum = '';
	foreach ( $data as $dugum ) {
		if ( is_array( $dugum ) && isset( $dugum['@type'] ) && 'Organization' === $dugum['@type'] && ! empty( $dugum['@id'] ) ) {
			$kurum = $dugum['@id'];
		}
	}

	foreach ( $data as $anahtar => $dugum ) {
		if ( ! is_array( $dugum ) || empty( $dugum['@type'] ) ) {
			continue;
		}

		if ( 'Person' === $dugum['@type'] ) {
			unset( $data[ $anahtar ] );
			continue;
		}

		if ( in_array( $dugum['@type'], array( 'Article', 'BlogPosting', 'NewsArticle' ), true ) ) {
			$data[ $anahtar ]['headline'] = wp_strip_all_tags( get_the_title() );
			if ( $kurum ) {
				$data[ $anahtar ]['author'] = array( '@id' => $kurum );
			} else {
				unset( $data[ $anahtar ]['author'] );
			}
		} elseif ( isset( $dugum['author'] ) ) {
			unset( $data[ $anahtar ]['author'] );
		}
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'servo_rank_math_json_ld', 99 );
