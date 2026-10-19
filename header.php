<?php
/**
 * Site başlığı.
 *
 * <title>, description, OG ve canonical Rank Math'e bırakılır; tema basmaz.
 * Tüm sayfalarda ürün sayfalarının kutulu sekme (.tabs) header'ı basılır
 * (bilinçli tasarım sapması, CLAUDE.md mimari karar 3). Standart header
 * şablonu (header-standart.php) ve CSS'i devre dışı olarak durur.
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

get_template_part( 'template-parts/site/header', 'urun' );
