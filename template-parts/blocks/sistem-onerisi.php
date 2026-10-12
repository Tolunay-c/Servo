<?php
/**
 * Blok: Sistem önerisi (.pickbox + .casebox).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_secenekler = (array) servo_sub( 'secenekler', array() );
$servo_o_etiket   = servo_sub( 'ornek_etiket' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_secenekler ) {
	echo '<div class="pickbox">';
	foreach ( $servo_secenekler as $servo_s ) {
		echo empty( $servo_s['one_cikan'] ) ? '<div class="pick">' : '<div class="pick on">';
		if ( $servo_s['ust_etiket'] ) {
			echo '<p class="t">' . esc_html( $servo_s['ust_etiket'] ) . '</p>';
		}
		echo '<h3>' . esc_html( $servo_s['baslik'] ) . '</h3>';
		if ( $servo_s['metin'] ) {
			echo '<p>' . servo_text( $servo_s['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $servo_s['link']['url'] ) ) {
			echo '<a' . servo_link_atts( $servo_s['link'] ) . '>' . esc_html( $servo_s['link']['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
	echo '</div>';
}

if ( $servo_o_etiket ) {
	echo '<div class="casebox" style="margin-top:26px"><p class="k">' . esc_html( $servo_o_etiket ) . '</p>';
	if ( servo_sub( 'ornek_deger' ) ) {
		echo '<p class="v">' . esc_html( servo_sub( 'ornek_deger' ) ) . '</p>';
	}
	if ( servo_sub( 'ornek_metin' ) ) {
		echo '<p>' . servo_text( servo_sub( 'ornek_metin' ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

echo '</div></section>';
