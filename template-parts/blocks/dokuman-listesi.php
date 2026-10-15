<?php
/**
 * Blok: Doküman listesi (.dgrid > article.doc).
 *
 * Kartlar seçilen doküman kayıtlarından gelir. Dosyası yüklenmemiş doküman
 * gösterilmez. "Talep formuyla" erişimli dokümanlarda dosya bağlantısı verilmez;
 * kart, aynı sayfadaki talep formuna (#talep) gider.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_kartlar = '';

foreach ( (array) servo_sub( 'dokumanlar', array() ) as $servo_id ) {
	$servo_dosya = (int) servo_field( 'dosya', $servo_id, 0 );
	$servo_url   = $servo_dosya ? wp_get_attachment_url( $servo_dosya ) : '';
	$servo_formla = 'formla' === servo_field( 'erisim', $servo_id, 'acik' );

	if ( ! $servo_url ) {
		continue;
	}

	$servo_tur      = strtoupper( (string) pathinfo( (string) wp_parse_url( $servo_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$servo_aciklama = servo_field( 'aciklama', $servo_id );
	$servo_meta     = servo_field( 'meta', $servo_id );

	$servo_kartlar .= '<article class="doc"><div class="ic"><b>' . esc_html( $servo_tur ) . '</b></div><div>';
	$servo_kartlar .= '<h3>' . servo_text( servo_field( 'kart_baslik', $servo_id, get_the_title( $servo_id ) ) ) . '</h3>';
	if ( $servo_aciklama ) {
		$servo_kartlar .= '<p>' . servo_text( $servo_aciklama ) . '</p>';
	}
	if ( $servo_meta ) {
		$servo_kartlar .= '<p class="meta">' . esc_html( $servo_meta ) . '</p>';
	}
	if ( $servo_formla ) {
		$servo_kartlar .= '<a class="btn primary" href="#talep">' . esc_html__( 'TALEP EDİN', 'servomotion' ) . '</a>';
	} else {
		/* translators: %s: dosya türü (ör. PDF) */
		$servo_kartlar .= '<a class="btn primary" href="' . esc_url( $servo_url ) . '" download>' . esc_html( sprintf( __( 'İNDİRİN (%s)', 'servomotion' ), $servo_tur ) ) . '</a>';
	}
	$servo_kartlar .= '</div></article>';
}

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_kartlar ) {
	echo '<div class="dgrid">' . $servo_kartlar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// Tasarımda ilk not kartlardan 26px, sonrakiler birbirinden 22px uzakta.
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
