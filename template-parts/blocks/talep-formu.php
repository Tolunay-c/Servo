<?php
/**
 * Blok: Talep formu (section.talep-formu > .formbox > form).
 *
 * Türler: dokuman (doküman kutuları + iletişim), destek, iade, kilavuz. Alanlar
 * servo_talep_sema() ile tanımlıdır (uç nokta aynı şemayı doğrular). Form
 * sunucuya talep.js ile gönderilir; alıcı adresi tanımlı değilse (ya da JS
 * kapalıysa) tasarımdaki mailto: yedeği çalışır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_tur  = (string) servo_sub( 'tur', 'dokuman' );
$servo_sema = servo_talep_sema( $servo_tur );

if ( ! $servo_sema ) {
	return;
}

$servo_buton   = servo_sub( 'buton' ) ? servo_sub( 'buton' ) : __( 'TALEBİ GÖNDERİN', 'servomotion' );
$servo_not     = servo_sub( 'not' );
$servo_alici   = servo_form_alici();
$servo_eposta  = servo_option( 'eposta', '' );
$servo_adres   = $servo_alici ? servo_talep_adresi() : '';
$servo_chk_lbl = 'display:flex;gap:10px;align-items:flex-start;font-size:15.5px;color:#17212d';
$servo_chk_inp = 'width:17px;height:17px;flex:none;margin-top:3px;accent-color:#0b0f14';

echo servo_section_open( 'section talep-formu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

echo '<div class="formbox"><form';
echo ' action="' . esc_url( 'mailto:' . $servo_eposta ) . '" method="post" enctype="text/plain"';
echo ' data-talep="' . esc_attr( $servo_tur ) . '"';
if ( $servo_adres ) {
	echo ' data-adres="' . esc_url( $servo_adres ) . '"';
}
echo ' data-tamam="' . esc_attr__( 'Talebiniz alındı. En kısa sürede size dönüş yapacağız.', 'servomotion' ) . '"';
echo ' data-hata="' . esc_attr__( 'Talep gönderilemedi. Lütfen tekrar deneyin veya e-posta ile yazın.', 'servomotion' ) . '">';

foreach ( $servo_sema['satirlar'] as $servo_satir ) {
	$servo_kind = $servo_satir[0];

	if ( 'dokumanlar' === $servo_kind ) {
		// Asıl liste sayfanın dilindeki dokümanlar; "diğer dil" kutusu karşı dildekileri de ister.
		$servo_dil_kodu = servo_en() ? 'en' : 'tr';
		$servo_tr       = servo_talep_dokumanlari( $servo_dil_kodu );
		$servo_en       = array_filter(
			servo_talep_dokumanlari( 'en' === $servo_dil_kodu ? 'tr' : 'en' ),
			function ( $d ) use ( $servo_tr ) {
				return ! in_array( $d['kod'], wp_list_pluck( $servo_tr, 'kod' ), true ) || 'ortak' !== servo_field( 'dil', $d['id'], 'tr' );
			}
		);
		echo '<div class="fld"><label>' . esc_html__( 'İstenen dokümanlar', 'servomotion' ) . '</label><div class="chk" style="display:grid;gap:11px">';
		foreach ( $servo_tr as $servo_d ) {
			echo '<label style="' . esc_attr( $servo_chk_lbl ) . '"><input type="checkbox" name="' . esc_attr( sanitize_title( $servo_d['etiket'] ) ) . '" value="' . esc_attr( $servo_d['kod'] ) . '" data-kod="' . esc_attr( $servo_d['kod'] ) . '" style="' . esc_attr( $servo_chk_inp ) . '"> ' . servo_bdi( esc_html( $servo_d['etiket'] ) ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $servo_en ) {
			echo '<label style="' . esc_attr( $servo_chk_lbl ) . '"><input type="checkbox" name="diger-dil" value="1" data-dil="' . esc_attr( 'en' === $servo_dil_kodu ? 'tr' : 'en' ) . '" style="' . esc_attr( $servo_chk_inp ) . '"> ' . ( servo_en() ? esc_html__( 'Türkçe sürümler', 'servomotion' ) : esc_html__( 'İngilizce sürümler', 'servomotion' ) ) . '</label>';
		}
		echo '</div></div>';
		continue;
	}

	$servo_alanlar = array_slice( $servo_satir, 1 );
	echo 'cift' === $servo_kind ? '<div class="two">' : '';

	foreach ( $servo_alanlar as $servo_a ) {
		$servo_id  = esc_attr( $servo_a['id'] );
		$servo_atr = ' id="' . $servo_id . '" name="' . esc_attr( $servo_a['ad'] ) . '"';
		$servo_atr .= ! empty( $servo_a['zorunlu'] ) ? ' required' : '';
		$servo_atr .= ! empty( $servo_a['placeholder'] ) ? ' placeholder="' . esc_attr( $servo_a['placeholder'] ) . '"' : '';

		echo '<div class="fld"><label for="' . $servo_id . '">' . esc_html( $servo_a['etiket'] ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( 'select' === $servo_a['tip'] ) {
			echo '<select' . $servo_atr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			foreach ( $servo_a['secenekler'] as $servo_o ) {
				echo '' === $servo_o ? '<option value="">' . esc_html__( 'seçin', 'servomotion' ) . '</option>' : '<option>' . esc_html( $servo_o ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</select>';
		} elseif ( 'textarea' === $servo_a['tip'] ) {
			echo '<textarea' . $servo_atr . '></textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<input' . $servo_atr . ' type="' . esc_attr( $servo_a['tip'] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';
	}

	echo 'cift' === $servo_kind ? '</div>' : '';
}

// Honeypot: ekran dışı; insanlar görmez, form dolduran botlar doldurur.
if ( $servo_adres ) {
	echo '<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden"><label for="' . esc_attr( $servo_tur ) . '-web">' . esc_html__( 'Web sitesi', 'servomotion' ) . '</label><input id="' . esc_attr( $servo_tur ) . '-web" name="web" type="text" tabindex="-1" autocomplete="off"></div>';
}

echo '<button class="formbtn" type="submit">' . esc_html( $servo_buton ) . '</button>';
echo '<p class="formnote talep-kvkk">' . servo_kvkk_notu() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( $servo_not ) {
	echo '<p class="formnote">' . servo_text( $servo_not ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

echo '<p class="talep-sonuc" role="status" aria-live="polite" hidden></p>';
echo '</form></div></div></section>';
