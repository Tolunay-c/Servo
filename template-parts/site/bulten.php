<?php
/**
 * Bülten bandı: section.news#bulten.
 *
 * E-posta servomotion/v1/bulten uç noktasına kaydedilir (assets/js/bulten-kayit.js;
 * tasarımın bulten.js / rehber-*.js dosyalarından önce çalışır, mailto açmaz). Formun altında
 * KVKK aydınlatma notu vardır. Rehber sayfalarında buton class'ı tasarımdaki gibi "btn primary"dır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_baslik = servo_option( 'bulten_baslik' );
$servo_metin  = servo_option( 'bulten_metin' );
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
<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden"><label for="nweb"><?php esc_html_e( 'Web sitesi', 'servomotion' ); ?></label><input id="nweb" name="web" type="text" tabindex="-1" autocomplete="off"></div>
<button class="<?php echo esc_attr( $servo_btn ); ?>" type="submit"><?php esc_html_e( 'KAYDOLUN', 'servomotion' ); ?></button></form>
<p class="newsnote" id="nnote"><?php esc_html_e( 'E-bülten aboneliği için kaydolun', 'servomotion' ); ?></p>
<p class="newsnote newskvkk"><?php
printf(
	/* translators: %s: KVKK aydınlatma metni bağlantısı */
	esc_html__( 'E-posta adresiniz yalnızca bülten göndermek için işlenir; aboneliğinizi her zaman sonlandırabilirsiniz. Ayrıntılar için %s.', 'servomotion' ),
	'<a href="' . esc_url( servo_bulten_kvkk_adresi() ) . '">' . esc_html__( 'KVKK aydınlatma metnimize bakın', 'servomotion' ) . '</a>'
); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?></p>
</div></div></section>
