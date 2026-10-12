<?php
/**
 * Shuttle Precisor: hareket videosu (section.video#video).
 *
 * "Bölümü göster" kapalıysa veya video dosyası yüklenmemişse bölüm basılmaz.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_video = servo_field( 'video_goster', false, false ) ? (int) servo_field( 'video_dosya', false, 0 ) : 0;
$servo_url   = $servo_video ? wp_get_attachment_url( $servo_video ) : '';

if ( ! $servo_url ) {
	return;
}

$servo_kapak    = (int) servo_field( 'video_kapak', false, 0 );
$servo_kapak    = $servo_kapak ? wp_get_attachment_image_url( $servo_kapak, 'full' ) : '';
$servo_alt_yazi = servo_field( 'video_alt_yazi' );
?>
<section class="video" id="video">
  <div class="wrap">
    <?php servo_urun_sec_hd( 'video' ); ?>
    <div class="vbox">
      <video controls preload="metadata" playsinline
             <?php if ( $servo_kapak ) : ?>poster="<?php echo esc_url( $servo_kapak ); ?>"<?php endif; ?>>
        <source src="<?php echo esc_url( $servo_url ); ?>" type="<?php echo esc_attr( (string) get_post_mime_type( $servo_video ) ); ?>">
        <?php esc_html_e( 'Tarayıcınız video etiketini desteklemiyor.', 'servomotion' ); ?>
        <a href="<?php echo esc_url( $servo_url ); ?>"><?php esc_html_e( 'Videoyu indirin', 'servomotion' ); ?></a>.
      </video>
    </div>
<?php if ( $servo_alt_yazi ) : ?>
    <p class="vcap"><?php echo servo_urun_metin( $servo_alt_yazi ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
  </div>
</section>
