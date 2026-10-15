<?php
/**
 * Blok: İndirme listesi (.dl > a).
 *
 * Her bağlantı bir Medya dosyasına (indirme) ya da bir sayfaya gider; sağdaki
 * rozet dosya türünden (PDF, XLSX) veya elle yazılan metinden gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_dosyalar = (array) servo_sub( 'dosyalar', array() );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_dosyalar ) {
	echo '<div class="dl">';
	foreach ( $servo_dosyalar as $servo_d ) {
		$servo_id    = ! empty( $servo_d['dosya'] ) ? (int) $servo_d['dosya'] : 0;
		$servo_url   = $servo_id ? wp_get_attachment_url( $servo_id ) : '';
		$servo_rozet = (string) $servo_d['rozet'];

		if ( $servo_url ) {
			if ( '' === $servo_rozet ) {
				$servo_rozet = strtoupper( (string) pathinfo( $servo_url, PATHINFO_EXTENSION ) );
			}
			$servo_atts = ' href="' . esc_url( $servo_url ) . '" download';
		} elseif ( ! empty( $servo_d['link']['url'] ) ) {
			$servo_atts = servo_link_atts( $servo_d['link'] );
		} else {
			continue;
		}

		echo '<a' . $servo_atts . '>' . esc_html( $servo_d['baslik'] ) . ( '' !== $servo_rozet ? '<span>' . esc_html( $servo_rozet ) . '</span>' : '' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

// Tasarımda ilk not listeden 26px, sonrakiler birbirinden 22px uzakta.
$servo_ilk = true;
foreach ( (array) servo_sub( 'notlar', array() ) as $servo_n ) {
	if ( ! $servo_n['metin'] ) {
		continue;
	}
	$servo_style = 'margin-top:' . ( $servo_ilk ? '26px' : '22px' ) . ';border-left:3px solid var(--yellow);padding-left:15px;font-size:15px;color:#64758a;line-height:1.65';
	echo '<p class="catnote" style="' . esc_attr( $servo_style ) . '">' . servo_text( $servo_n['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$servo_ilk = false;
}

echo '</div></section>';
