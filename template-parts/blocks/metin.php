<?php
/**
 * Blok: Metin.
 *
 * Düzenler:
 * - iki_kolon : .wrap.about (solda etiket + başlık, sağda paragraflar ve bağlantılar)
 * - hikaye    : .wrap.company-story (üstte bölüm başlığı, altta paragraflar)
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_iki_kolon   = 'iki_kolon' === servo_sub( 'duzen', 'hikaye' );
$servo_paragraflar = servo_paragraphs( (string) servo_sub( 'paragraflar', '' ) );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( $servo_iki_kolon ) {
	echo '<div class="wrap about"><div>';
	if ( servo_sub( 'etiket' ) ) {
		echo '<p class="label">' . servo_text( servo_sub( 'etiket' ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( servo_sub( 'baslik' ) ) {
		echo '<h2>' . servo_text( servo_sub( 'baslik' ) ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div><div>' . $servo_paragraflar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	foreach ( array_values( (array) servo_sub( 'baglantilar', array() ) ) as $servo_i => $servo_b ) {
		if ( empty( $servo_b['link']['url'] ) ) {
			continue;
		}
		$servo_style = $servo_i > 0 ? ' style="margin-left:26px"' : '';
		echo '<a' . servo_link_atts( $servo_b['link'] ) . $servo_style . '>' . esc_html( $servo_b['link']['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div></div>';
} else {
	echo '<div class="wrap company-story">';
	get_template_part( 'template-parts/blocks/_bolum-basligi' );
	if ( $servo_paragraflar ) {
		echo '<div>' . $servo_paragraflar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

echo '</section>';
