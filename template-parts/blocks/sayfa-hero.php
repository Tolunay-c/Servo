<?php
/**
 * Blok: Sayfa başlığı (hero).
 *
 * Düzenler:
 * - standart : section.phero; sağda rakamlar (.pfacts) veya görsel (.pvis)
 * - kurumsal : section.company-hero; sağda künye kutuları (.company-facts)
 * - iletisim : section.ch; sağda iletişim kanalları (.chan), altta şerit (.chstrip)
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_duzen    = servo_sub( 'duzen', 'standart' );
$servo_yan      = 'standart' === $servo_duzen ? servo_sub( 'yan_icerik', 'yok' ) : 'yok';
$servo_etiket   = servo_sub( 'etiket' );
$servo_baslik   = servo_sub( 'baslik' );
$servo_vurgu    = servo_sub( 'vurgu' );
$servo_giris    = servo_sub( 'giris' );
$servo_butonlar = servo_buttons( servo_sub( 'butonlar', array() ) );
$servo_classes  = array(
	'standart' => 'phero',
	'kurumsal' => 'company-hero',
	'iletisim' => 'ch',
);

// Rakam kutuları (.pfacts).
$servo_rakamlar = '';
if ( in_array( $servo_yan, array( 'rakamlar', 'gorsel' ), true ) ) {
	foreach ( (array) servo_sub( 'rakamlar', array() ) as $servo_r ) {
		$servo_rakamlar .= '<div><b>' . esc_html( $servo_r['deger'] );
		if ( $servo_r['birim'] ) {
			$servo_rakamlar .= '<small>' . esc_html( $servo_r['birim'] ) . '</small>';
		}
		$servo_rakamlar .= '</b><span>' . esc_html( $servo_r['aciklama'] ) . '</span></div>';
	}
}

echo servo_section_open( isset( $servo_classes[ $servo_duzen ] ) ? $servo_classes[ $servo_duzen ] : 'phero', '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap"><div>';

$servo_ust_yol = 'standart' === $servo_duzen ? servo_sub( 'ust_yol' ) : null;
if ( is_array( $servo_ust_yol ) && ! empty( $servo_ust_yol['url'] ) ) {
	echo '<p class="crumb" style="font:11px \'IBM Plex Mono\',monospace;letter-spacing:.16em;color:#7f90a3;margin-bottom:14px"><a href="' . esc_url( $servo_ust_yol['url'] ) . '" style="color:#7f90a3;text-decoration:none">' . esc_html( $servo_ust_yol['title'] ) . '</a>';
	if ( servo_sub( 'ust_yol_son' ) ) {
		echo ' · ' . esc_html( servo_sub( 'ust_yol_son' ) );
	}
	echo '</p>';
}

if ( $servo_etiket ) {
	echo '<p class="label">' . esc_html( $servo_etiket ) . '</p>';
}

if ( $servo_baslik || $servo_vurgu ) {
	echo '<h1>' . esc_html( (string) $servo_baslik );
	if ( $servo_vurgu ) {
		echo ( $servo_baslik ? '<br>' : '' ) . '<em>' . esc_html( $servo_vurgu ) . '</em>';
	}
	echo '</h1>';
}

if ( $servo_giris ) {
	echo '<p class="intro">' . servo_text( $servo_giris ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( $servo_butonlar ) {
	echo '<div class="actions">' . $servo_butonlar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( 'gorsel' === $servo_yan && $servo_rakamlar ) {
	echo '<div class="pfacts">' . $servo_rakamlar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div>';

if ( 'rakamlar' === $servo_yan && $servo_rakamlar ) {
	echo '<div class="pfacts" style="margin-top:0">' . $servo_rakamlar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( 'gorsel' === $servo_yan ) {
	$servo_gorsel = servo_visual( servo_sub( 'gorsel' ), servo_sub( 'gorsel_svg', '' ) );
	if ( $servo_gorsel ) {
		echo '<div class="pvis">' . $servo_gorsel . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( 'kurumsal' === $servo_duzen ) {
	$servo_kunye = (array) servo_sub( 'kunye', array() );
	if ( $servo_kunye ) {
		echo '<aside class="company-facts" aria-label="' . esc_attr( (string) servo_sub( 'kunye_aciklama', '' ) ) . '">';
		foreach ( $servo_kunye as $servo_k ) {
			echo '<div><strong>' . esc_html( $servo_k['deger'] ) . '</strong><span>' . esc_html( $servo_k['aciklama'] ) . '</span></div>';
		}
		echo '</aside>';
	}
}

if ( 'iletisim' === $servo_duzen ) {
	$servo_telefon  = servo_option( 'telefon' );
	$servo_whatsapp = servo_option( 'whatsapp' );
	$servo_eposta   = servo_option( 'eposta' );
	$servo_kanallar = array();

	if ( $servo_telefon ) {
		$servo_kanallar[] = array( servo_tel_href( $servo_telefon ), false, 'kanal-telefon', servo_sub( 'kanal_telefon', '' ), $servo_telefon );
	}
	if ( $servo_whatsapp ) {
		$servo_kanallar[] = array( servo_whatsapp_href( $servo_whatsapp ), true, 'kanal-whatsapp', servo_sub( 'kanal_whatsapp', '' ), $servo_whatsapp );
	}
	if ( $servo_eposta ) {
		$servo_kanallar[] = array( 'mailto:' . $servo_eposta, false, 'kanal-eposta', servo_sub( 'kanal_eposta', '' ), $servo_eposta );
	}

	if ( $servo_kanallar ) {
		echo '<div class="chan">';
		foreach ( $servo_kanallar as $servo_kanal ) {
			echo '<a href="' . esc_url( $servo_kanal[0] ) . '"' . ( $servo_kanal[1] ? ' target="_blank" rel="noopener"' : '' ) . '>';
			echo '<span class="ic">' . servo_get_svg( $servo_kanal[2] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<span><span class="k">' . esc_html( $servo_kanal[3] ) . '</span><span class="v">' . esc_html( $servo_kanal[4] ) . '</span></span><span class="go">→</span></a>';
		}
		echo '</div>';
	}
}

echo '</div>';

if ( 'iletisim' === $servo_duzen ) {
	$servo_serit = (array) servo_sub( 'serit', array() );
	if ( $servo_serit ) {
		echo '<div class="chstrip"><div class="wrap">';
		foreach ( $servo_serit as $servo_s ) {
			echo '<div><b>' . esc_html( $servo_s['baslik'] ) . '</b>' . servo_text( $servo_s['metin'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></div>';
	}
}

echo '</section>';
