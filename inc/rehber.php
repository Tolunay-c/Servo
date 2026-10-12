<?php
/**
 * Rehber (blog): yazı listesi, yazı şablonu ve Gutenberg uyarlamaları.
 *
 * Rehber yazıları Gutenberg ile yazılır. Tasarımın bileşenleri (.callout,
 * .keyval, .atable, ol.steps, ol.alist) editörde çekirdek bloklara eklenen
 * block style'lar ve desenlerle (pattern) sunulur; ön yüzde render_block
 * filtresi bu blokların çıktısını tasarımdaki markup'a çevirir. Böylece
 * rehber.css değiştirilmeden kullanılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block style'ları ve desenleri kaydeder.
 */
function servo_rehber_editor_kayit() {
	$stiller = array(
		'core/group'     => array( 'callout' => __( 'Bilgi kutusu', 'servomotion' ) ),
		'core/paragraph' => array( 'kutu-basligi' => __( 'Bilgi kutusu başlığı', 'servomotion' ) ),
		'core/table'     => array( 'keyval' => __( 'Değer kutuları', 'servomotion' ) ),
		'core/list'      => array(
			'steps' => __( 'Numaralı adımlar', 'servomotion' ),
			'alist' => __( 'Numaralı liste', 'servomotion' ),
		),
	);

	foreach ( $stiller as $blok => $liste ) {
		foreach ( $liste as $ad => $etiket ) {
			register_block_style(
				$blok,
				array(
					'name'  => $ad,
					'label' => $etiket,
				)
			);
		}
	}

	register_block_pattern_category( 'servomotion-rehber', array( 'label' => __( 'ServoMotion rehber', 'servomotion' ) ) );

	$desenler = array(
		'bilgi-kutusu'   => array(
			__( 'Bilgi kutusu', 'servomotion' ),
			__( 'Solunda sarı çizgi olan gri kutu: küçük başlık ve bir veya birkaç paragraf.', 'servomotion' ),
			'<!-- wp:group {"className":"is-style-callout"} -->
<div class="wp-block-group is-style-callout"><!-- wp:paragraph {"className":"is-style-kutu-basligi"} -->
<p class="is-style-kutu-basligi">' . esc_html__( 'Kutu başlığı', 'servomotion' ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>' . esc_html__( 'Kutu metni. Önemli kısımları kalın yazabilirsiniz.', 'servomotion' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
		),
		'deger-kutulari' => array(
			__( 'Değer kutuları', 'servomotion' ),
			__( 'Yan yana üç değer kutusu. Her satır bir kutudur: 1. sütun açıklama, 2. sütun değer, 3. sütun birim.', 'servomotion' ),
			'<!-- wp:table {"hasFixedLayout":false,"className":"is-style-keyval"} -->
<figure class="wp-block-table is-style-keyval"><table><tbody><tr><td>' . esc_html__( 'Projeksiyon hassasiyeti', 'servomotion' ) . '</td><td>±1,00</td><td>mm</td></tr><tr><td>' . esc_html__( 'Çizgi inceliği', 'servomotion' ) . '</td><td>1</td><td>mm</td></tr><tr><td>' . esc_html__( 'Konumlandırma hızı', 'servomotion' ) . '</td><td>1000</td><td>mm/s</td></tr></tbody></table></figure>
<!-- /wp:table -->',
		),
		'tablo'          => array(
			__( 'Karşılaştırma tablosu', 'servomotion' ),
			__( 'Koyu başlık satırlı tablo. İlk sütun kalın yazılır.', 'servomotion' ),
			'<!-- wp:table {"hasFixedLayout":false} -->
<figure class="wp-block-table"><table><thead><tr><th>' . esc_html__( 'Kavram', 'servomotion' ) . '</th><th>' . esc_html__( 'Ne anlatır', 'servomotion' ) . '</th><th>' . esc_html__( 'Neye bakılır', 'servomotion' ) . '</th></tr></thead><tbody><tr><td></td><td></td><td></td></tr><tr><td></td><td></td><td></td></tr></tbody></table></figure>
<!-- /wp:table -->',
		),
		'adimlar'        => array(
			__( 'Numaralı adımlar', 'servomotion' ),
			__( 'Çizgiyle ayrılmış, numaralı adımlar. Her adımın başındaki kalın kısım başlık olarak gösterilir.', 'servomotion' ),
			'<!-- wp:list {"ordered":true,"className":"is-style-steps"} -->
<ol class="wp-block-list is-style-steps"><!-- wp:list-item -->
<li><strong>' . esc_html__( 'Adım başlığı', 'servomotion' ) . '</strong>' . esc_html__( 'Adımın açıklaması.', 'servomotion' ) . '</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>' . esc_html__( 'Adım başlığı', 'servomotion' ) . '</strong>' . esc_html__( 'Adımın açıklaması.', 'servomotion' ) . '</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list -->',
		),
	);

	foreach ( $desenler as $ad => $d ) {
		register_block_pattern(
			'servomotion/' . $ad,
			array(
				'title'       => $d[0],
				'description' => $d[1],
				'categories'  => array( 'servomotion-rehber' ),
				'postTypes'   => array( 'post' ),
				'content'     => $d[2],
			)
		);
	}
}
add_action( 'init', 'servo_rehber_editor_kayit' );

/**
 * Tablo bloğunun "Şeritler" stilini kaldırır.
 *
 * Tasarımda karşılığı yoktur; ön yüzde her tablo .atable veya .keyval olur.
 * Stil block.json'da tanımlı olduğu için meta veriden çıkarılır.
 *
 * @param array $metadata Blok meta verisi.
 * @return array
 */
function servo_tablo_stilleri( $metadata ) {
	if ( isset( $metadata['name'], $metadata['styles'] ) && 'core/table' === $metadata['name'] ) {
		$metadata['styles'] = array_values(
			array_filter(
				$metadata['styles'],
				function ( $stil ) {
					return 'stripes' !== $stil['name'];
				}
			)
		);
	}

	return $metadata;
}
add_filter( 'block_type_metadata', 'servo_tablo_stilleri' );

/**
 * Bir bloğun className'inde verilen stil sınıfı var mı?
 *
 * @param array  $block Blok.
 * @param string $stil  Stil adı (is-style- öneksiz).
 * @return bool
 */
function servo_rehber_stil( $block, $stil ) {
	$class = isset( $block['attrs']['className'] ) ? ' ' . $block['attrs']['className'] . ' ' : '';

	return false !== strpos( $class, ' is-style-' . $stil . ' ' );
}

/**
 * HTML'deki ilk etiketin class'ından bazılarını çıkarır, bazılarını ekler.
 *
 * @param string   $html  HTML.
 * @param string[] $cikar Çıkarılacak sınıflar.
 * @param string[] $ekle  Eklenecek sınıflar.
 * @return string
 */
function servo_rehber_sinif( $html, $cikar, $ekle = array() ) {
	$p = new WP_HTML_Tag_Processor( $html );

	if ( ! $p->next_tag() ) {
		return $html;
	}

	foreach ( $cikar as $c ) {
		$p->remove_class( $c );
	}
	foreach ( $ekle as $c ) {
		$p->add_class( $c );
	}
	if ( '' === trim( (string) $p->get_attribute( 'class' ) ) ) {
		$p->remove_attribute( 'class' );
	}

	return $p->get_updated_html();
}

/**
 * Rehber yazılarındaki çekirdek blokların çıktısını tasarımın markup'ına çevirir.
 *
 * - Başlık ve listelerden WordPress sınıfları kalkar (paragraf sınıfı
 *   servo_rehber_icerik()'te kalkar); "Numaralı adımlar" /
 *   "Numaralı liste" stilleri ol.steps / ol.alist olur.
 * - "Bilgi kutusu" grubu div.callout, başlık paragrafı div.t olur.
 * - Tablolar div.ascroll > table.atable, "Değer kutuları" tablosu div.keyval olur.
 *
 * @param string $html  Blok çıktısı.
 * @param array  $block Blok.
 * @return string
 */
function servo_rehber_render_block( $html, $block ) {
	if ( is_admin() || 'post' !== get_post_type() || empty( $block['blockName'] ) ) {
		return $html;
	}

	switch ( $block['blockName'] ) {
		case 'core/heading':
			return servo_rehber_sinif( $html, array( 'wp-block-heading' ) );

		case 'core/list':
			$ekle = array();
			foreach ( array( 'steps', 'alist' ) as $stil ) {
				if ( servo_rehber_stil( $block, $stil ) ) {
					$ekle[] = $stil;
				}
			}
			return servo_rehber_sinif( $html, array( 'wp-block-list', 'is-style-steps', 'is-style-alist' ), $ekle );

		case 'core/paragraph':
			if ( servo_rehber_stil( $block, 'kutu-basligi' ) ) {
				$ic = preg_replace( '#^\s*<p[^>]*>|</p>\s*$#', '', $html );
				return '<div class="t">' . $ic . '</div>';
			}
			return $html;

		case 'core/group':
			if ( ! servo_rehber_stil( $block, 'callout' ) ) {
				return $html;
			}
			$ic = '';
			foreach ( $block['innerBlocks'] as $alt ) {
				$ic .= render_block( $alt );
			}
			return '<div class="callout">' . $ic . '</div>';

		case 'core/table':
			if ( ! preg_match( '#<table[^>]*>(.*)</table>#s', $html, $m ) ) {
				return $html;
			}
			if ( ! servo_rehber_stil( $block, 'keyval' ) ) {
				return '<div class="ascroll"><table class="atable">' . $m[1] . '</table></div>';
			}
			$kutular = '';
			preg_match_all( '#<tr[^>]*>(.*?)</tr>#s', $m[1], $satirlar );
			foreach ( $satirlar[1] as $satir ) {
				preg_match_all( '#<t[dh][^>]*>(.*?)</t[dh]>#s', $satir, $h );
				$h = array_pad( array_map( 'trim', $h[1] ), 3, '' );
				if ( '' === $h[0] && '' === $h[1] ) {
					continue;
				}
				$kutular .= '<div><div class="k">' . $h[0] . '</div><div class="v">' . $h[1] . ( '' !== $h[2] ? '<small>' . $h[2] . '</small>' : '' ) . '</div></div>';
			}
			return '<div class="keyval">' . $kutular . '</div>';
	}

	return $html;
}
add_filter( 'render_block', 'servo_rehber_render_block', 10, 2 );

/**
 * Yazı içeriğini hazırlar: içindekiler için H2'lere id verir, <strong>'u
 * tasarımın kullandığı <b>'ye çevirir ve marka adlarını işaretler.
 *
 * İçerik the_content filtrelerinden geçer; sadece wptexturize kapatılır
 * (tasarımdaki düz tırnak ve kesme işaretleri kıvrık tırnağa dönmesin).
 *
 * @param int|WP_Post $post Yazı.
 * @return array{0:string,1:array<int,array{id:string,metin:string}>} İçerik ve içindekiler.
 */
function servo_rehber_icerik( $post ) {
	$post      = get_post( $post );
	$texturize = has_filter( 'the_content', 'wptexturize' );

	if ( false !== $texturize ) {
		remove_filter( 'the_content', 'wptexturize', $texturize );
	}
	$content   = apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	if ( false !== $texturize ) {
		add_filter( 'the_content', 'wptexturize', $texturize );
	}

	$content = preg_replace( '#<(/?)strong(\s[^>]*)?>#i', '<$1b>', (string) $content );
	// WordPress paragraflara render_block'tan sonra da sınıf ekler; tasarımda yok.
	$content = preg_replace( '#<p class="wp-block-paragraph"#', '<p', $content );
	$content = preg_replace( '#(<p class="[^"]*?)\s*\bwp-block-paragraph\b#', '$1', $content );
	$toc     = array();
	$kullan  = array();

	$content = preg_replace_callback(
		'#<h2([^>]*)>(.*?)</h2>#is',
		function ( $m ) use ( &$toc, &$kullan ) {
			$atts  = $m[1];
			$metin = trim( wp_strip_all_tags( $m[2] ) );

			if ( preg_match( '#\sid="([^"]+)"#', $atts, $id ) ) {
				$id = $id[1];
			} else {
				$id    = sanitize_title( $metin );
				$ek    = 2;
				$temel = $id;
				while ( isset( $kullan[ $id ] ) ) {
					$id = $temel . '-' . $ek++;
				}
				$atts .= ' id="' . esc_attr( $id ) . '"';
			}

			$kullan[ $id ] = true;
			$toc[]         = array(
				'id'    => $id,
				'metin' => $metin,
			);

			return '<h2' . $atts . '>' . $m[2] . '</h2>';
		},
		$content
	);

	return array( servo_bdi( $content ), $toc );
}

/**
 * Türkçe büyük harfe çevirir (i → İ, ı → I).
 *
 * Ürün ve marka adları (servo_marka_adlari()) İngilizce kuralla çevrilir:
 * "ServoMotion" → "SERVOMOTION" ("SERVOMOTİON" değil).
 *
 * @param string $text Metin.
 * @return string
 */
function servo_tr_upper( $text ) {
	$adlar = array_map(
		function ( $ad ) {
			return preg_quote( $ad, '/' );
		},
		array_keys( servo_marka_adlari() )
	);
	$parca = $adlar ? preg_split( '/(' . implode( '|', $adlar ) . ')/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE ) : array( (string) $text );

	foreach ( $parca as $i => $p ) {
		$parca[ $i ] = mb_strtoupper( 1 === $i % 2 ? $p : str_replace( array( 'i', 'ı' ), array( 'İ', 'I' ), $p ), 'UTF-8' );
	}

	return implode( '', $parca );
}

/**
 * Yazının okuma süresini (dakika) döndürür.
 *
 * Alan boşsa metindeki kelime sayısından (dakikada 200 kelime) hesaplanır.
 *
 * @param int|WP_Post $post Yazı.
 * @return int
 */
function servo_rehber_okuma( $post ) {
	$post = get_post( $post );
	$dk   = (int) servo_field( 'okuma_suresi', $post->ID, 0 );

	if ( $dk > 0 ) {
		return $dk;
	}

	$kelime = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( $post->post_content ) ), -1, PREG_SPLIT_NO_EMPTY ) );

	return max( 1, (int) ceil( $kelime / 200 ) );
}

