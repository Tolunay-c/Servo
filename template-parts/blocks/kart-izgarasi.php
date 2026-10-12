<?php
/**
 * Blok: Kart ızgarası.
 *
 * Görünümler: marka (.brand-grid > .brand-card, isteğe bağlı .fotorow),
 * talep (.reqgrid > .req).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_talep  = 'talep' === servo_sub( 'gorunum', 'marka' );
$servo_kartlar = (array) servo_sub( 'kartlar', array() );
$servo_fotolar = $servo_talep ? array() : (array) servo_sub( 'fotograflar', array() );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_kartlar ) {
	echo $servo_talep ? '<div class="reqgrid">' : '<div class="brand-grid">';
	foreach ( $servo_kartlar as $servo_k ) {
		$servo_h3_style = ( ! $servo_talep && ! empty( $servo_k['marka_yazisi'] ) ) ? ' style="font-family:Poppins,sans-serif"' : '';

		echo $servo_talep ? '<article class="req">' : '<article class="brand-card">';
		echo '<h3' . $servo_h3_style . '>' . esc_html( $servo_k['baslik'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $servo_k['metin'] ) {
			echo '<p>' . servo_text( $servo_k['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $servo_k['link']['url'] ) ) {
			echo '<a' . servo_link_atts( $servo_k['link'] ) . '>' . esc_html( $servo_k['link']['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</article>';
	}
	echo '</div>';
}

if ( $servo_fotolar ) {
	echo '<div class="fotorow">';
	foreach ( $servo_fotolar as $servo_f ) {
		if ( ! $servo_f['gorsel'] ) {
			continue;
		}
		echo '<figure class="fotobox">' . wp_get_attachment_image( $servo_f['gorsel'], 'servo-foto', false, array( 'loading' => 'lazy' ) );
		if ( $servo_f['aciklama'] ) {
			echo '<figcaption>' . esc_html( $servo_f['aciklama'] ) . '</figcaption>';
		}
		echo '</figure>';
	}
	echo '</div>';
}

echo '</div></section>';
