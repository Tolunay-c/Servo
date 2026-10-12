<?php
/**
 * Rehber listesi (tasarım: rehber.html): başlık (.gh), arama ve filtreler
 * (.gtools), öne çıkan yazı ve kartlar (#yazilar).
 *
 * Filtre ve arama rehber-liste.js ile tarayıcıda çalışır. Kategori
 * filtreleri yazı kategorilerinden, ürün alanı filtreleri "Ürün alanları"
 * taxonomy'sinden üretilir. Öne çıkan yazı: sabitlenmiş (sticky) en yeni
 * yazı, yoksa en yeni yazı.
 *
 * Argümanlar:
 * - etiket, baslik, aciklama: başlık bölümünün metinleri.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$servo_yazilar  = $wp_query->posts;
$servo_sabitler = array_map( 'intval', (array) get_option( 'sticky_posts' ) );
$servo_one      = null;

foreach ( $servo_yazilar as $servo_i => $servo_y ) {
	if ( in_array( (int) $servo_y->ID, $servo_sabitler, true ) ) {
		$servo_one = $servo_y;
		unset( $servo_yazilar[ $servo_i ] );
		break;
	}
}
if ( ! $servo_one && $servo_yazilar ) {
	$servo_one = array_shift( $servo_yazilar );
}

$servo_kategoriler = get_terms(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
		'orderby'    => 'name',
	)
);
$servo_alanlar     = get_terms(
	array(
		'taxonomy'   => 'urun_alani',
		'hide_empty' => true,
		'orderby'    => 'name',
	)
);
$servo_toplam      = count( $wp_query->posts );
?>
<section class="gh"><div class="wrap">
<?php if ( ! empty( $args['etiket'] ) ) : ?>
<p class="label"><?php echo esc_html( $args['etiket'] ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $args['baslik'] ) ) : ?>
<h1><?php echo servo_bdi( servo_text( $args['baslik'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
<?php endif; ?>
<?php if ( ! empty( $args['aciklama'] ) ) : ?>
<p><?php echo servo_bdi( servo_text( $args['aciklama'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
</div></section>

<section class="gtools"><div class="wrap">
<div class="gsearch"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>
<input type="search" id="q" placeholder="<?php esc_attr_e( 'Yazılarda arayın', 'servomotion' ); ?>" aria-label="<?php esc_attr_e( 'Yazılarda arayın', 'servomotion' ); ?>" autocomplete="off"></div>
<div class="gfilters">
<?php
foreach (
	array(
		array( 'cat', __( 'KATEGORİ', 'servomotion' ), __( 'Kategori', 'servomotion' ), $servo_kategoriler ),
		array( 'area', __( 'ÜRÜN ALANI', 'servomotion' ), __( 'Ürün alanı', 'servomotion' ), $servo_alanlar ),
	) as $servo_f
) :
	if ( is_wp_error( $servo_f[3] ) || ! $servo_f[3] ) {
		continue;
	}
	?>
  <div class="fgroup"><div class="ftitle"><?php echo esc_html( $servo_f[1] ); ?></div><div class="frow" role="group" aria-label="<?php echo esc_attr( $servo_f[2] ); ?>">
    <button class="filter" type="button" data-<?php echo esc_attr( $servo_f[0] ); ?>="all" aria-pressed="true"><?php esc_html_e( 'Tümü', 'servomotion' ); ?></button>
	<?php foreach ( $servo_f[3] as $servo_term ) : ?>
    <button class="filter" type="button" data-<?php echo esc_attr( $servo_f[0] ); ?>="<?php echo esc_attr( $servo_term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $servo_term->name ); ?></button>
	<?php endforeach; ?>
  </div></div>
<?php endforeach; ?>
</div>
<?php /* translators: %d: yazı sayısı */ ?>
<p class="gcount" id="gcount"><?php echo wp_kses( sprintf( __( '<b>%d</b> yazı listeleniyor', 'servomotion' ), $servo_toplam ), array( 'b' => array() ) ); ?></p>
</div></section>

<section class="section" id="yazilar"><div class="wrap">
<?php
if ( $servo_one ) {
	get_template_part(
		'template-parts/rehber/kart',
		null,
		array(
			'post'      => $servo_one,
			'one_cikan' => true,
		)
	);
}
?>
<div class="stories" id="stories">
<?php
foreach ( $servo_yazilar as $servo_y ) {
	get_template_part( 'template-parts/rehber/kart', null, array( 'post' => $servo_y ) );
}
?>
</div>
<p class="empty-note" id="emptyNote"<?php echo $servo_toplam ? ' hidden' : ''; ?>><?php esc_html_e( 'Bu seçimle eşleşen yazı bulunamadı. Filtreleri sıfırlamayı deneyin.', 'servomotion' ); ?></p>
</div></section>
