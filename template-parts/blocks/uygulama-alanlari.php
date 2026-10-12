<?php
/**
 * Blok: Uygulama alanları.
 *
 * Kart içerikleri uygulama kayıtlarının "Ana sayfa kartı" alanlarından gelir.
 * Öne çıkanlar .applead, diğerleri .application-grid ile basılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_ihtiyaclar = (array) servo_sub( 'ihtiyaclar', array() );
$servo_one        = (array) servo_sub( 'one_cikanlar', array() );
$servo_ara        = servo_sub( 'ara_etiket' );
$servo_digerleri  = (array) servo_sub( 'digerleri', array() );
$servo_notlar     = (array) servo_sub( 'notlar', array() );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_ihtiyaclar ) {
	echo '<div class="needrow">';
	foreach ( $servo_ihtiyaclar as $servo_i ) {
		echo '<div class="need"><b>' . esc_html( $servo_i['baslik'] ) . '</b><p>' . servo_text( $servo_i['metin'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

if ( $servo_one ) {
	echo '<div class="applead">';
	foreach ( $servo_one as $servo_id ) {
		$servo_belirleyici = servo_field( 'kart_belirleyici', $servo_id );
		$servo_ornek       = servo_field( 'kart_ornek', $servo_id );

		echo '<article><div class="appvis">' . servo_visual( servo_field( 'kart_gorsel', $servo_id ), servo_field( 'kart_svg', $servo_id, '' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h3>' . esc_html( servo_field( 'kart_baslik', $servo_id, get_the_title( $servo_id ) ) ) . '</h3>';
		echo '<p class="appsym">' . servo_text( servo_field( 'kart_metin', $servo_id, '' ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $servo_belirleyici ) {
			echo '<p class="appkey"><em>' . esc_html__( 'Belirleyici olan', 'servomotion' ) . '</em>' . esc_html( $servo_belirleyici ) . '</p>';
		}
		if ( $servo_ornek ) {
			echo '<p class="appscale">' . servo_text( $servo_ornek ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( '0' !== (string) get_post_meta( $servo_id, 'detay_sayfasi', true ) ) {
			echo '<a class="appnote" href="' . esc_url( get_permalink( $servo_id ) ) . '">' . esc_html__( 'Uygulama sayfasını görün →', 'servomotion' ) . '</a>';
		}
		echo '</article>';
	}
	echo '</div>';
}

if ( $servo_ara ) {
	echo '<p class="rowlab2">' . esc_html( $servo_ara ) . '</p>';
}

if ( $servo_digerleri ) {
	echo '<div class="application-grid">';
	foreach ( $servo_digerleri as $servo_id ) {
		$servo_belirleyici = servo_field( 'kart_belirleyici', $servo_id );

		echo '<article><div class="appvis">' . servo_visual( servo_field( 'kart_gorsel', $servo_id ), servo_field( 'kart_svg', $servo_id, '' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h3>' . esc_html( servo_field( 'kart_baslik', $servo_id, get_the_title( $servo_id ) ) ) . '</h3>';
		echo '<p>' . servo_text( servo_field( 'kart_metin', $servo_id, '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $servo_belirleyici ) {
			/* translators: %s: belirleyici etkenler */
			echo '<span style="display:block;margin-top:8px;font-size:14.5px;color:#64758a">' . esc_html( sprintf( __( 'Belirleyici olan: %s', 'servomotion' ), $servo_belirleyici ) ) . '</span>';
		}
		echo '</p></article>';
	}
	echo '</div>';
}

// Tasarımda son not dışındaki notlar daha yakın aralıkla basılır.
$servo_son = count( $servo_notlar ) - 1;
foreach ( $servo_notlar as $servo_i => $servo_not ) {
	$servo_style = $servo_i < $servo_son ? ' style="margin-top:26px"' : '';
	echo '<p class="appfoot"' . $servo_style . '>' . servo_text( $servo_not['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
