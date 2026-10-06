<?php
/**
 * Footer: kolonlar (footer_menu) ve alt satır (Site Ayarları).
 *
 * Ürün sayfalarında aynı içerik <footer class="foot"> ile basılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_unvan     = servo_option( 'firma_unvani' );
$servo_marka     = servo_option( 'marka_adi' );
$servo_marka_url = servo_option( 'marka_url' );
$servo_eposta    = servo_option( 'eposta' );
$servo_telefon   = servo_option( 'telefon' );

$servo_iletisim = array();

if ( $servo_eposta ) {
	$servo_iletisim[] = '<a href="' . esc_url( 'mailto:' . $servo_eposta ) . '">' . esc_html( $servo_eposta ) . '</a>';
}

if ( $servo_telefon ) {
	$servo_iletisim[] = '<a href="' . esc_url( servo_tel_href( $servo_telefon ) ) . '">' . esc_html( $servo_telefon ) . '</a>';
}
?>
<footer<?php echo servo_is_product_layout() ? ' class="foot"' : ''; ?>><div class="wrap">
<?php if ( has_nav_menu( 'footer_menu' ) ) : ?>
<div class="fcols">
<?php
wp_nav_menu(
	array(
		'theme_location' => 'footer_menu',
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 2,
		'fallback_cb'    => false,
		'walker'         => new Servo_Footer_Walker(),
	)
);
?>
</div>
<?php endif; ?>
<div class="fbase">
<?php if ( $servo_unvan ) : ?>
<span>
	<?php
	$servo_marka_html = '';

	if ( $servo_marka && $servo_marka_url ) {
		$servo_marka_html = ' (<a href="' . esc_url( $servo_marka_url ) . '" target="_blank" rel="noopener">' . esc_html( $servo_marka ) . '</a>)';
	} elseif ( $servo_marka ) {
		$servo_marka_html = ' (' . esc_html( $servo_marka ) . ')';
	}

	// Parçalar yukarıda escape edildi; çeviri metni esc_html ile güvenli.
	printf(
		/* translators: 1: "ServoMotion" marka adı, 2: firma unvanı ve kardeş marka bağlantısı */
		esc_html__( '%1$s, %2$s markasıdır.', 'servomotion' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<bdi lang="en">ServoMotion</bdi>',
		esc_html( $servo_unvan ) . $servo_marka_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	?>
</span>
<?php endif; ?>
<?php if ( $servo_iletisim ) : ?>
<span><?php echo implode( ' · ', $servo_iletisim ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parçalar yukarıda escape edildi. ?></span>
<?php endif; ?>
</div>
</div></footer>
