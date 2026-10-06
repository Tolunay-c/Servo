<?php
/**
 * Site başlığı.
 *
 * <title>, description, OG ve canonical Rank Math'e bırakılır; tema basmaz.
 * Ürün sayfalarında (core, shuttle) tasarımın .tabs header'ı, diğer
 * sayfalarda standart header.nav basılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
wp_body_open();

get_template_part( 'template-parts/site/header', servo_is_product_layout() ? 'urun' : 'standart' );
