<?php
/**
 * Bülten bandı: section.news#bulten.
 *
 * Form şimdilik tasarımın kendi davranışıyla çalışır (bulten.js /
 * rehber-*.js: "e-posta ile yazın" mesajı). Rehber sayfalarında buton
 * class'ı tasarımdaki gibi "btn primary"dır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_baslik = servo_option( 'bulten_baslik' );
$servo_metin  = servo_option( 'bulten_metin' );
$servo_eposta = servo_option( 'eposta' );
$servo_btn    = 'rehber' === servo_asset_group() ? 'btn primary' : 'newsbtn';
?>
<section class="news" id="bulten"><div class="wrap"><div>
<?php if ( $servo_baslik ) : ?>
<h2><?php echo esc_html( $servo_baslik ); ?></h2>
<?php endif; ?>
<?php if ( $servo_metin ) : ?>
<p><?php echo esc_html( $servo_metin ); ?></p>
<?php endif; ?>
</div><div>
<form class="newsform" id="nf"><input type="email" id="nmail" placeholder="<?php esc_attr_e( 'E-posta adresiniz', 'servomotion' ); ?>" aria-label="<?php esc_attr_e( 'E-posta adresiniz', 'servomotion' ); ?>" required>
<button class="<?php echo esc_attr( $servo_btn ); ?>" type="submit"><?php esc_html_e( 'KAYDOLUN', 'servomotion' ); ?></button></form>
<?php if ( $servo_eposta ) : ?>
<p class="newsnote" id="nnote"><?php esc_html_e( 'Sorularınız için:', 'servomotion' ); ?> <a href="<?php echo esc_url( 'mailto:' . $servo_eposta ); ?>"><?php echo esc_html( $servo_eposta ); ?></a></p>
<?php else : ?>
<p class="newsnote" id="nnote"></p>
<?php endif; ?>
</div></div></section>