/**
 * Yazının yazarını döndürür (alan boşsa Site Ayarları'ndaki varsayılan).
 *
 * @param int|WP_Post $post Yazı.
 * @return string
 */
function servo_rehber_yazar( $post ) {
	$post = get_post( $post );

	return (string) servo_field( 'yazar', $post->ID, servo_option( 'rehber_yazar', __( 'ServoMotion Mühendislik Ekibi', 'servomotion' ) ) );
}

/**
 * Yazının kategori ve ürün alanı terimlerini döndürür.
 *
 * @param int|WP_Post $post Yazı.
 * @return array{kategori:WP_Term|null,alanlar:WP_Term[]}
 */
function servo_rehber_terimler( $post ) {
	$post     = get_post( $post );
	$kategori = get_the_category( $post->ID );
	$alanlar  = get_the_terms( $post->ID, 'urun_alani' );

	return array(
		'kategori' => $kategori ? $kategori[0] : null,
		'alanlar'  => is_array( $alanlar ) ? $alanlar : array(),
	);
}

/**
 * Kategori ve ürün alanı etiketlerini (.tagrow) döndürür.
 *
 * @param int|WP_Post $post Yazı.
 * @return string
 */
function servo_rehber_etiketler( $post ) {
	$t    = servo_rehber_terimler( $post );
	$html = '';

	if ( $t['kategori'] ) {
		$html .= '<span class="ctag ' . esc_attr( $t['kategori']->slug ) . '">' . esc_html( $t['kategori']->name ) . '</span>';
	}
	foreach ( $t['alanlar'] as $alan ) {
		$html .= '<span class="atag">' . esc_html( $alan->name ) . '</span>';
	}

	return '<div class="tagrow">' . servo_bdi( $html ) . '</div>';
}

