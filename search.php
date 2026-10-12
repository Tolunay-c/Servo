<?php
/**
 * Arama sonuçları şablonu.
 *
 * Tasarımda arama sayfası yok; iç sayfaların bileşenleriyle kurulur:
 * başlık (.phero), arama kutusu (.gsearchbox, sözlükteki gibi) ve sonuç
 * kartları (.reqgrid > .req, doküman talep kartları gibi).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$servo_sorgu  = get_search_query();
$servo_toplam = (int) $wp_query->found_posts;
$servo_sayfa  = max( 1, (int) get_query_var( 'paged' ) );

get_header();
?>
<main>
<section class="phero">
	<div class="wrap">
		<div>
			<p class="label"><?php esc_html_e( 'ARAMA', 'servomotion' ); ?></p>
			<h1><?php esc_html_e( 'ARAMA', 'servomotion' ); ?><br><em><?php esc_html_e( 'SONUÇLARI', 'servomotion' ); ?></em></h1>
			<p class="intro">
				<?php
				if ( '' === $servo_sorgu ) {
					esc_html_e( 'Aramak istediğiniz kelimeyi aşağıdaki kutuya yazın.', 'servomotion' );
				} elseif ( $servo_toplam ) {
					/* translators: 1: aranan kelime, 2: sonuç sayısı */
					echo esc_html( sprintf( __( '“%1$s” için %2$d sonuç bulundu.', 'servomotion' ), $servo_sorgu, $servo_toplam ) );
				} else {
					/* translators: %s: aranan kelime */
					echo esc_html( sprintf( __( '“%s” için sonuç bulunamadı.', 'servomotion' ), $servo_sorgu ) );
				}
				?>
			</p>
		</div>
	</div>
</section>
<section class="section" style="background:#fff">
	<div class="wrap">
		<form class="gsearchbox" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="s" style="display:block;font:10.5px/1.5 'IBM Plex Mono',monospace;letter-spacing:.14em;text-transform:uppercase;color:#7a8a9b;margin-bottom:8px"><?php esc_html_e( 'Sitede arayın', 'servomotion' ); ?></label>
			<input id="s" name="s" type="search" value="<?php echo esc_attr( $servo_sorgu ); ?>" placeholder="<?php esc_attr_e( 'örn. strok, reçete, montaj', 'servomotion' ); ?>">
		</form>
		<?php if ( '' !== $servo_sorgu && have_posts() ) : ?>
		<div class="reqgrid">
			<?php
			while ( have_posts() ) :
				the_post();
				$servo_ozet = servo_arama_ozet( get_post() );
				?>
			<article class="req">
				<h3><?php the_title(); ?></h3>
				<?php if ( $servo_ozet ) : ?>
				<p><?php echo esc_html( $servo_ozet ); ?></p>
				<?php endif; ?>
				<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Sayfaya gidin →', 'servomotion' ); ?></a>
			</article>
			<?php endwhile; ?>
		</div>
			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="actions" style="margin-top:40px">
				<?php if ( $servo_sayfa > 1 ) : ?>
			<a class="btn" href="<?php echo esc_url( get_previous_posts_page_link() ); ?>"><?php esc_html_e( '← ÖNCEKİ SONUÇLAR', 'servomotion' ); ?></a>
				<?php endif; ?>
				<?php if ( $servo_sayfa < $wp_query->max_num_pages ) : ?>
			<a class="btn primary" href="<?php echo esc_url( get_next_posts_page_link( $wp_query->max_num_pages ) ); ?>"><?php esc_html_e( 'SONRAKİ SONUÇLAR →', 'servomotion' ); ?></a>
				<?php endif; ?>
		</div>
			<?php endif; ?>
		<?php elseif ( '' !== $servo_sorgu ) : ?>
		<p class="appfoot">
			<?php esc_html_e( 'Farklı bir kelimeyle tekrar deneyebilir ya da sorunuzu doğrudan bize iletebilirsiniz.', 'servomotion' ); ?>
			<a href="<?php echo esc_url( servo_post_url( 'iletisim' ) ); ?>"><?php esc_html_e( 'İletişime geçin →', 'servomotion' ); ?></a>
		</p>
		<?php endif; ?>
	</div>
</section>
</main>
<?php
get_footer();
