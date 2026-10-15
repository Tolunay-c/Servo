<?php
/**
 * WP-CLI: canlı site denetimi, FAZ 2 içerik betiği (`wp servo faz2`).
 *
 * Dokümanlar ve formlar: doküman kayıtlarının erişim alanları, korumalı dizine
 * alınan veri sayfaları, talep formlu sayfalar (dokümanlar ve servis alt
 * sayfaları), kalan uygulama notu PDF'leri (M08) ve düğme metinleri (m07).
 * Faz 1 sınıfının yardımcılarını kullanır; her adım tekrar çalıştırılabilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/cli-faz1.php';

/**
 * FAZ 2 içerik adımları.
 */
class Servo_Faz2 extends Servo_Faz1 {

	/**
	 * Adımlar, çalışma sırasıyla.
	 *
	 * @var string[]
	 */
	const ADIMLAR = array(
		'koruma',
		'notlar',
		'dokumanlar',
		'sayfalar',
		'duymeler',
		'arama',
	);

	/**
	 * Korumalı dizini kurar (.htaccess ve index.php).
	 */
	protected function adim_koruma() {
		if ( $this->dry ) {
			WP_CLI::log( 'Korumalı dizin kurulacak: ' . servo_korumali_yol() );
			return;
		}

		WP_CLI::log( servo_korumali_hazirla() ? 'Korumalı dizin hazır: ' . servo_korumali_yol() : 'Korumalı dizin oluşturulamadı.' );
	}

	/**
	 * M08: kalan uygulama notu PDF'leri (İngilizce, 6 dosya) Medya'ya alınır. Türkçe olanlar FAZ 1'de alındı.
	 */
	protected function adim_notlar() {
		foreach ( array( 'film-foil', 'labels', 'nonwoven', 'packaging', 'rubber', 'textiles' ) as $ad ) {
			$this->medya( 'application-' . $ad . '.pdf' );
		}
	}

	/**
	 * Doküman kayıtları: erişim alanları; veri sayfaları ve sertifika korumalı dizine alınır; İngilizce veri sayfaları eklenir.
	 */
	protected function adim_dokumanlar() {
		$tr = array(
			'servomotion-core-precisor-veri-sayfasi' => array( 'core-veri-sayfasi', 'Core Precisor veri sayfası', 0 ),
			'servomotion-shuttle-precisor-veri-sayfasi' => array( 'shuttle-veri-sayfasi', 'Shuttle Precisor veri sayfası', 1 ),
			'servomotion-tuv-uygunluk-sertifikasi' => array( 'uygunluk-sertifikasi', 'Uygunluk sertifikası', 2, 'ortak', 'Declaration of conformity' ),
		);
		$en = array(
			'servomotion-core-precisor-datasheet.pdf'   => array( 'servomotion-core-precisor-datasheet', 'Core Precisor data sheet', 'Core Precisor datasheet (EN)', 'core-veri-sayfasi', 3 ),
			'servomotion-shuttle-precisor-datasheet.pdf' => array( 'servomotion-shuttle-precisor-datasheet', 'Shuttle Precisor data sheet', 'Shuttle Precisor datasheet (EN)', 'shuttle-veri-sayfasi', 4 ),
		);

		foreach ( $tr as $slug => $d ) {
			$var = get_posts(
				array(
					'post_type'   => 'dokuman',
					'post_status' => 'any',
					'name'        => $slug,
					'numberposts' => 1,
					'fields'      => 'ids',
				)
			);
			if ( ! $var ) {
				WP_CLI::warning( 'Doküman kaydı yok: ' . $slug );
				continue;
			}
			WP_CLI::log( 'Doküman: ' . $slug . ' → talep formuyla (korumalı).' );
			$this->dokuman_ayarla( $var[0], $d[0], $d[1], isset( $d[3] ) ? $d[3] : 'tr', $d[2], isset( $d[4] ) ? $d[4] : '' );
		}

		foreach ( $en as $dosya => $d ) {
			$var = get_posts(
				array(
					'post_type'   => 'dokuman',
					'post_status' => 'any',
					'name'        => $d[0],
					'numberposts' => 1,
					'fields'      => 'ids',
				)
			);
			$id  = $var ? $var[0] : 0;

			WP_CLI::log( ( $id ? 'Güncelle: ' : 'Oluştur: ' ) . 'doküman ' . $d[0] );

			if ( $this->dry ) {
				continue;
			}

			$ek = $this->medya( $dosya );
			if ( ! $ek ) {
				continue;
			}

			if ( ! $id ) {
				$id = wp_insert_post(
					array(
						'post_type'   => 'dokuman',
						'post_status' => 'publish',
						'post_title'  => $d[2],
						'post_name'   => $d[0],
						'menu_order'  => $d[4],
					)
				);
			}

			update_field( 'dosya', $ek, $id );
			update_field( 'kart_baslik', $d[2], $id );
			$this->dokuman_ayarla( $id, $d[3], $d[1], 'en', $d[4] );
		}
	}

