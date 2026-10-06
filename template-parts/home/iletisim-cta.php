<?php
/**
 * Ana sayfa: İletişim CTA (#iletisim).
 *
 * Markup _design/index.html ile aynıdır. E-posta ve telefon Site
 * Ayarları'ndan gelir; metinler 7. adımda ACF alanlarına bağlanacak.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_eposta  = servo_option( 'eposta' );
$servo_telefon = servo_option( 'telefon' );
$servo_konu    = __( 'ServoMotion Bilgi Talebi', 'servomotion' );
?>
<section class="contact" id="iletisim"><div class="wrap"><div><h2>HATTINIZA UYGUN SİSTEMİ<br>BİRLİKTE BELİRLEYELİM</h2><p>Hattınızın hizalama gereksinimlerini iletin; lazer yerleşimi, hareket kontrolü ve PLC/otomasyon entegrasyonunu sisteminize özel olarak tasarlayalım.</p></div><div class="contact-links"><a href="<?php echo esc_url( servo_post_url( 'iletisim' ) ); ?>">UYGULAMA ÖZETİ OLUŞTURUN →</a><?php if ( $servo_eposta ) : ?><a href="<?php echo esc_url( 'mailto:' . $servo_eposta . '?subject=' . rawurlencode( $servo_konu ) ); ?>"><?php echo esc_html( strtoupper( $servo_eposta ) ); ?></a><?php endif; ?><?php if ( $servo_telefon ) : ?><a href="<?php echo esc_url( servo_tel_href( $servo_telefon ) ); ?>"><?php echo esc_html( $servo_telefon ); ?></a><?php endif; ?></div></div></section>
