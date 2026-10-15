<?php
/**
 * Blok: Madde listesi.
 *
 * Görünümler: belirti (.symlist, 2 sütun), ihtiyac (.needrow, 3 sütun).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_ihtiyac  = 'ihtiyac' === servo_sub( 'gorunum', 'belirti' );
$servo_maddeler = (array) servo_sub( 'maddeler', array() );
$servo_not      = servo_sub( 'not' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_maddeler ) {
	echo $servo_ihtiyac ? '<div class="needrow">' : '<div class="symlist">';
	foreach ( $servo_maddeler as $servo_m ) {
		echo $servo_ihtiyac ? '<div class="need">' : '<div class="sym">';
		echo '<b>' . esc_html( $servo_m['baslik'] ) . '</b>';
		if ( $servo_m['metin'] ) {
			echo '<p>' . servo_text( $servo_m['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
	echo '</div>';
}

$servo_rakamlar = (array) servo_sub( 'rakamlar', array() );
if ( $servo_rakamlar ) {
	echo '<div class="pfacts" style="margin-top:30px">';
	foreach ( $servo_rakamlar as $servo_r ) {
		echo '<div><b>' . esc_html( $servo_r['deger'] ) . ( $servo_r['birim'] ? '<small>' . esc_html( $servo_r['birim'] ) . '</small>' : '' ) . '</b><span>' . esc_html( $servo_r['aciklama'] ) . '</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

if ( $servo_not ) {
	echo '<p class="note" style="margin-top:28px;border-left:3px solid var(--yellow);padding-left:15px;font-size:15px;color:#506275;line-height:1.65">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
