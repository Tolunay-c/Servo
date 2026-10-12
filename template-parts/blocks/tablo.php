<?php
/**
 * Blok: Tablo.
 *
 * Görünümler:
 * - karsilastirma : section.cmp > table.ctable (sütun başlıkları, satırlar, ikonlu son satır)
 * - parametre     : section.section > table.meas (başlık + açıklama)
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_gorunum = servo_sub( 'gorunum', 'karsilastirma' );
$servo_not     = servo_sub( 'not' );

if ( 'parametre' === $servo_gorunum ) {
	$servo_parametreler = (array) servo_sub( 'parametreler', array() );

	echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="wrap">';
	get_template_part( 'template-parts/blocks/_bolum-basligi' );

	if ( $servo_parametreler ) {
		echo '<table class="meas">';
		foreach ( $servo_parametreler as $servo_p ) {
			echo '<tr><th>' . esc_html( $servo_p['baslik'] ) . '</th><td>' . servo_text( $servo_p['aciklama'] ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</table>';
	}
} else {
	$servo_sutunlar = (array) servo_sub( 'sutunlar', array() );
	$servo_satirlar = (array) servo_sub( 'satirlar', array() );
	$servo_altyapi  = (array) servo_sub( 'altyapi', array() );
	$servo_a_etiket = servo_sub( 'altyapi_etiket' );
	$servo_durum    = array(
		'evet'  => 'yes',
		'hayir' => 'no',
	);

	echo servo_section_open( 'cmp', '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<div class="wrap">';
	get_template_part( 'template-parts/blocks/_bolum-basligi' );
	echo '<div class="tbl-scroll"><table class="ctable">';

	if ( $servo_sutunlar ) {
		echo '<thead><tr>';
		foreach ( $servo_sutunlar as $servo_s ) {
			echo '<th>' . servo_text( $servo_s['baslik'] ) . '</th>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</tr></thead>';
	}

	echo '<tbody>';
	foreach ( $servo_satirlar as $servo_satir ) {
		echo '<tr><td>' . esc_html( $servo_satir['etiket'] ) . '</td>';
		foreach ( (array) $servo_satir['hucreler'] as $servo_h ) {
			$servo_class = isset( $servo_durum[ $servo_h['durum'] ] ) ? ' class="' . $servo_durum[ $servo_h['durum'] ] . '"' : '';
			echo '<td' . $servo_class . '>' . servo_text( $servo_h['metin'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $servo_h['kucuk_not'] ) {
				echo '<span class="sm">' . esc_html( $servo_h['kucuk_not'] ) . '</span>';
			}
			echo '</td>';
		}
		echo '</tr>';
	}

	if ( $servo_a_etiket && $servo_altyapi ) {
		$servo_colspan = max( 1, count( $servo_sutunlar ) - 1 );
		echo '<tr><td>' . esc_html( $servo_a_etiket ) . '</td><td colspan="' . (int) $servo_colspan . '"><div class="infra">';
		foreach ( $servo_altyapi as $servo_a ) {
			echo '<div class="inf">' . servo_get_svg( (string) $servo_a['ikon'] ) . '<span>' . esc_html( $servo_a['metin'] ) . '</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></td></tr>';
	}

	echo '</tbody></table></div>';
}

if ( $servo_not ) {
	echo '<p class="note">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
