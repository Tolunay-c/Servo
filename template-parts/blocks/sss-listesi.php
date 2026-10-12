<?php
/**
 * Blok: Sıkça sorulan sorular (.qwrap > section.qgroup > details.qa).
 *
 * Sorular sss kayıtlarından, gruplar sss_grup taxonomy'sinden gelir.
 * Aynı sorular için FAQPage JSON-LD'yi servo_sss_jsonld() basar.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_gruplar = servo_sss_gruplari( (array) servo_sub( 'gruplar', array() ) );
$servo_not     = servo_sub( 'not' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap qwrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

foreach ( $servo_gruplar as $servo_g ) {
	echo '<section class="qgroup"><h2>' . esc_html( $servo_g['term']->name ) . '</h2>';
	foreach ( $servo_g['sorular'] as $servo_soru ) {
		echo '<details class="qa"><summary>' . esc_html( get_the_title( $servo_soru ) ) . '</summary>';
		echo '<div class="qa-a">' . wp_kses_post( wpautop( $servo_soru->post_content ) ) . '</div></details>';
	}
	echo '</section>';
}

if ( $servo_not ) {
	echo '<p class="appfoot" style="margin-top:10px">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
