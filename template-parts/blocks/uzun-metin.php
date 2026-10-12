<?php
/**
 * Blok: Uzun metin (.wrap.legal).
 *
 * Başlıklı bölümler; her bölümde isteğe bağlı bilgi kutusu (.kv) ve
 * editör metni. KVKK ve benzeri yasal metinler için.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_ust = servo_sub( 'ust_not' );
$servo_alt = servo_sub( 'alt_not' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap legal">';

if ( $servo_ust ) {
	echo '<p class="upd">' . esc_html( $servo_ust ) . '</p>';
}

foreach ( (array) servo_sub( 'bolumler', array() ) as $servo_b ) {
	if ( $servo_b['baslik'] ) {
		echo '<h2>' . esc_html( $servo_b['baslik'] ) . '</h2>';
	}

	$servo_satirlar = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $servo_b['kutu'] ) ) );
	if ( $servo_satirlar ) {
		echo '<div class="kv">';
		foreach ( $servo_satirlar as $servo_satir ) {
			echo '<p>' . wp_kses_post( $servo_satir ) . '</p>';
		}
		echo '</div>';
	}

	if ( $servo_b['metin'] ) {
		echo wp_kses_post( $servo_b['metin'] );
	}
}

if ( $servo_alt ) {
	echo '<p class="upd" style="margin-top:34px">' . esc_html( $servo_alt ) . '</p>';
}

echo '</div></section>';
