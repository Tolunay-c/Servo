<?php
/**
 * Flexible content (sayfa blokları) altyapısı.
 *
 * Her layout template-parts/blocks/{layout-adi}.php dosyasıdır ve tasarımdaki
 * bir <section>'a karşılık gelir. Bloklar, SCF'nin satır döngüsü içinde
 * servo_sub() ile kendi alanlarını okur.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sayfanın bloklarını sırayla basar.
 *
 * Çıktıdaki ürün ve marka adları servo_bdi() ile işaretlenir. SCF kapalıysa
 * hiçbir şey basmaz.
 *
 * @param int|false $post_id Yazı ID'si; false ise geçerli yazı.
 * @return bool Blok basıldıysa true.
 */
function servo_render_blocks( $post_id = false ) {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'bloklar', $post_id ) ) {
		return false;
	}

	// Marka adları tüm blok çıktısında tek seferde işaretlenir (servo_bdi()).
	ob_start();
	while ( have_rows( 'bloklar', $post_id ) ) {
		the_row();
		get_template_part( 'template-parts/blocks/' . str_replace( '_', '-', get_row_layout() ) );
	}
	echo servo_bdi( (string) ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bloklar kendi çıktısını escape eder.

	return true;
}

/**
 * Geçerli sayfada belirli bir blok olup olmadığını döndürür.
 *
 * Asset'leri koşullu yüklemek için kullanılır; SCF'ye ihtiyaç duymaz.
 *
 * @param string   $layout  Layout adı (ör. 'sozluk').
 * @param int|null $post_id Yazı ID'si; null ise geçerli sorgu.
 * @return bool
 */
function servo_page_has_block( $layout, $post_id = null ) {
	if ( null === $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_queried_object_id();
	}

	$layouts = get_post_meta( $post_id, 'bloklar', true );

	return is_array( $layouts ) && in_array( $layout, $layouts, true );
}

/**
 * Blok satırındaki bir alt alanı okur.
 *
 * @param string $name    Alan adı.
 * @param mixed  $default Alan boşsa dönecek değer.
 * @return mixed
 */
function servo_sub( $name, $default = null ) {
	if ( ! function_exists( 'get_sub_field' ) ) {
		return $default;
	}

	$value = get_sub_field( $name );

	return servo_is_empty( $value ) ? $default : $value;
}

/**
 * Sayfada tekil bir bölüm ID'si üretir.
 *
 * Aynı ID ikinci kez istenirse sonuna sıra eki eklenir (ör. iletisim-2).
 *
 * @param string $id İstenen ID (boş olabilir).
 * @return string Boşsa boş metin.
 */
function servo_block_id( $id ) {
	static $used = array();

	$id = sanitize_title( (string) $id );

	if ( '' === $id ) {
		return '';
	}

	$unique = $id;
	$i      = 2;

	while ( isset( $used[ $unique ] ) ) {
		$unique = $id . '-' . $i;
		++$i;
	}

	$used[ $unique ] = true;

	return $unique;
}

/**
 * Bölümün <section> açılış etiketini döndürür.
 *
 * Arka plan seçimi (arka_plan) ve çapa (capa) alanları satırdan okunur.
 *
 * @param string $class    Bölümün temel class'ı (ör. 'section').
 * @param string $fixed_id Stile bağlı sabit ID (ör. 'sistemler'); varsa çapa alanı yok sayılır.
 * @param bool   $bg       Arka plan seçeneği uygulanacak mı.
 * @return string
 */
function servo_section_open( $class, $fixed_id = '', $bg = true ) {
	$classes = array( $class );
	$style   = '';

	if ( $bg ) {
		$arka_plan = servo_sub( 'arka_plan', 'varsayilan' );

		if ( 'koyu' === $arka_plan ) {
			$classes[] = 'dark';
		} elseif ( 'beyaz' === $arka_plan ) {
			$style = 'background:#fff';
		}
	}

	$id   = servo_block_id( '' !== $fixed_id ? $fixed_id : servo_sub( 'capa', '' ) );
	$html = '<section class="' . esc_attr( implode( ' ', $classes ) ) . '"';

	if ( '' !== $style ) {
		$html .= ' style="' . esc_attr( $style ) . '"';
	}

	if ( '' !== $id ) {
		$html .= ' id="' . esc_attr( $id ) . '"';
	}

	return $html . '>';
}

/**
 * Satır içi HTML içerebilen bir metni güvenli basılabilir hâle getirir.
 *
 * Satır sonları <br> olur.
 *
 * @param string $text Metin.
 * @return string
 */
function servo_text( $text ) {
	return wp_kses_post( nl2br( trim( (string) $text ) ) );
}

/**
 * Boş satırla ayrılmış paragrafları <p> etiketleriyle döndürür.
 *
 * @param string $text Metin.
 * @param string $atts <p> etiketine eklenecek öznitelikler (ör. ' class="x"').
 * @return string
 */
