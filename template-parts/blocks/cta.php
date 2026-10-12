<?php
/**
 * Blok: Çağrı bandı (CTA).
 *
 * Görünümler:
 * - iletisim : section.contact (sarı, sağda bağlantı listesi)
 * - bant     : section.cta-band (sağda buton)
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_iletisim = 'iletisim' === servo_sub( 'gorunum', 'bant' );
$servo_baslik   = servo_sub( 'baslik' );
$servo_metin    = servo_sub( 'metin' );
$servo_linkler  = '';

foreach ( (array) servo_sub( 'linkler', array() ) as $servo_l ) {
	$servo_class = $servo_iletisim ? '' : ' class="btn"';

	if ( 'eposta' === $servo_l['tur'] ) {
		$servo_eposta = servo_option( 'eposta' );
		if ( $servo_eposta ) {
			$servo_href     = 'mailto:' . $servo_eposta . ( $servo_l['konu'] ? '?subject=' . rawurlencode( $servo_l['konu'] ) : '' );
			$servo_linkler .= '<a' . $servo_class . ' href="' . esc_url( $servo_href ) . '">' . esc_html( strtoupper( $servo_eposta ) ) . '</a>';
		}
	} elseif ( 'telefon' === $servo_l['tur'] ) {
		$servo_telefon = servo_option( 'telefon' );
		if ( $servo_telefon ) {
			$servo_linkler .= '<a' . $servo_class . ' href="' . esc_url( servo_tel_href( $servo_telefon ) ) . '">' . esc_html( $servo_telefon ) . '</a>';
		}
	} elseif ( ! empty( $servo_l['link']['url'] ) ) {
		$servo_linkler .= '<a' . $servo_class . servo_link_atts( $servo_l['link'] ) . '>' . esc_html( $servo_l['link']['title'] ) . '</a>';
	}
}

echo servo_section_open( $servo_iletisim ? 'contact' : 'cta-band', '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap"><div>';
if ( $servo_baslik ) {
	echo '<h2>' . servo_text( $servo_baslik ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
if ( $servo_metin ) {
	echo '<p>' . servo_text( $servo_metin ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
echo '</div>';

if ( $servo_linkler ) {
	echo $servo_iletisim ? '<div class="contact-links">' . $servo_linkler . '</div>' : $servo_linkler; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