/**
 * Yazının kart görselini döndürür: öne çıkan görsel varsa o, yoksa seçili şema.
 *
 * @param int|WP_Post $post Yazı.
 * @return string
 */
function servo_rehber_gorsel( $post ) {
	$post = get_post( $post );

	if ( has_post_thumbnail( $post ) ) {
		return (string) get_the_post_thumbnail( $post, 'large', array( 'loading' => 'lazy' ) );
	}

	return servo_get_svg( (string) servo_field( 'kart_svg', $post->ID, '' ) );
}

/**
 * Liste filtresinin (rehber-liste.js) aradığı metni üretir.
 *
 * Tasarımdaki gibi: başlık + özet + giriş küçük harfle, ardından aynı metnin
 * Türkçe karaktersiz hâli.
 *
 * @param int|WP_Post $post Yazı.
 * @return string
 */
function servo_rehber_ara_metni( $post ) {
	$post  = get_post( $post );
	$metin = $post->post_title . ' ' . get_the_excerpt( $post ) . ' ' . servo_field( 'giris', $post->ID, '' );
	$metin = str_replace( "\u{0307}", '', mb_strtolower( trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $metin ) ) ), 'UTF-8' ) );
	$sade  = strtr(
		$metin,
		array(
			'ı' => 'i',
			'ğ' => 'g',
			'ü' => 'u',
			'ş' => 's',
			'ö' => 'o',
			'ç' => 'c',
		)
	);

	return $metin . ' ' . $sade;
}

