<?php
/**
 * Blok: Vaka listesi (article.case).
 *
 * Her vakanın id'si post slug'ından üretilir (servo_vaka_anchor); başlık
 * değişse de diğer sayfalardaki bağlantılar kırılmaz.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_vakalar = (array) servo_sub( 'vakalar', array() );
$servo_not     = servo_sub( 'not' );
$servo_iki     = 'iki_sutun' === servo_sub( 'gorunum', 'liste' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_iki ) {
	echo '<div class="cases">';
}

foreach ( $servo_vakalar as $servo_id ) {
	if ( 'publish' !== get_post_status( $servo_id ) ) {
		continue;
	}

	$servo_anchor   = servo_block_id( servo_vaka_anchor( $servo_id ) );
	$servo_etiket   = servo_field( 'etiket', $servo_id );
	$servo_giris    = servo_field( 'giris', $servo_id );
	$servo_bilgiler = (array) servo_field( 'bilgiler', $servo_id, array() );
	$servo_foto     = servo_field( 'foto', $servo_id );

	echo '<article class="case"' . ( $servo_anchor ? ' id="' . esc_attr( $servo_anchor ) . '"' : '' ) . '><div class="top">';
	echo '<div class="vis">' . servo_visual( servo_field( 'gorsel', $servo_id ), servo_field( 'svg', $servo_id, '' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="hd">';
	if ( $servo_etiket ) {
		echo '<p class="tag">' . esc_html( $servo_etiket ) . '</p>';
	}
	echo '<h3>' . esc_html( get_the_title( $servo_id ) ) . '</h3>';
	if ( $servo_giris ) {
		echo '<p class="lead">' . servo_text( $servo_giris ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div></div>';

	if ( $servo_bilgiler ) {
		echo '<div class="cfg">';
		foreach ( $servo_bilgiler as $servo_b ) {
			echo '<div><b>' . esc_html( $servo_b['deger'] );
			if ( $servo_b['birim'] ) {
				echo '<small>' . esc_html( $servo_b['birim'] ) . '</small>';
			}
			echo '</b><span>' . esc_html( $servo_b['aciklama'] ) . '</span></div>';
		}
		echo '</div>';
	}

	echo '<div class="body">';
	foreach ( array( 'baslangic' => __( 'Başlangıç durumu', 'servomotion' ), 'kurulan' => __( 'Kurulan yapı', 'servomotion' ) ) as $servo_alan => $servo_baslik ) {
		$servo_metin = servo_field( $servo_alan, $servo_id );
		if ( $servo_metin ) {
			echo '<div><h4>' . esc_html( $servo_baslik ) . '</h4><p>' . servo_text( $servo_metin ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
	echo '</div>';

	if ( $servo_foto ) {
		echo '<figure class="fotobox">' . wp_get_attachment_image( $servo_foto, 'servo-foto', false, array( 'loading' => 'lazy' ) );
		if ( servo_field( 'foto_aciklama', $servo_id ) ) {
			echo '<figcaption>' . esc_html( servo_field( 'foto_aciklama', $servo_id ) ) . '</figcaption>';
		}
		echo '</figure>';
	}

	echo '</article>';
}

if ( $servo_iki ) {
	echo '</div>';
}

if ( $servo_not ) {
	echo '<p class="anon">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
