<?php
/**
 * Blok: Ana sayfa hero (section.hero).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_etiket   = servo_sub( 'etiket' );
$servo_baslik   = servo_sub( 'baslik' );
$servo_vurgu    = servo_sub( 'vurgu' );
$servo_mesaj    = (array) servo_sub( 'mesaj', array() );
$servo_kapanis  = servo_sub( 'kapanis' );
$servo_butonlar = servo_buttons( servo_sub( 'butonlar', array() ) );
$servo_gorsel   = servo_sub( 'gorsel' );
$servo_g_baslik = servo_sub( 'gorsel_baslik' );
$servo_g_metin  = servo_sub( 'gorsel_metin' );
$servo_serit    = (array) servo_sub( 'serit', array() );

echo servo_section_open( 'hero', '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap hero-grid"><div>';

if ( $servo_etiket ) {
	echo '<p class="label">' . servo_text( $servo_etiket ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( $servo_baslik || $servo_vurgu ) {
	echo '<h1>' . esc_html( (string) $servo_baslik );
	if ( $servo_vurgu ) {
		echo ( $servo_baslik ? '<br>' : '' ) . '<em>' . esc_html( $servo_vurgu ) . '</em>';
	}
	echo '</h1>';
}

if ( $servo_mesaj || $servo_kapanis ) {
	echo '<div class="hero-message">';

	if ( $servo_mesaj ) {
		echo '<p class="hero-benefits">';
		foreach ( $servo_mesaj as $servo_satir ) {
			$servo_tag = empty( $servo_satir['vurgulu'] ) ? 'span' : 'strong';
			echo '<' . $servo_tag . '>' . esc_html( $servo_satir['metin'] ) . '</' . $servo_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</p>';
	}

	if ( $servo_kapanis ) {
		echo '<p class="hero-signoff">' . servo_text( $servo_kapanis ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	echo '</div>';
}

if ( $servo_butonlar ) {
	echo '<div class="actions">' . $servo_butonlar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div>';

if ( $servo_gorsel ) {
	echo '<figure>' . wp_get_attachment_image( $servo_gorsel, 'servo-hero', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) );

	if ( $servo_g_baslik || $servo_g_metin ) {
		echo '<figcaption>';
		if ( $servo_g_baslik ) {
			echo '<b>' . esc_html( $servo_g_baslik ) . '</b>';
		}
		if ( $servo_g_metin ) {
			echo '<span>' . esc_html( $servo_g_metin ) . '</span>';
		}
		echo '</figcaption>';
	}

	echo '</figure>';
}

echo '</div>';

if ( $servo_serit ) {
	echo '<div class="wrap"><div class="strip">';
	foreach ( $servo_serit as $servo_kutu ) {
		echo '<div>';
		if ( $servo_kutu['baslik'] ) {
			echo '<b>' . esc_html( $servo_kutu['baslik'] ) . '</b>';
		}
		if ( $servo_kutu['metin'] ) {
			echo '<p>' . servo_text( $servo_kutu['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $servo_kutu['alt_metin'] ) {
			echo '<span>' . esc_html( $servo_kutu['alt_metin'] ) . '</span>';
		}
		echo '</div>';
	}
	echo '</div></div>';
}

echo '</section>';