	/**
	 * Bir doküman kaydını "talep formuyla" erişime alır ve dosyasını korumalı dizine taşır.
	 *
	 * @param int    $id     Kayıt.
	 * @param string $kod    Talep kodu.
	 * @param string $etiket Form kutusu yazısı.
	 * @param string $dil    tr | en.
	 * @param int    $sira   Sıra.
	 */
	protected function dokuman_ayarla( $id, $kod, $etiket, $dil, $sira, $etiket_en = '' ) {
		if ( $this->dry ) {
			return;
		}

		update_field( 'erisim', 'formla', $id );
		update_field( 'dil', $dil, $id );
		update_field( 'talep_kodu', $kod, $id );
		update_field( 'talep_etiketi', $etiket, $id );
		update_field( 'talep_etiketi_en', $etiket_en, $id );
		wp_update_post(
			array(
				'ID'         => $id,
				'menu_order' => $sira,
			)
		);

		if ( servo_dokuman_korumayi_uygula( $id ) ) {
			WP_CLI::log( '  dosya korumalı dizine taşındı: ' . get_post_meta( (int) servo_field( 'dosya', $id, 0 ), '_wp_attached_file', true ) );
		}
	}

	/**
	 * Dokümanlar sayfası ve servis sayfaları (talep formlarıyla) paketten yeniden kurulur.
	 */
	protected function adim_sayfalar() {
		$sayfa = $this->sayfa_bul( 'dokumanlar' );

		if ( $sayfa ) {
			WP_CLI::log( 'Sayfa: dokumanlar (talep formuyla).' );
			$satirlar = $this->sayfa_satirlari( 'dokumanlar.html' );
			if ( ! $this->dry ) {
				$this->satirlari_yaz( $sayfa->ID, $satirlar );
				$this->seo( $sayfa->ID, 'dokumanlar.html' );
			}
		}

		$this->adim_servis();
	}

	/**
	 * m07: veri sayfası ve sertifika düğme/bağlantı metinleri artık dosya indirmeyi değil talebi anlatır.
	 */
	protected function adim_duymeler() {
		foreach ( array( 'core-precisor', 'shuttle-precisor' ) as $slug ) {
			$sayfa = $this->sayfa_bul( $slug );
			if ( ! $sayfa ) {
				continue;
			}
			$link = get_post_meta( $sayfa->ID, 'cta_butonlar_1_link', true );
			if ( is_array( $link ) && 'VERİ SAYFASI (PDF)' === $link['title'] ) {
				$link['title'] = 'VERİ SAYFASI TALEP EDİN';
				WP_CLI::log( 'Düğme: ' . $slug . ' → "' . $link['title'] . '"' );
				if ( ! $this->dry ) {
					update_post_meta( $sayfa->ID, 'cta_butonlar_1_link', $link );
				}
			}
		}

		$ad    = 'options_guvenlik_maddeler_3_metin';
		$metin = (string) get_option( $ad, '' );
		if ( false !== strpos( $metin, 'Sertifikayı indirin' ) ) {
			WP_CLI::log( 'Ürün güvenliği: "Sertifikayı indirin" → "Sertifikayı talep edin"' );
			if ( ! $this->dry ) {
				update_option( $ad, str_replace( 'Sertifikayı indirin', 'Sertifikayı talep edin', $metin ) );
			}
		}
	}

	/**
	 * Arama dizinini yeniler.
	 */
	protected function adim_arama() {
		parent::adim_arama();
	}
}
