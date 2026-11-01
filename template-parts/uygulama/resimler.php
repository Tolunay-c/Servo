<?php
/**
 * Uygulama sayfası: "Uygulama Resimleri" bölümü (koyu hero'dan hemen sonra, koyu zeminli).
 *
 * Resimler uygulama kaydındaki iki görsel alanından gelir (uygulama_gorsel_1 / _2), kendi
 * oranlarında yan yana (mobilde alt alta) basılır; alt yazı alanları (_aciklama) doluysa
 * resmin altında gösterilir. Başlık yapısı diğer bölümlerle aynıdır (.sec-hd: sarı çizgi,
 * mono üst etiket, büyük harfli başlık). İkisi de boşsa bölüm çıkmaz.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_resimler = array();
foreach ( array( 1, 2 ) as $servo_no ) {
	$servo_id = (int) servo_field( "uygulama_gorsel_{$servo_no}", get_the_ID(), 0 );
	if ( $servo_id ) {
		$servo_resimler[] = array( $servo_id, (string) servo_field( "uygulama_gorsel_{$servo_no}_aciklama", get_the_ID(), '' ) );
	}
}

if ( ! $servo_resimler ) {
	return;
}

echo '<section class="section dark uyg-resimler"><div class="wrap">';
echo '<div class="sec-hd"><div class="rule"></div><p class="tagline">' . esc_html__( 'SAHADAN', 'servomotion' ) . '</p><h2>' . esc_html__( 'Uygulama Resimleri', 'servomotion' ) . '</h2></div>';
echo '<div class="uyg-resim-grid">';
foreach ( $servo_resimler as $servo_r ) {
	echo '<figure>' . wp_get_attachment_image( $servo_r[0], 'full', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( '' !== $servo_r[1] ) {
		echo '<figcaption>' . esc_html( $servo_r[1] ) . '</figcaption>';
	}
	echo '</figure>';
}
echo '</div></div></section>';