function servo_paragraphs( $text, $atts = '' ) {
	$html = '';

	foreach ( preg_split( '/\R\s*\R/', trim( (string) $text ) ) as $paragraph ) {
		if ( '' !== trim( $paragraph ) ) {
			$html .= '<p' . $atts . '>' . servo_text( $paragraph ) . '</p>';
		}
	}

	return $html;
}

/**
 * SCF link alanından <a> etiketinin href/target/rel özniteliklerini üretir.
 *
 * @param array $link Link alanı değeri (url, title, target).
 * @return string Baştaki boşlukla birlikte öznitelikler.
 */
function servo_link_atts( $link ) {
	$atts = ' href="' . esc_url( $link['url'] ) . '"';

	if ( ! empty( $link['target'] ) ) {
		$atts .= ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"';
	}

	return $atts;
}

/**
 * Görsel alanı doluysa görseli, değilse seçili tema SVG'sini döndürür.
 *
 * @param int|false $image_id Görsel ID'si.
 * @param string    $svg      Tema SVG dosya adı (uzantısız).
 * @param string    $size     Görsel boyutu.
 * @param array     $atts     Görsel öznitelikleri.
 * @return string
 */
function servo_visual( $image_id, $svg, $size = 'large', $atts = array() ) {
	if ( $image_id ) {
		return (string) wp_get_attachment_image( $image_id, $size, false, $atts );
	}

	return servo_get_svg( (string) $svg );
}

/**
 * Bölüm başlığının alanları dolu mu?
 *
 * @return bool
 */
function servo_has_section_head() {
	return (bool) ( servo_sub( 'etiket' ) || servo_sub( 'baslik' ) || servo_sub( 'aciklama' ) );
}

/**
 * <bdi lang="en"> etiketine (marka adları) içerik alanlarında izin verir.
 *
 * @param array  $tags    İzin verilen etiketler.
 * @param string $context Bağlam.
 * @return array
 */
function servo_kses_bdi( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['bdi'] = array( 'lang' => true );
	}

	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'servo_kses_bdi', 10, 2 );

/**
 * Panelde kapalı blokların başlığında bloğun kendi başlığını gösterir.
 *
 * @param string $title  Layout başlığı.
 * @param array  $field  Flexible alan.
 * @param array  $layout Layout.
 * @param int    $i      Sıra.
 * @return string
 */
function servo_layout_title( $title, $field, $layout, $i ) {
	foreach ( array( 'baslik', 'etiket' ) as $name ) {
		$value = get_sub_field( $name );

		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$text = wp_strip_all_tags( str_replace( array( "\r", "\n" ), ' ', $value ) );

			return $title . ' — <span style="font-weight:400">' . esc_html( wp_trim_words( $text, 10, '…' ) ) . '</span>';
		}
	}

	return $title;
}
add_filter( 'acf/fields/flexible_content/layout_title/name=bloklar', 'servo_layout_title', 10, 4 );

/**
 * SVG seçim alanlarının seçeneklerini tema dosyalarından doldurur.
 *
 * Wrapper class'ı "servo-svg-gorsel" olan alanlar şema/görsel SVG'lerini,
 * "servo-svg-ikon" olanlar ikon-* SVG'lerini, "servo-svg-rehber" olanlar
 * rehber yazı kartı şemalarını (rehber-*), "servo-svg-servis" olanlar servis
 * kanalı simgelerini (servis-*) listeler.
 *
 * @param array $field Alan.
 * @return array
 */
function servo_svg_choices( $field ) {
	$class = isset( $field['wrapper']['class'] ) ? (string) $field['wrapper']['class'] : '';

	if ( false === strpos( $class, 'servo-svg-' ) ) {
		return $field;
	}

	// Alanın listeleyeceği grup: ikon-*, rehber-* veya diğer şemalar.
	if ( false !== strpos( $class, 'servo-svg-ikon' ) ) {
		$grup = 'ikon-';
	} elseif ( false !== strpos( $class, 'servo-svg-rehber' ) ) {
		$grup = 'rehber-';
	} elseif ( false !== strpos( $class, 'servo-svg-servis' ) ) {
		$grup = 'servis-';
	} else {
		$grup = '';
	}

	$choices = array();

	foreach ( (array) glob( get_template_directory() . '/assets/img/svg/*.svg' ) as $file ) {
		$name       = basename( $file, '.svg' );
		$dosya_grup = '';

		foreach ( array( 'ikon-', 'rehber-', 'servis-' ) as $onek ) {
			if ( 0 === strpos( $name, $onek ) ) {
				$dosya_grup = $onek;
			}
		}

		if ( 'logo-isaret' === $name || 0 === strpos( $name, 'kanal-' ) || $grup !== $dosya_grup ) {
			continue;
		}

		$choices[ $name ] = ucfirst( str_replace( array( 'ikon-', 'rehber-', 'servis-', '-' ), array( '', '', '', ' ' ), $name ) );
	}

	$field['choices'] = $choices;

	return $field;
}
add_filter( 'acf/load_field/type=select', 'servo_svg_choices' );

