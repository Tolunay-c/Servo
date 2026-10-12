<?php
/**
 * Ürün sayfaları (Core Precisor, Shuttle Precisor) için alan okuma ve çıktı yardımcıları.
 *
 * İki ürünün alan grupları (group_servo_core, group_servo_shuttle) ortak
 * bölümlerde aynı alan adlarını kullanır; şablon parçaları bu fonksiyonlarla
 * okur. Metinlerdeki ürün ve marka adları servo_bdi() ile işaretlenir.
 * Simülasyon ve konfigüratörün JS'e bağlı markup'ı şablonlarda sabittir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ürün sayfası şablonlarının slug'ları.
 *
 * @return array<string,string> Şablon dosyası => ürün ('core' / 'shuttle').
 */
function servo_urun_sablonlari() {
	return array(
		'page-templates/core-precisor.php'    => 'core',
		'page-templates/shuttle-precisor.php' => 'shuttle',
	);
}

/**
 * Ürün sayfasının bölümlerini sırasıyla basar.
 *
 * Sayfa şablonu ve arama dizini (inc/arama.php) aynı listeyi kullanır.
 *
 * @param string $urun 'core' veya 'shuttle'.
 */
function servo_urun_bolumleri( $urun ) {
	$ozel = 'core' === $urun
		? array( 'uygulama', 'faydalar', 'calisma', 'konfigurasyonlar', 'secim', 'govde', 'altyapi' )
		: array( 'mimari', 'video', 'konfigurator', 'altyapi', 'foto' );

	get_template_part( 'template-parts/urun/hero', null, array( 'urun' => $urun ) );

	foreach ( $ozel as $parca ) {
		get_template_part( 'template-parts/' . $urun . '/' . $parca );
	}

	foreach ( array( 'teknik', 'guvenlik', 'gecis', 'cta' ) as $parca ) {
		get_template_part( 'template-parts/urun/' . $parca );
	}
}

/**
 * Satır içi HTML içerebilen metni basılabilir hâle getirir ve marka adlarını işaretler.
 *
 * @param string $text Metin.
 * @return string
 */
function servo_urun_metin( $text ) {
	return servo_bdi( servo_text( $text ) );
}

/**
 * Düz metni escape eder ve marka adlarını işaretler.
 *
 * @param string $text Metin.
 * @return string
 */
function servo_urun_duz( $text ) {
	return servo_bdi( esc_html( (string) $text ) );
}

/**
 * Bölüm başlığını (.sec-hd) basar.
 *
 * @param string $p Alan öneki (ör. 'uygulama' → uygulama_etiket, uygulama_baslik, uygulama_aciklama).
 */
function servo_urun_sec_hd( $p ) {
	$etiket   = servo_field( $p . '_etiket' );
	$baslik   = servo_field( $p . '_baslik' );
	$aciklama = servo_field( $p . '_aciklama' );

	if ( ! $etiket && ! $baslik && ! $aciklama ) {
		return;
	}

	echo '<div class="sec-hd"><div class="rule"></div>';
	if ( $etiket ) {
		echo '<p class="tagline">' . esc_html( $etiket ) . '</p>';
	}
	if ( $baslik ) {
		echo '<h2>' . servo_urun_metin( $baslik ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo servo_bdi( servo_paragraphs( (string) $aciklama ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}

/**
 * Kart ızgarasını (.rail-grid > .rail) basar.
 *
 * Tasarımda Core kartları <article> + <span class="n">, Shuttle kartları
 * <div> + <div class="n"> kullanır.
 *
 * @param string $p   Alan öneki ({p}_kartlar).
 * @param string $tag Kart etiketi: 'article' veya 'div'.
 */
function servo_urun_kartlar( $p, $tag = 'article' ) {
	$kartlar = (array) servo_field( $p . '_kartlar', false, array() );

	if ( ! $kartlar ) {
		return;
	}

	$n_tag = 'article' === $tag ? 'span' : 'div';

	echo '<div class="rail-grid">';
	foreach ( $kartlar as $k ) {
		echo '<' . $tag . ' class="rail">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $k['numara'] ) ) {
			echo '<' . $n_tag . ' class="n">' . esc_html( $k['numara'] ) . '</' . $n_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $k['baslik'] ) {
			echo '<h3>' . servo_urun_duz( $k['baslik'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $k['metin'] ) {
			echo '<p>' . servo_urun_metin( $k['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

/**
 * Adım satırlarını (h3.rowlab + .cycle > .cy) basar.
 *
 * @param string $p Alan öneki ({p}_satirlar).
 */
function servo_urun_dongu( $p ) {
	foreach ( (array) servo_field( $p . '_satirlar', false, array() ) as $satir ) {
		if ( $satir['baslik'] ) {
			echo '<h3 class="rowlab">' . servo_urun_duz( $satir['baslik'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( empty( $satir['adimlar'] ) ) {
			continue;
		}
		echo '<div class="cycle">';
		foreach ( $satir['adimlar'] as $a ) {
			echo '<div class="cy">';
			if ( $a['numara'] ) {
				echo '<div class="n">' . esc_html( $a['numara'] ) . '</div>';
			}
			if ( $a['baslik'] ) {
				echo '<h4>' . servo_urun_duz( $a['baslik'] ) . '</h4>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $a['metin'] ) {
				echo '<p>' . servo_urun_metin( $a['metin'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</div>';
		}
		echo '</div>';
	}
}

/**
 * Bileşen kutularını (.cgrid2 > .cbox) basar.
 *
 * @param string $p Alan öneki ({p}_kutular). Maddeler alanında her satır bir <li> olur.
 */
function servo_urun_kutular( $p ) {
	$kutular = (array) servo_field( $p . '_kutular', false, array() );

	if ( ! $kutular ) {
		return;
	}

	echo '<div class="cgrid2">';
	foreach ( $kutular as $k ) {
		echo '<div class="cbox">';
		if ( $k['ust'] ) {
			echo '<div class="r">' . servo_urun_duz( $k['ust'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $k['baslik'] ) {
			echo '<h3>' . servo_urun_duz( $k['baslik'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		$maddeler = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $k['maddeler'] ) ) );
		if ( $maddeler ) {
			echo '<ul>';
			foreach ( $maddeler as $m ) {
				echo '<li>' . servo_bdi( wp_kses_post( $m ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Teknik özellik tablolarını (.spec-grid > .spec) basar.
 */
function servo_urun_teknik_tablolar() {
	$tablolar = (array) servo_field( 'teknik_tablolar', false, array() );

	if ( ! $tablolar ) {
		return;
	}

	echo '<div class="spec-grid">';
	foreach ( $tablolar as $t ) {
		echo '<div class="spec">';
		if ( $t['baslik'] ) {
			echo '<h3>' . servo_urun_duz( $t['baslik'] ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '<table><tbody>';
		foreach ( (array) $t['satirlar'] as $s ) {
			echo '<tr><th>' . servo_urun_duz( $s['ozellik'] ) . '</th><td>' . servo_urun_metin( $s['deger'] ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</tbody></table></div>';
	}
	echo '</div>';
}
