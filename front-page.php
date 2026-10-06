<?php
/**
 * Ana sayfa şablonu.
 *
 * Bölümler template-parts/home/ altındadır; sıra _design/index.html ile aynıdır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main><?php
foreach ( array( 'hero', 'urunler', 'kazanimlar', 'kategori-farki', 'karsilastirma', 'uygulama-alanlari', 'hakkinda', 'iletisim-cta' ) as $servo_part ) {
	get_template_part( 'template-parts/home/' . $servo_part );
}
?>
</main>
<?php
get_footer();