/**
 * İlgili yazıları döndürür: alan doluysa seçilenler, değilse en yeni 3 yazı.
 *
 * @param int|WP_Post $post Yazı.
 * @return WP_Post[]
 */
function servo_rehber_ilgili( $post ) {
	$post = get_post( $post );
	$ids  = array_filter( array_map( 'absint', (array) servo_field( 'ilgili', $post->ID, array() ) ) );

	$args = $ids
		? array(
			'post__in' => $ids,
			'orderby'  => 'post__in',
		)
		: array(
			'post__not_in'   => array( $post->ID ),
			'posts_per_page' => 3,
		);

	return get_posts(
		array_merge(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page'      => 3,
				'suppress_filters'    => false,
			),
			$args
		)
	);
}

/**
 * Rehber listesi ve arşivlerinde tüm yazıları tek sayfada listeler.
 *
 * Filtre ve arama tarayıcıda (rehber-liste.js) çalıştığı için sayfalama
 * yapılmaz. Öne çıkan yazı şablonda ayrıca seçildiği için sabit yazılar
 * sorgunun başına alınmaz.
 *
 * @param WP_Query $query Sorgu.
 */
function servo_rehber_sorgu( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'ignore_sticky_posts', true );
	}
}
add_action( 'pre_get_posts', 'servo_rehber_sorgu' );
