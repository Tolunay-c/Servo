<?php
/**
 * Blok: Sözlük (.gsearchbox + .terms > article.term).
 *
 * Terimler terim kayıtlarından gelir. Arama assets/js/sozluk.js ile çalışır;
 * script #q, #list, #cnt ve #empty ID'lerine bağlı olduğundan bu ID'ler
 * sabittir ve blok bir sayfada bir kez kullanılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_terimler = servo_terimler( servo_sub( 'siralama', 'elle' ) );
$servo_etiket   = servo_sub( 'arama_etiket' );
$servo_ornek    = servo_sub( 'arama_ornek' );
$servo_bos      = servo_sub( 'bos_mesaj' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap"><div class="gsearchbox">';
if ( $servo_etiket ) {
	echo '<label for="q" style="display:block;font:10.5px/1.5 \'IBM Plex Mono\',monospace;letter-spacing:.14em;text-transform:uppercase;color:#7a8a9b;margin-bottom:8px">' . esc_html( $servo_etiket ) . '</label>';
}
echo '<input id="q" type="search"' . ( $servo_ornek ? ' placeholder="' . esc_attr( $servo_ornek ) . '"' : '' ) . ' autocomplete="off">';
/* translators: %d: terim sayısı */
echo '<p id="cnt" style="font:11px/1.6 \'IBM Plex Mono\',monospace;color:#7a8a9b;margin-top:10px">' . esc_html( sprintf( __( '%d terim', 'servomotion' ), count( $servo_terimler ) ) ) . '</p>';
echo '</div>';

echo '<h2 style="position:absolute;left:-9999px">' . esc_html__( 'Terimler', 'servomotion' ) . '</h2><div class="terms" id="list">';
foreach ( $servo_terimler as $servo_t ) {
	$servo_baslik = get_the_title( $servo_t );
	$servo_alt    = (string) servo_field( 'alternatif', $servo_t->ID, '' );
	$servo_metin  = trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $servo_t->post_content ) ) );
	// Arama metni: ad + açıklama + alternatif adlar (sozluk.js küçük harfe göre arar).
	$servo_ara = mb_strtolower( $servo_baslik . ' ' . $servo_metin . ' ' . $servo_alt, 'UTF-8' );

	echo '<article class="term" data-t="' . esc_attr( $servo_ara ) . '"><h3>' . esc_html( $servo_baslik ) . '</h3>';
	echo servo_paragraphs( $servo_t->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( '' !== $servo_alt ) {
		echo '<p class="alt">' . esc_html( $servo_alt ) . '</p>';
	}
	echo '</article>';
}
echo '</div>';

if ( $servo_bos ) {
	echo '<p id="empty" hidden style="border-left:3px solid var(--yellow);padding-left:15px;color:#64758a;font-size:15.5px">' . servo_text( $servo_bos ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '</div></section>';
