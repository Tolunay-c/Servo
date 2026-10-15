<?php
/**
 * Dar ekranda logonun yanında görünen alan: TR/EN düğmesi ve hamburger.
 *
 * Geniş ekranda CSS ile gizlidir (nav-mobil.css). Menü paneli aynı header
 * içindeki aria-controls hedefidir; dil düğmesinin masaüstü kopyası menünün
 * içinde kalır, dar ekranda bu kopya kullanılır.
 *
 * Argümanlar: panel (string) menü panelinin id'si.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_panel = isset( $args['panel'] ) ? $args['panel'] : 'ana-menu';
?>
<div class="nav-aksiyon"><?php echo servo_dil_dugmesi_html( 'langlink-mobil' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><button type="button" class="navtoggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $servo_panel ); ?>" aria-label="<?php esc_attr_e( 'Menüyü aç', 'servomotion' ); ?>" data-ac="<?php esc_attr_e( 'Menüyü aç', 'servomotion' ); ?>" data-kapat="<?php esc_attr_e( 'Menüyü kapat', 'servomotion' ); ?>"><span class="navtoggle-cizgi" aria-hidden="true"></span></button></div>
