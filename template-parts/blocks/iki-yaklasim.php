<?php
/**
 * Blok: İki yaklaşım + rakamlar (section.catband).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_rakamlar = (array) servo_sub( 'rakamlar', array() );
$servo_not      = servo_sub( 'not' );

echo servo_section_open( 'catband', '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

echo '<div class="cat2">';
foreach ( array( 'sol' => 'cat cat-a', 'sag' => 'cat cat-b' ) as $servo_taraf => $servo_class ) {
	echo '<article class="' . esc_attr( $servo_class ) . '">';
	if ( servo_sub( $servo_taraf . '_etiket' ) ) {
		echo '<p class="k">' . esc_html( servo_sub( $servo_taraf . '_etiket' ) ) . '</p>';
	}
	if ( servo_sub( $servo_taraf . '_baslik' ) ) {
		echo '<h3>' . esc_html( servo_sub( $servo_taraf . '_baslik' ) ) . '</h3>';
	}
	if ( servo_sub( $servo_taraf . '_metin' ) ) {
		echo '<p>' . servo_text( servo_sub( $servo_taraf . '_metin' ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</article>';
}
echo '</div>';

if ( $servo_rakamlar ) {
	echo '<div class="catproof">';
	foreach ( $servo_rakamlar as $servo_r ) {
		echo '<div><b>' . esc_html( $servo_r['deger'] );
		if ( $servo_r['birim'] ) {
			echo '<small>' . esc_html( $servo_r['birim'] ) . '</small>';
		}
		echo '</b><span>' . esc_html( $servo_r['aciklama'] ) . '</span></div>';
	}
	echo '</div>';
}

if ( $servo_not ) {
	echo '<p class="catnote">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
