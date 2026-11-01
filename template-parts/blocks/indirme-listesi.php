<?php
/**
 * Blok: İndirme listesi (.dl > a).
 *
 * Her bağlantı bir Medya dosyasına (indirme) ya da bir sayfaya gider; sağdaki
 * rozet dosya türünden (PDF, XLSX) veya elle yazılan metinden gelir. "E-posta ile talep"
 * işaretli satırlarda doğrudan indirme yoktur: belgenin yanında e-posta alanı ve Gönder
 * düğmesi çıkar; talep servomotion/v1/talep ile ekibe bildirim olarak gider.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_dosyalar = (array) servo_sub( 'dosyalar', array() );
$servo_adres    = servo_form_alici() ? servo_talep_adresi() : '';
$servo_eposta   = servo_option( 'eposta', '' );

echo servo_section_open( 'section' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo '<div class="wrap">';
get_template_part( 'template-parts/blocks/_bolum-basligi' );

if ( $servo_dosyalar ) {
	// JS kapalıysa alanlar açık görünür (Talep et düğmesi gizlenir).
	echo '<noscript><style>.dl-alan[hidden]{display:block!important}.dl-ac{display:none!important}</style></noscript>';
	echo '<div class="dl">';
	foreach ( $servo_dosyalar as $servo_i => $servo_d ) {
		if ( ! empty( $servo_d['talep'] ) ) {
			$servo_fid = 'dlt-' . (int) $servo_i;
			echo '<form class="dlform" data-talep="uygulama_notu"' . ( $servo_adres ? ' data-adres="' . esc_url( $servo_adres ) . '"' : '' )
				. ' action="' . esc_url( 'mailto:' . $servo_eposta ) . '" method="post" enctype="text/plain"'
				. ' data-tamam="' . esc_attr__( 'Talebiniz alınmıştır. Destek ekibimiz en kısa sürede tarafınıza dönüş sağlayacaktır.', 'servomotion' ) . '"'
				. ' data-hata="' . esc_attr__( 'Talep gönderilemedi. Lütfen tekrar deneyin veya e-posta ile yazın.', 'servomotion' ) . '">';
			echo '<p class="dlt"><span class="dl-bas">' . esc_html( $servo_d['baslik'] ) . '</span><span>' . esc_html( '' !== (string) $servo_d['rozet'] ? $servo_d['rozet'] : 'PDF' ) . '</span></p>';
			// Karttaki tek düğme "Talep et"tir; e-posta alanı ve Gönder basılınca kartın içinde açılır (talep-kart.js, aynı anda tek kart).
			echo '<button type="button" class="dl-ac" aria-expanded="false" aria-controls="' . esc_attr( $servo_fid ) . '-alan">' . esc_html__( 'Talep et', 'servomotion' ) . '</button>';
			echo '<div class="dl-alan" id="' . esc_attr( $servo_fid ) . '-alan" hidden>';
			echo '<label for="' . esc_attr( $servo_fid ) . '">' . esc_html__( 'PDF talep etmek için lütfen e-posta adresinizi girin', 'servomotion' ) . '</label>';
			echo '<div class="dlrow"><input id="' . esc_attr( $servo_fid ) . '" type="email" name="E-posta" required autocomplete="email">';
			echo '<input type="hidden" name="Uygulama notu" value="' . esc_attr( $servo_d['baslik'] ) . '">';
			if ( $servo_adres ) {
				echo '<span aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden"><label for="' . esc_attr( $servo_fid ) . '-web">' . esc_html__( 'Web sitesi', 'servomotion' ) . '</label><input id="' . esc_attr( $servo_fid ) . '-web" name="web" type="text" tabindex="-1" autocomplete="off"></span>';
			}
			echo '<button class="formbtn" type="submit">' . esc_html__( 'Gönder', 'servomotion' ) . '</button></div></div>';
			echo '<p class="talep-sonuc" role="status" aria-live="polite" hidden></p></form>';
			continue;
		}

		$servo_id    = ! empty( $servo_d['dosya'] ) ? (int) $servo_d['dosya'] : 0;
		$servo_url   = $servo_id ? wp_get_attachment_url( $servo_id ) : '';
		$servo_rozet = (string) $servo_d['rozet'];

		if ( $servo_url ) {
			if ( '' === $servo_rozet ) {
				$servo_rozet = strtoupper( (string) pathinfo( $servo_url, PATHINFO_EXTENSION ) );
			}
			$servo_atts = ' href="' . esc_url( $servo_url ) . '" download';
		} elseif ( ! empty( $servo_d['link']['url'] ) ) {
			$servo_atts = servo_link_atts( $servo_d['link'] );
		} else {
			continue;
		}

		echo '<a' . $servo_atts . '><span class="dl-bas">' . esc_html( $servo_d['baslik'] ) . '</span>' . ( '' !== $servo_rozet ? '<span>' . esc_html( $servo_rozet ) . '</span>' : '' ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

// Tasarımda ilk not listeden 26px, sonrakiler birbirinden 22px uzakta.
$servo_ilk = true;
foreach ( (array) servo_sub( 'notlar', array() ) as $servo_n ) {
	if ( ! $servo_n['metin'] ) {
		continue;
	}
	$servo_style = 'margin-top:' . ( $servo_ilk ? '26px' : '22px' ) . ';border-left:3px solid var(--yellow);padding-left:15px;font-size:15px;color:#64758a;line-height:1.65';
	echo '<p class="catnote" style="' . esc_attr( $servo_style ) . '">' . servo_text( $servo_n['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$servo_ilk = false;
}

echo '</div></section>';
