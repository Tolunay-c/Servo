<?php
/**
 * Blok: Servis kanalları (.svcgrid > .svc).
 *
 * Simge, başlık, metin ve bağlantılı kartlar. Simgeler assets/img/svg/servis-*.svg
 * dosyalarıdır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_kartlar = (array) servo_sub( 'kartlar', array() );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_kartlar ) {
	echo '<div class="svcgrid">';
	foreach ( $servo_kartlar as $servo_k ) {
		echo '<div class="svc">';
		if ( ! empty( $servo_k['ikon'] ) ) {
			echo '<div class="ic">' . servo_get_svg( $servo_k['ikon'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası.
		}
		if ( $servo_k['baslik'] ) {
			echo '<h3>' . esc_html( $servo_k['baslik'] ) . '</h3>';
		}
		if ( $servo_k['metin'] ) {
			echo '<p>' . servo_text( $servo_k['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $servo_k['link']['url'] ) ) {
			echo '<a class="go"' . servo_link_atts( $servo_k['link'] ) . '>' . esc_html( $servo_k['link']['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
	echo '</div>';
}

echo '</div></section>';
