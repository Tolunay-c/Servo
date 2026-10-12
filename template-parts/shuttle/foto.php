<?php
/**
 * Shuttle Precisor: saha fotoğrafı (figure.fotobox).
 *
 * "Bölümü göster" kapalıysa veya fotoğraf yüklenmemişse bölüm basılmaz.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_foto = servo_field( 'foto_goster', false, false ) ? (int) servo_field( 'foto_gorsel', false, 0 ) : 0;

if ( ! $servo_foto ) {
	return;
}

$servo_aciklama = servo_field( 'foto_aciklama' );
?>
<section class="section" style="background:#fff"><div class="wrap"><figure class="fotobox"><?php echo wp_get_attachment_image( $servo_foto, 'servo-foto', false, array( 'loading' => 'lazy' ) ); ?><?php if ( $servo_aciklama ) : ?><figcaption><?php echo servo_urun_duz( $servo_aciklama ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figcaption><?php endif; ?></figure></div></section>
