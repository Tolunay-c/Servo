<?php
/**
 * Blok: Ürün kartları (section.section#sistemler).
 *
 * Kartların görünümü #sistemler seçicisine bağlı olduğundan ID sabittir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_kartlar = (array) servo_sub( 'kartlar', array() );
$servo_not     = servo_sub( 'not' );

echo servo_section_open( 'section', 'sistemler' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_kartlar ) {
	echo '<div class="products">';
	foreach ( $servo_kartlar as $servo_kart ) {
		echo '<article class="product">';

		$servo_gorsel = servo_visual( $servo_kart['gorsel'], $servo_kart['gorsel_svg'], 'large', array( 'loading' => 'lazy' ) );
		if ( $servo_gorsel ) {
			echo '<div class="visual">' . $servo_gorsel . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="product-content">';
		if ( $servo_kart['etiket'] ) {
			echo '<span class="label">' . esc_html( $servo_kart['etiket'] ) . '</span>';
		}
		if ( $servo_kart['baslik'] ) {
			echo '<h3>' . esc_html( $servo_kart['baslik'] ) . '</h3>';
		}
		if ( $servo_kart['metin'] ) {
			echo '<p>' . servo_text( $servo_kart['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $servo_kart['ozellikler'] ) ) {
			echo '<div class="specs">';
			foreach ( $servo_kart['ozellikler'] as $servo_oz ) {
				echo '<span><b>' . esc_html( $servo_oz['deger'] ) . '</b>' . esc_html( $servo_oz['aciklama'] ) . '</span>';
			}
			echo '</div>';
		}
		if ( ! empty( $servo_kart['buton']['url'] ) ) {
			$servo_aria = $servo_kart['buton_aciklama'] ? ' aria-label="' . esc_attr( $servo_kart['buton_aciklama'] ) . '"' : '';
			echo '<a class="btn"' . servo_link_atts( $servo_kart['buton'] ) . $servo_aria . '>' . esc_html( $servo_kart['buton']['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div></article>';
	}
	echo '</div>';
}

if ( $servo_not ) {
	echo '<p class="note">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
