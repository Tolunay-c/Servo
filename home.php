<?php
/**
 * Rehber listesi (yazılar sayfası, tasarım: rehber.html).
 *
 * Başlık metinleri Site Ayarları > Rehber sekmesinden gelir.
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
		'baslik'   => servo_option( 'rehber_baslik', get_the_title( (int) get_option( 'page_for_posts' ) ) ),
		'aciklama' => servo_option( 'rehber_aciklama' ),
	)
);
?>
</main>
<?php
get_footer();