/**
 * Flexible content kullanan içerik tiplerinde blok editörünü kapatır.
 *
 * Sayfa ve uygulama düzenleme ekranında sadece bloklar görünür. SSS cevabı
 * ve sözlük açıklaması kısa metinler olduğu için klasik editörle yazılır.
 *
 * @param bool   $use       Blok editörü kullanılsın mı.
 * @param string $post_type İçerik tipi.
 * @return bool
 */
function servo_disable_block_editor( $use, $post_type ) {
	return in_array( $post_type, array( 'page', 'uygulama', 'sss', 'terim' ), true ) ? false : $use;
}
add_filter( 'use_block_editor_for_post_type', 'servo_disable_block_editor', 10, 2 );

/**
 * Detay sayfası kapalı uygulama kayıtlarını 404'e düşürür.
 *
 * Ana sayfadaki "diğer uygulama alanları" kartları kayıt olarak durur ama
 * kendi sayfaları yoktur.
 */
function servo_uygulama_detay_kontrol() {
	if ( ! is_singular( 'uygulama' ) ) {
		return;
	}

	// Alan hiç kaydedilmemişse (yeni kayıt) detay sayfası açık sayılır.
	$detay = get_post_meta( get_queried_object_id(), 'detay_sayfasi', true );

	if ( '0' === (string) $detay ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'servo_uygulama_detay_kontrol' );

/**
 * Detay sayfası kapalı uygulama kayıtlarını WordPress site haritasından çıkarır.
 *
 * @param array  $args      Sorgu argümanları.
 * @param string $post_type Yazı tipi.
 * @return array
 */
function servo_uygulama_site_haritasi( $args, $post_type ) {
	if ( 'uygulama' === $post_type ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => 'detay_sayfasi',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'detay_sayfasi',
				'value'   => '0',
				'compare' => '!=',
			),
		);
	}

	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'servo_uygulama_site_haritasi', 10, 2 );

/**
 * Detay sayfası kapalı uygulama kayıtlarını Rank Math site haritasından çıkarır.
 *
 * @param array|false $url  Site haritası satırı.
 * @param string      $type Satır türü ('post', 'term', 'user').
 * @param object      $post Kayıt.
 * @return array|false
 */
function servo_uygulama_rank_math_haritasi( $url, $type, $post ) {
	if ( 'post' === $type && isset( $post->ID, $post->post_type ) && 'uygulama' === $post->post_type
		&& '0' === (string) get_post_meta( $post->ID, 'detay_sayfasi', true ) ) {
		return false;
	}

	return $url;
}
add_filter( 'rank_math/sitemap/entry', 'servo_uygulama_rank_math_haritasi', 10, 3 );

/**
 * Buton satırını (div.actions içeriği) üretir.
 *
 * @param array $rows Butonlar repeater değeri (link, birincil).
 * @return string
 */
function servo_buttons( $rows ) {
	$html = '';

	foreach ( (array) $rows as $row ) {
		if ( empty( $row['link']['url'] ) ) {
			continue;
		}

		$class = empty( $row['birincil'] ) ? 'btn' : 'btn primary';
		$indir = empty( $row['indir'] ) ? '' : ' download';
		$rozet = empty( $row['rozet'] ) ? '' : ' <span style="font-size:12px;opacity:.75;margin-left:8px">' . esc_html( $row['rozet'] ) . '</span>';
		$html .= '<a class="' . $class . '"' . servo_link_atts( $row['link'] ) . $indir . '>' . esc_html( $row['link']['title'] ) . $rozet . '</a>';
	}

	return $html;
}

/**
 * SSS bloğu olan sayfaya FAQPage yapılandırılmış verisini (JSON-LD) basar.
 *
 * Sorular sayfadaki SSS bloklarının gösterdiği gruplardan alınır. Rank Math
 * FAQ bloğu kullanılmadığı için şema tek kez basılır (CLAUDE.md, madde 9).
 */
function servo_sss_jsonld() {
	if ( ! is_singular() || ! servo_page_has_block( 'sss_listesi' ) ) {
		return;
	}

	$sorular = array();

	foreach ( (array) servo_field( 'bloklar', get_queried_object_id(), array() ) as $row ) {
		if ( 'sss_listesi' !== $row['acf_fc_layout'] ) {
			continue;
		}

		foreach ( servo_sss_gruplari( empty( $row['gruplar'] ) ? array() : (array) $row['gruplar'] ) as $grup ) {
			foreach ( $grup['sorular'] as $soru ) {
				$sorular[ $soru->ID ] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( get_the_title( $soru ) ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $soru->post_content ) ) ),
					),
				);
			}
		}
	}

	if ( ! $sorular ) {
		return;
	}

	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_values( $sorular ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>' . "\n";
}
add_action( 'wp_head', 'servo_sss_jsonld' );
