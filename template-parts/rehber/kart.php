<?php
/**
 * Rehber yazı kartı: öne çıkan (article.featured) veya standart (article.story).
 *
 * data-* öznitelikleri rehber-liste.js'in filtre ve aramasında kullanılır.
 *
 * Argümanlar:
 * - post: WP_Post.
 * - one_cikan: true ise büyük kart.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_post    = get_post( $args['post'] );
$servo_buyuk   = ! empty( $args['one_cikan'] );
$servo_url     = get_permalink( $servo_post );
$servo_baslik  = $servo_post->post_title;
$servo_ozet    = get_the_excerpt( $servo_post );
$servo_t       = servo_rehber_terimler( $servo_post );
$servo_dk      = servo_rehber_okuma( $servo_post );
$servo_tarih   = get_the_date( 'j F Y', $servo_post );
$servo_yazar   = servo_rehber_yazar( $servo_post );
$servo_alanlar = implode( ' ', wp_list_pluck( $servo_t['alanlar'], 'slug' ) );
/* translators: %d: dakika */
$servo_okuma = sprintf( __( '%d dk okuma', 'servomotion' ), $servo_dk );
?>
<article class="<?php echo $servo_buyuk ? 'featured' : 'story'; ?>" data-category="<?php echo esc_attr( $servo_t['kategori'] ? $servo_t['kategori']->slug : '' ); ?>" data-areas="<?php echo esc_attr( $servo_alanlar ); ?>" data-text="<?php echo esc_attr( servo_rehber_ara_metni( $servo_post ) ); ?>"><a class="<?php echo $servo_buyuk ? 'fvis' : 'svis'; ?>" href="<?php echo esc_url( $servo_url ); ?>" aria-label="<?php echo esc_attr( wp_strip_all_tags( $servo_baslik ) ); ?>"><?php echo servo_rehber_gorsel( $servo_post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tema SVG dosyası veya ek görseli. ?></a><div class="<?php echo $servo_buyuk ? 'fbody' : 'sbody'; ?>"><?php echo servo_rehber_etiketler( $servo_post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $servo_buyuk ? '<h2>' : '<h3>'; ?><a href="<?php echo esc_url( $servo_url ); ?>" style="text-decoration:none"><?php echo servo_bdi( esc_html( $servo_baslik ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php echo $servo_buyuk ? '</h2>' : '</h3>'; ?><p class="pmeta"><b><?php echo esc_html( $servo_tarih ); ?></b> · <?php echo esc_html( $servo_okuma ); ?></p><?php if ( $servo_ozet ) : ?><p class="sum"><?php echo servo_bdi( esc_html( $servo_ozet ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?><?php if ( $servo_buyuk ) : ?><a class="btn primary" href="<?php echo esc_url( $servo_url ); ?>" style="align-self:flex-start;margin-top:24px"><?php esc_html_e( 'YAZIYI OKUYUN', 'servomotion' ); ?></a><?php else : ?><a class="read" href="<?php echo esc_url( $servo_url ); ?>"><?php esc_html_e( 'Yazıyı okuyun →', 'servomotion' ); ?></a><p class="sfoot"><?php echo esc_html( servo_tr_upper( $servo_yazar ) ); ?></p><?php endif; ?></div></article>
