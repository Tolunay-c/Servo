<?php
/**
 * Yazı arşivleri (kategori, etiket, yazar, tarih).
 *
 * Tasarımda ayrı arşiv sayfası yok; rehber listesinin aynısı, arşivin
 * yazılarıyla ve arşiv başlığıyla gösterilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main>
<?php
get_template_part(
	'template-parts/rehber/liste',
	null,
	array(
		'etiket'   => servo_option( 'rehber_etiket' ),
		'baslik'   => wp_strip_all_tags( get_the_archive_title() ),
		'aciklama' => wp_strip_all_tags( get_the_archive_description() ),
	)
);
?>
</main>
<?php
get_footer();
