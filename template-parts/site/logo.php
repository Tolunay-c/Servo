<?php
/**
 * Logo: SVG işaret + "ServoMotion" yazısı.
 *
 * Argümanlar:
 * - variant: 'standart' (header.nav) veya 'urun' (ürün sayfası nav'ı:
 *   işaret ve yazı <b> içinde, ardından alt yazı <span>).
 *
 * Tasarımda <a> içinde boşluk yoktur; çıktı tek parça üretilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_variant = isset( $args['variant'] ) ? $args['variant'] : 'standart';

// Logo SVG'si temanın kendi dosyasıdır; kullanıcı girdisi değildir.
$servo_logo = servo_get_svg( 'logo-isaret' ) . '<bdi lang="en"><i class="lo1">Servo</i><i class="lo2">Motion</i></bdi>';

if ( 'urun' === $servo_variant ) {
	$servo_logo    = '<b>' . $servo_logo . '</b>';
	$servo_tagline = servo_option( 'logo_alt_yazi' );

	if ( $servo_tagline ) {
		$servo_logo .= '<span>' . esc_html( $servo_tagline ) . '</span>';
	}
}

echo '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '">' . trim( $servo_logo ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
