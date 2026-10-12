<?php
/**
 * Blok: İletişim bilgileri.
 *
 * Değerler Site Ayarları'ndan gelir. Görünümler:
 * - dort_kutu : section.section.info > .igrid (merkez, telefon, e-posta, kurumsal)
 * - iki_kolon : .company-contact (merkez + iletişim)
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_dort    = 'iki_kolon' !== servo_sub( 'gorunum', 'dort_kutu' );
$servo_adres   = servo_option( 'adres' );
$servo_telefon = servo_option( 'telefon' );
$servo_eposta  = servo_option( 'eposta' );

echo servo_section_open( $servo_dort ? 'section info' : 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_dort ) {
	$servo_whatsapp = servo_option( 'whatsapp' );
	$servo_faks     = servo_option( 'faks' );
	$servo_eposta_2 = servo_option( 'eposta_2' );
	$servo_unvan    = servo_option( 'firma_unvani' );
	$servo_vergi    = servo_option( 'vergi' );
	$servo_harita   = servo_option( 'harita_url' );
	$servo_markalar = array();

	foreach ( (array) servo_option( 'markalar', array() ) as $servo_m ) {
		if ( $servo_m['ad'] ) {
			$servo_markalar[] = $servo_m['url'] ? '<a href="' . esc_url( $servo_m['url'] ) . '" target="_blank" rel="noopener">' . esc_html( $servo_m['ad'] ) . '</a>' : esc_html( $servo_m['ad'] );
		}
	}

	echo '<div class="igrid">';

	echo '<div class="ibox"><h3>' . esc_html__( 'MERKEZ', 'servomotion' ) . '</h3>';
	if ( $servo_adres ) {
		echo '<address>' . servo_text( $servo_adres ) . '</address>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $servo_harita ) {
		echo '<a class="maplink" href="' . esc_url( $servo_harita ) . '" target="_blank" rel="noopener">' . servo_get_svg( 'kanal-harita' ) . ' ' . esc_html__( 'Yol tarifi alın →', 'servomotion' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';

	echo '<div class="ibox"><h3>' . esc_html__( 'TELEFON', 'servomotion' ) . '</h3>';
	if ( $servo_telefon ) {
		echo '<p><a href="' . esc_url( servo_tel_href( $servo_telefon ) ) . '">' . esc_html( $servo_telefon ) . '</a></p>';
	}
	if ( $servo_whatsapp ) {
		echo '<p><a href="' . esc_url( servo_tel_href( $servo_whatsapp ) ) . '">' . esc_html( $servo_whatsapp ) . '</a> <span style="font-size:14px;color:#7a8a9b">' . esc_html__( '· WhatsApp', 'servomotion' ) . '</span></p>';
	}
	if ( $servo_faks ) {
		echo '<p class="lbl">' . esc_html__( 'FAKS', 'servomotion' ) . '</p><p>' . esc_html( $servo_faks ) . '</p>';
	}
	echo '</div>';

	echo '<div class="ibox"><h3>' . esc_html__( 'E-POSTA', 'servomotion' ) . '</h3>';
	foreach ( array( array( servo_option( 'eposta_etiket' ), $servo_eposta ), array( servo_option( 'eposta_2_etiket' ), $servo_eposta_2 ) ) as $servo_e ) {
		if ( ! $servo_e[1] ) {
			continue;
		}
		if ( $servo_e[0] ) {
			echo '<p class="lbl">' . esc_html( $servo_e[0] ) . '</p>';
		}
		echo '<p><a href="' . esc_url( 'mailto:' . $servo_e[1] ) . '">' . esc_html( $servo_e[1] ) . '</a></p>';
	}
	echo '</div>';

	echo '<div class="ibox"><h3>' . esc_html__( 'KURUMSAL', 'servomotion' ) . '</h3>';
	if ( $servo_unvan ) {
		echo '<p>' . esc_html( $servo_unvan ) . '</p>';
	}
	if ( $servo_vergi ) {
		echo '<p class="lbl">' . esc_html__( 'VERGİ DAİRESİ / NO', 'servomotion' ) . '</p><p>' . esc_html( $servo_vergi ) . '</p>';
	}
	if ( $servo_markalar ) {
		echo '<p class="lbl">' . esc_html__( 'MARKALAR', 'servomotion' ) . '</p><p>' . implode( ' · ', $servo_markalar ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';

	echo '</div>';
} else {
	echo '<div class="company-contact"><div><h3>' . esc_html__( 'MERKEZ', 'servomotion' ) . '</h3>';
	if ( $servo_adres ) {
		echo '<address>' . servo_text( $servo_adres ) . '</address>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div><div>';
	if ( servo_sub( 'ikinci_baslik' ) ) {
		echo '<h3>' . esc_html( servo_sub( 'ikinci_baslik' ) ) . '</h3>';
	}
	if ( $servo_eposta ) {
		echo '<a href="' . esc_url( 'mailto:' . $servo_eposta ) . '">' . esc_html( strtoupper( $servo_eposta ) ) . '</a>';
	}
	if ( $servo_telefon ) {
		echo '<a href="' . esc_url( servo_tel_href( $servo_telefon ) ) . '">' . esc_html( $servo_telefon ) . '</a>';
	}
	echo '</div></div>';
}

echo '</div></section>';
