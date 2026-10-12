<?php
/**
 * Ürün sayfaları: üst bölüm (header.hero), Core ve Shuttle'da ortak.
 *
 * Giriş metni "tek paragraf" (.lead) veya "giriş cümlesi + madde listesi"
 * (.lead-box); sağ taraf ürün fotoğrafı (.hero-product) veya hazır şema
 * (.hero-cap ile). Tasarımda şerit Core'da <div class="wrap strip-in">,
 * Shuttle'da <div class="wrap"><div class="strip-in"> olarak yazılmıştır.
 *
 * Argümanlar:
 * - urun: 'core' veya 'shuttle'.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_urun     = isset( $args['urun'] ) ? $args['urun'] : 'core';
$servo_ust      = servo_field( 'hero_ust' );
$servo_baslik   = servo_field( 'hero_baslik' );
$servo_vurgu    = servo_field( 'hero_vurgu' );
$servo_kpi      = (array) servo_field( 'hero_kpi', false, array() );
$servo_butonlar = (array) servo_field( 'hero_butonlar', false, array() );
$servo_serit    = (array) servo_field( 'hero_serit', false, array() );
$servo_foto     = 'foto' === servo_field( 'hero_gorsel_tipi', false, 'foto' );
?>
<header class="hero" id="top">
  <div class="wrap hero-in">
    <div>
<?php if ( $servo_ust ) : ?>
      <p class="eyebrow" style="font-size:14px;letter-spacing:.16em;color:var(--yellow)"><?php echo servo_urun_duz( $servo_ust ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php endif; ?>
<?php if ( $servo_baslik || $servo_vurgu ) : ?>
      <h1><?php echo servo_urun_duz( $servo_baslik ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ( $servo_vurgu ) : ?> <em><?php echo servo_urun_duz( $servo_vurgu ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></em><?php endif; ?></h1>
<?php endif; ?>
<?php
if ( 'liste' === servo_field( 'hero_giris_tipi', false, 'paragraf' ) ) :
	$servo_liste_baslik = servo_field( 'hero_liste_baslik' );
	$servo_maddeler     = array_values( array_filter( wp_list_pluck( (array) servo_field( 'hero_maddeler', false, array() ), 'metin' ) ) );
	if ( $servo_liste_baslik || $servo_maddeler ) :
		?>
      <div class="lead-box" style="margin:18px 0 0; font-size:16.5px; color:#AEBCCB; line-height:1.65;">
		<?php if ( $servo_liste_baslik ) : ?>
        <p style="margin:0 0 12px 0; color:#FFFFFF; font-weight:600;"><?php echo servo_urun_metin( $servo_liste_baslik ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<?php if ( $servo_maddeler ) : ?>
        <ul style="margin:0; padding-left:18px; list-style-type:square;">
			<?php foreach ( $servo_maddeler as $servo_i => $servo_m ) : ?>
          <li style="margin-bottom:<?php echo count( $servo_maddeler ) - 1 === $servo_i ? '0' : '6px'; ?>;"><?php echo servo_urun_metin( $servo_m ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
			<?php endforeach; ?>
        </ul>
		<?php endif; ?>
      </div>
		<?php
	endif;
else :
	$servo_giris = servo_field( 'hero_giris' );
	if ( $servo_giris ) :
		?>
      <p class="lead"><?php echo servo_urun_metin( $servo_giris ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php
	endif;
endif;
?>
<?php if ( $servo_kpi ) : ?>
      <div class="kpis">
	<?php foreach ( $servo_kpi as $servo_k ) : ?>
        <div class="kpi"><div class="k"><?php echo servo_urun_duz( $servo_k['etiket'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><div class="v"><?php echo esc_html( $servo_k['deger'] ); ?><?php if ( $servo_k['birim'] ) : ?><small><?php echo esc_html( $servo_k['birim'] ); ?></small><?php endif; ?></div></div>
	<?php endforeach; ?>
      </div>
<?php endif; ?>
<?php if ( $servo_butonlar ) : ?>
      <div class="btns">
	<?php
	foreach ( $servo_butonlar as $servo_b ) :
		if ( empty( $servo_b['link']['url'] ) ) {
			continue;
		}
		?>
        <a class="btn <?php echo empty( $servo_b['sari'] ) ? 'btn-o' : 'btn-y'; ?>"<?php echo servo_link_atts( $servo_b['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo servo_urun_duz( $servo_b['link']['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	<?php endforeach; ?>
      </div>
<?php endif; ?>
    </div>
    <div>
<?php
if ( $servo_foto ) :
	$servo_gorsel = servo_field( 'hero_gorsel' );
	if ( $servo_gorsel ) :
		?>
      <div class="hero-fig hero-product"><?php echo wp_get_attachment_image( $servo_gorsel, 'servo-hero', false, array( 'loading' => 'eager' ) ); ?></div>
		<?php
	endif;
else :
	$servo_sema = servo_get_svg( (string) servo_field( 'hero_sema', false, '' ) );
	$servo_yazi = servo_field( 'hero_sema_yazi' );
	if ( $servo_sema ) :
		?>
      <div class="hero-fig">
		<?php echo $servo_sema; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası. ?>
      </div>
		<?php
	endif;
	if ( $servo_yazi ) :
		?>
      <p class="hero-cap"><?php echo servo_urun_duz( $servo_yazi ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php
	endif;
endif;
?>
    </div>
  </div>
<?php
if ( $servo_serit ) :
	$servo_kutular = '';
	foreach ( $servo_serit as $servo_s ) {
		$servo_kutular .= '<div class="strip-i"><b>' . servo_urun_duz( $servo_s['baslik'] ) . '</b>' . servo_urun_metin( $servo_s['metin'] ) . '</div>';
	}
	?>
  <div class="hero-strip">
	<?php if ( 'core' === $servo_urun ) : ?>
    <div class="wrap strip-in"><?php echo $servo_kutular; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<?php else : ?>
    <div class="wrap"><div class="strip-in"><?php echo $servo_kutular; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></div>
	<?php endif; ?>
  </div>
<?php endif; ?>
</header>
