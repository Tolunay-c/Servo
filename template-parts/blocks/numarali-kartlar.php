<?php
/**
 * Blok: Numaralı kartlar.
 *
 * Görünümler: kazanim (.benefits), adim (.steps2), akis (.flow),
 * zaman (.milestones).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_gorunum = servo_sub( 'gorunum', 'adim' );
$servo_ogeler  = (array) servo_sub( 'ogeler', array() );
$servo_kapsar  = array(
	'kazanim' => 'benefits',
	'adim'    => 'steps2',
	'akis'    => 'flow',
	'zaman'   => 'milestones',
);

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_ogeler ) {
	echo '<div class="' . esc_attr( isset( $servo_kapsar[ $servo_gorunum ] ) ? $servo_kapsar[ $servo_gorunum ] : 'steps2' ) . '">';

	foreach ( $servo_ogeler as $servo_o ) {
		$servo_h3 = $servo_o['baslik'] ? '<h3>' . esc_html( $servo_o['baslik'] ) . '</h3>' : '';
		$servo_p  = $servo_o['metin'] ? '<p>' . servo_text( $servo_o['metin'] ) . '</p>' : '';
		$servo_no = esc_html( (string) $servo_o['numara'] );

		switch ( $servo_gorunum ) {
			case 'kazanim':
				echo '<article><div class="number">' . $servo_no . '</div>' . $servo_h3 . $servo_p . '</article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'akis':
				$servo_sure = $servo_o['sure'] ? '<span class="t">' . esc_html( $servo_o['sure'] ) . '</span>' : '';
				echo '<article class="fstep"><p class="n">' . $servo_no . '</p>' . $servo_h3 . $servo_p . $servo_sure . '</article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'zaman':
				$servo_alt = $servo_o['alt_etiket'] ? '<span>' . esc_html( $servo_o['alt_etiket'] ) . '</span>' : '';
				echo '<article><div class="milestone-key">' . $servo_no . $servo_alt . '</div><div>' . $servo_h3 . $servo_p . '</div></article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			default:
				echo '<article><p class="n">' . $servo_no . '</p><div>' . $servo_h3 . $servo_p . '</div></article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
		}
	}

	echo '</div>';
}

echo '</div></section>';
