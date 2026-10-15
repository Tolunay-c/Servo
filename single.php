<?php
/**
 * Rehber yazısı (tasarım: rehber-*.html).
 *
 * Başlık bölümü (.pdoc), içindekiler + metin (.pbody), yazı altı notu ve
 * ilgili yazılar (.related). İçindekiler metindeki H2'lerden üretilir (en az
 * iki ara başlık varsa; yoksa metin tek sütundur: .pbody.solo);
 * yazı sonu kutusu ve not Site Ayarları > Rehber sekmesinden gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main>
<?php
while ( have_posts() ) :
	the_post();

	list( $servo_icerik, $servo_toc ) = servo_rehber_icerik( get_post() );

	// Tasarımda tek ara başlıklı yazıda içindekiler yok; metin tek sütun (.pbody.solo).
	if ( count( $servo_toc ) < 2 ) {
		$servo_toc = array();
	}

	$servo_rehber_id = (int) get_option( 'page_for_posts' );
	$servo_kirinti   = servo_field( 'kirinti' );
	$servo_giris     = servo_field( 'giris' );
	$servo_gorsel    = servo_rehber_gorsel( get_post() );
	$servo_ilgili    = servo_rehber_ilgili( get_post() );
	$servo_son       = array(
		'baslik' => servo_option( 'rehber_son_baslik' ),
		'metin'  => servo_option( 'rehber_son_metin' ),
		'buton'  => servo_option( 'rehber_son_buton' ),
	);
	$servo_not       = servo_option( 'rehber_not' );
	/* translators: %d: dakika */
	$servo_okuma = sprintf( __( '%d DK OKUMA', 'servomotion' ), servo_rehber_okuma( get_post() ) );
	?>
<section class="pdoc"><div class="wrap">
<p class="crumb"><a href="<?php echo esc_url( $servo_rehber_id ? get_permalink( $servo_rehber_id ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'REHBER', 'servomotion' ); ?></a><?php echo $servo_kirinti ? ' · ' . esc_html( $servo_kirinti ) : ''; ?></p>
<p class="topmeta"><span><b><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></b></span><span><?php echo esc_html( $servo_okuma ); ?></span></p>
<h1><?php echo servo_bdi( esc_html( get_post()->post_title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
	<?php if ( $servo_giris ) : ?>
<p class="intro"><?php echo servo_bdi( servo_text( $servo_giris ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
<div class="ptags"><?php echo servo_rehber_etiketler( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php if ( $servo_gorsel ) : ?>
<div class="phero"><?php echo $servo_gorsel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası veya ek görseli. ?></div>
	<?php endif; ?>
</div></section>

<section class="<?php echo $servo_toc ? 'pbody' : 'pbody solo'; ?>"><div class="wrap">
	<?php if ( $servo_toc ) : ?>
<nav class="toc" aria-label="<?php esc_attr_e( 'İçindekiler', 'servomotion' ); ?>"><div class="ttitle"><?php esc_html_e( 'İÇİNDEKİLER', 'servomotion' ); ?></div><ol>
		<?php foreach ( $servo_toc as $servo_t ) : ?>
<li><a href="#<?php echo esc_attr( $servo_t['id'] ); ?>"><?php echo servo_bdi( esc_html( $servo_t['metin'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
		<?php endforeach; ?>
</ol></nav>
	<?php endif; ?>
<div class="prose">
	<?php echo $servo_icerik; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content çıktısı. ?>
	<?php if ( $servo_son['baslik'] || $servo_son['metin'] ) : ?>
<div class="endbox">
		<?php if ( $servo_son['baslik'] ) : ?>
  <h3><?php echo esc_html( $servo_son['baslik'] ); ?></h3>
		<?php endif; ?>
		<?php if ( $servo_son['metin'] ) : ?>
  <p><?php echo servo_bdi( servo_text( $servo_son['metin'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $servo_son['buton']['url'] ) ) : ?>
  <a class="btn"<?php echo servo_link_atts( $servo_son['buton'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $servo_son['buton']['title'] ); ?></a>
		<?php endif; ?>
</div>
	<?php endif; ?>
</div></div></section>

	<?php if ( $servo_not ) : ?>
<section class="section" style="padding:0"><div class="wrap"><p style="border-left:3px solid var(--yellow);padding-left:15px;font-size:15.5px;color:#506275;line-height:1.65;margin-bottom:40px"><?php echo servo_bdi( servo_text( $servo_not ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p></div></section>
	<?php endif; ?>
	<?php if ( $servo_ilgili ) : ?>
<section class="related"><div class="wrap">
<div class="rehber-intro"><div><p class="label"><?php esc_html_e( 'DEVAMI', 'servomotion' ); ?></p><h2><?php esc_html_e( 'İLGİLİ YAZILAR', 'servomotion' ); ?></h2></div></div>
<div class="stories">
		<?php
		foreach ( $servo_ilgili as $servo_y ) {
			get_template_part( 'template-parts/rehber/kart', null, array( 'post' => $servo_y ) );
		}
		?>
</div></div></section>
	<?php endif; ?>
	<?php
endwhile;
?>
</main>
<?php
get_footer();
