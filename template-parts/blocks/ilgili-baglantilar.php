<?php
/**
 * Blok: İlgili bağlantılar (.related-links).
 *
 * Vaka türündeki bağlantılar vaka çalışmaları sayfasındaki vakaya
 * (slug'dan üretilen çapa ile) gider.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_linkler = '';

foreach ( (array) servo_sub( 'baglantilar', array() ) as $servo_b ) {
	if ( 'vaka' === $servo_b['tur'] ) {
		$servo_url = $servo_b['vaka'] ? servo_vaka_url( $servo_b['vaka'] ) : '';
		if ( $servo_url && $servo_b['metin'] ) {
			$servo_linkler .= '<a href="' . esc_url( $servo_url ) . '">' . esc_html( $servo_b['metin'] ) . '</a>';
		}
	} elseif ( ! empty( $servo_b['link']['url'] ) ) {
		$servo_linkler .= '<a' . servo_link_atts( $servo_b['link'] ) . '>' . esc_html( $servo_b['link']['title'] ) . '</a>';
	}
}

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_linkler ) {
	echo '<div class="related-links">' . $servo_linkler . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
