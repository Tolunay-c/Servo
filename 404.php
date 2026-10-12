<?php
/**
 * 404 şablonu (tasarım: 404.html).
 *
 * Metinler Site Ayarları > 404 sayfası sekmesinden gelir; boş alanlarda
 * (veya SCF kapalıyken) tasarımdaki metin ve bağlantılar kullanılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_rehber = (int) get_option( 'page_for_posts' );

$servo_butonlar = servo_option_links(
	'hata_butonlar',
	array(
		array(
			'url'   => home_url( '/' ),
			'title' => __( 'ANA SAYFA', 'servomotion' ),
		),
		array(
			'url'   => home_url( '/#sistemler' ),
			'title' => __( 'SİSTEMLER', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'iletisim' ),
			'title' => __( 'İLETİŞİM', 'servomotion' ),
		),
	)
);

$servo_baglantilar = servo_option_links(
	'hata_baglantilar',
	array(
		array(
			'url'   => servo_post_url( 'core-precisor' ),
			'title' => __( 'Core Precisor →', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'shuttle-precisor' ),
			'title' => __( 'Shuttle Precisor →', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'referanslar' ),
			'title' => __( 'Uygulama örnekleri →', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'dokumanlar' ),
			'title' => __( 'Veri sayfaları →', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'sss' ),
			'title' => __( 'Sıkça sorulan sorular →', 'servomotion' ),
		),
		array(
			'url'   => servo_post_url( 'sozluk' ),
			'title' => __( 'Sözlük →', 'servomotion' ),
		),
		array(
			'url'   => $servo_rehber ? get_permalink( $servo_rehber ) : servo_post_url( 'rehber' ),
			'title' => __( 'Rehber →', 'servomotion' ),
		),
	)
);

get_header();
?>
<main>
<section class="phero" style="padding:110px 0 120px">
	<div class="wrap" style="grid-template-columns:minmax(0,1fr)">
		<div>
			<p class="label"><?php echo esc_html( servo_option( 'hata_etiket', __( 'HATA 404', 'servomotion' ) ) ); ?></p>
			<h1><?php echo esc_html( servo_option( 'hata_baslik', __( 'BU KONUMDA', 'servomotion' ) ) ); ?><br><em><?php echo esc_html( servo_option( 'hata_vurgu', __( 'BİR ÇİZGİ YOK', 'servomotion' ) ) ); ?></em></h1>
			<p class="intro"><?php echo servo_text( servo_option( 'hata_giris', __( 'Aradığınız sayfa taşınmış veya adresi değişmiş olabilir. Aşağıdan devam edebilirsiniz.', 'servomotion' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<div class="actions">
				<?php foreach ( $servo_butonlar as $servo_i => $servo_link ) : ?>
				<a class="<?php echo 0 === $servo_i ? 'btn primary' : 'btn'; ?>"<?php echo servo_link_atts( $servo_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $servo_link['title'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
<section class="section" style="background:#fff">
	<div class="wrap">
		<div class="section-head"><p class="label"><?php echo esc_html( servo_option( 'hata_liste_etiket', __( 'SIK KULLANILANLAR', 'servomotion' ) ) ); ?></p><h2><?php echo esc_html( servo_option( 'hata_liste_baslik', __( 'BELKİ BUNLARI ARIYORDUNUZ', 'servomotion' ) ) ); ?></h2></div>
		<div class="related-links">
			<?php foreach ( $servo_baglantilar as $servo_link ) : ?>
			<a<?php echo servo_link_atts( $servo_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $servo_link['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
</main>
<?php
get_footer();
