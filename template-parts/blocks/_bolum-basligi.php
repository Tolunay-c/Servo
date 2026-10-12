<?php
/**
 * Blok parçası: bölüm başlığı.
 *
 * Standart: <div class="section-head"> (etiket, h2, açıklama).
 * Çizgili:  <div class="sec-hd"> (çizgi, küçük etiket, h2, açıklama).
 * Koyu zeminde açıklama paragrafları tasarımdaki açık renkle basılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

if ( ! servo_has_section_head() ) {
	return;
}

$servo_etiket   = servo_sub( 'etiket' );
$servo_baslik   = servo_sub( 'baslik' );
$servo_aciklama = servo_sub( 'aciklama' );
$servo_p_atts   = 'koyu' === servo_sub( 'arka_plan' ) ? ' style="color:#b4c2d1"' : '';

if ( 'cizgili' === servo_sub( 'baslik_stili' ) ) {
	echo '<div class="sec-hd"><div class="rule"></div>';
	if ( $servo_etiket ) {
		echo '<p class="tagline">' . esc_html( $servo_etiket ) . '</p>';
	}
} else {
	echo '<div class="section-head">';
	if ( $servo_etiket ) {
		echo '<p class="label">' . servo_text( $servo_etiket ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( $servo_baslik ) {
	echo '<h2>' . servo_text( $servo_baslik ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo servo_paragraphs( (string) $servo_aciklama, $servo_p_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '</div>';
