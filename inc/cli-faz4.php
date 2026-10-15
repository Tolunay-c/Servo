<?php
/**
 * WP-CLI: canlı site denetimi, FAZ 4 betiği (`wp servo faz4`): İngilizce sürüm (Polylang).
 *
 * Kaynak: servomotion-site-tum131026/en/. Her adım tekrar çalıştırılabilir; kayıtlar
 * Türkçe karşılıklarıyla Polylang'de bağlanır (kopya oluşmaz).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/cli-faz1.php';

/**
 * FAZ 4 adımları.
 */
class Servo_Faz4 extends Servo_Faz1 {

	/**
	 * Adımlar, çalışma sırasıyla.
	 *
	 * @var string[]
	 */
	const ADIMLAR = array(
		'dil',
		'ayarlar',
		'js',
		'medya',
		'vakalar',
		'uygulamalar',
		'sss_terim',
		'sayfalar',
		'urunler',
		'rehber',
		'ayar_metinleri',
		'tani',
		'menuler',
		'arama',
	);

	/**
	 * İçerik dili.
	 *
	 * @var string
	 */
	protected $dil = 'en';

	/**
	 * Adımlar boyunca tema dili İngilizce'ye sabitlenir (Site Ayarları: options_en; kayıt aramaları: en).
	 *
	 * @param array $args       Adımlar.
	 * @param array $assoc_args Seçenekler.
	 */
	protected function baslat( $args, $assoc_args ) {
		servo_dil_zorla( 'en' );
		parent::baslat( $args, $assoc_args );
		servo_dil_zorla( '' );
	}

	/**
	 * Türkçe sayfa slug'ı => [İngilizce slug, paket dosyası (en/ içinde), İngilizce başlık].
	 *
	 * @return array<string,array>
	 */
	protected function sayfa_haritasi() {
		return array(
			'ana-sayfa'            => array( 'home', 'index.html', 'Home' ),
			'core-precisor'        => array( 'core-precisor', 'core-precisor.html', 'Core Precisor' ),
			'shuttle-precisor'     => array( 'shuttle-precisor', 'shuttle-precisor.html', 'Shuttle Precisor' ),
			'basari-hikayeleri'    => array( 'success-stories', 'success-stories.html', 'Success Stories' ),
			'servis'               => array( 'service', 'service.html', 'Service' ),
			'servis-destek-hatti'  => array( 'service-helpdesk', 'service-helpdesk.html', 'Helpdesk' ),
			'servis-iade-sevkiyat' => array( 'service-return-shipment', 'service-return-shipment.html', 'Return Shipment' ),
			'servis-kilavuzlar'    => array( 'service-manuals', 'service-manuals.html', 'Manuals' ),
			'kurumsal'             => array( 'company', 'company.html', 'Company' ),
			'rehber'               => array( 'guide', 'guide.html', 'Guide' ),
			'iletisim'             => array( 'contact', 'contact.html', 'Contact' ),
			'dokumanlar'           => array( 'documents', 'documents.html', 'Documents' ),
			'sss'                  => array( 'faq', 'faq.html', 'FAQ' ),
			'sozluk'               => array( 'glossary', 'glossary.html', 'Glossary' ),
			'kvkk'                 => array( 'privacy', 'privacy.html', 'Privacy and Cookies' ),
		);
	}

	/**
	 * Türkçe uygulama slug'ı => [İngilizce slug, İngilizce başlık].
	 *
	 * @return array<string,array>
	 */
	protected function uygulama_haritasi() {
		return array(
			'lastik'     => array( 'rubber', 'Rubber and Composite' ),
			'nonwoven'   => array( 'nonwoven', 'Hygiene Products and Nonwoven' ),
			'ambalaj'    => array( 'packaging', 'Packaging and Paper' ),
			'film-folyo' => array( 'film-foil', 'Film and Foil' ),
			'tekstil'    => array( 'textiles', 'Textiles and Technical Fabrics' ),
			'etiket'     => array( 'labels', 'Labels and Adhesive Tape' ),
		);
	}

	/**
	 * Dil bazlı paket dizini (en/).
	 *
	 * @return string
	 */
	protected function en_dizin() {
		return $this->dir . 'en/';
	}

	/**
	 * Diller, Polylang ayarları ve mevcut içeriğin Türkçe olarak işaretlenmesi.
	 */
	protected function adim_dil() {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			WP_CLI::error( 'Polylang etkin değil.' );
		}

		if ( $this->dry ) {
			WP_CLI::log( 'Diller ve Polylang ayarları yazılacak.' );
			return;
		}

		$model = $GLOBALS['polylang']->model;

		foreach ( array(
			array(
				'name'       => 'Türkçe',
				'slug'       => 'tr',
				'locale'     => 'tr_TR',
				'rtl'        => 0,
				'term_group' => 0,
				'flag'       => 'tr',
			),
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_GB',
				'rtl'        => 0,
				'term_group' => 1,
				'flag'       => 'gb',
			),
		) as $l ) {
			if ( ! $model->get_language( $l['slug'] ) ) {
				$model->add_language( $l );
				WP_CLI::log( 'Dil eklendi: ' . $l['slug'] );
			}
		}

		$model->options['default_lang']  = 'tr';
		$model->options['force_lang']    = 1;
		$model->options['hide_default']  = true;
		$model->options['rewrite']       = true;
		$model->options['redirect_lang'] = false;
		$model->options['browser']       = false;
		$model->options['media_support'] = false;
		$model->options['post_types']    = array( 'uygulama', 'vaka', 'sss', 'terim' );
		$model->options['taxonomies']    = array( 'urun_alani', 'sss_grup' );
		$model->options->save();

		foreach ( array( 'page', 'post', 'uygulama', 'vaka', 'sss', 'terim' ) as $t ) {
			$ids = get_posts(
				array(
					'post_type'   => $t,
					'post_status' => 'any',
					'numberposts' => -1,
					'fields'      => 'ids',
					'lang'        => '',
				)
			);
			$n   = 0;
			foreach ( $ids as $id ) {
				if ( ! pll_get_post_language( $id ) ) {
					pll_set_post_language( $id, 'tr' );
					++$n;
				}
			}
			if ( $n ) {
				WP_CLI::log( $t . ': ' . $n . ' kayıt Türkçe olarak işaretlendi.' );
			}
		}

		foreach ( array( 'category', 'urun_alani', 'sss_grup' ) as $tax ) {
			foreach ( (array) get_terms(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'lang'       => '',
				)
			) as $t ) {
				if ( ! pll_get_term_language( $t->term_id ) ) {
					pll_set_term_language( $t->term_id, 'tr' );
				}
			}
		}

		flush_rewrite_rules( false );
	}

	/**
	 * Site Ayarları: mevcut değerler options_tr_* adına taşınır (bir kez); İngilizce değerler paketten yazılır.
	 */
	protected function adim_ayarlar() {
		global $wpdb;

		// Taşıma: options_x → options_tr_x, _options_x → _options_tr_x (ACF alan anahtarı referansları).
		$bekleyen = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE (option_name LIKE 'options\\_%' AND option_name NOT LIKE 'options\\_tr\\_%' AND option_name NOT LIKE 'options\\_en\\_%') OR (option_name LIKE '\\_options\\_%' AND option_name NOT LIKE '\\_options\\_tr\\_%' AND option_name NOT LIKE '\\_options\\_en\\_%')" ); // phpcs:ignore WordPress.DB
		WP_CLI::log( 'Taşınacak Site Ayarları kaydı: ' . $bekleyen );

		if ( $bekleyen && ! $this->dry ) {
			$wpdb->query( "UPDATE {$wpdb->options} SET option_name = CONCAT('options_tr_', SUBSTRING(option_name, 9)) WHERE option_name LIKE 'options\\_%' AND option_name NOT LIKE 'options\\_tr\\_%' AND option_name NOT LIKE 'options\\_en\\_%'" ); // phpcs:ignore WordPress.DB
			$wpdb->query( "UPDATE {$wpdb->options} SET option_name = CONCAT('_options_tr_', SUBSTRING(option_name, 10)) WHERE option_name LIKE '\\_options\\_%' AND option_name NOT LIKE '\\_options\\_tr\\_%' AND option_name NOT LIKE '\\_options\\_en\\_%'" ); // phpcs:ignore WordPress.DB
			wp_cache_flush();
		}
	}

	/**
	 * İngilizce sayfalarda kullanılan script'ler: paketteki İngilizce inline script'ler assets/js/en/ altına
	 * (Türkçe karşılığıyla aynı adla) içerik değiştirilmeden alınır.
	 */
	protected function adim_js() {
		$tema = get_template_directory() . '/assets/js/';
		$hedef = $tema . 'en/';

		$eslesme = array(
			'core-precisor.html'    => array( 'core-precisor.html', 'core-precisor.html' ),
			'shuttle-precisor.html' => array( 'shuttle-precisor.html', 'shuttle-precisor.html' ),
			'iletisim.html'         => array( 'contact.html' ),
			'sozluk.html'           => array( 'glossary.html' ),
			'rehber.html'           => array( 'guide.html' ),
			'rehber-hassasiyet.html' => array( 'guide-accuracy.html' ),
			'index.html'            => array( 'index.html' ),
		);

		$yazilan = array();
		foreach ( $eslesme as $tr => $en ) {
			$tr_js = $this->satir_ici_scriptler( $this->dir . $tr );
			$en_js = $this->satir_ici_scriptler( $this->en_dizin() . $en[0] );

			foreach ( glob( $tema . '*.js' ) as $dosya ) {
				$ad    = basename( $dosya );
				$kendi = $this->normal( (string) file_get_contents( $dosya ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

				// Türkçe sayfadaki hangi inline script bu dosyaya karşılık geliyor?
				$eslenen = null;
				foreach ( $tr_js as $i => $kod ) {
					$n = $this->normal( $kod );
					if ( '' !== $n && ( false !== strpos( $kendi, $n ) || false !== strpos( $n, $kendi ) ) ) {
						$eslenen = $i;
						break;
					}
				}
				if ( null === $eslenen || isset( $yazilan[ $ad ] ) ) {
					continue;
				}

				// İngilizce sayfada en yakın uzunluktaki script.
				$en_kod = null;
				$fark   = PHP_INT_MAX;
				foreach ( $en_js as $kod ) {
					$d = abs( strlen( $kod ) - strlen( $tr_js[ $eslenen ] ) );
					if ( $d < $fark ) {
						$fark   = $d;
						$en_kod = $kod;
					}
				}
				if ( null !== $en_kod && $fark < max( 400, strlen( $tr_js[ $eslenen ] ) / 5 ) ) {
					$yazilan[ $ad ] = "/* Kaynak: servomotion-site-tum131026/en/{$en[0]} <script> (içerik değiştirilmeden taşındı) */\n" . $en_kod . "\n";
				}
			}
		}

		if ( ! is_dir( $hedef ) && ! $this->dry ) {
			wp_mkdir_p( $hedef );
		}

		foreach ( $yazilan as $ad => $icerik ) {
			$mevcut = file_exists( $hedef . $ad ) ? file_get_contents( $hedef . $ad ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( $mevcut !== $icerik ) {
				WP_CLI::log( 'EN script: assets/js/en/' . $ad . ( $mevcut ? ' (güncellendi)' : '' ) );
				if ( ! $this->dry ) {
					file_put_contents( $hedef . $ad, $icerik ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
				}
			}
		}
	}

	/**
	 * Bir HTML dosyasındaki src'siz, JSON-LD olmayan <script> gövdeleri.
	 *
	 * @param string $dosya Dosya.
	 * @return string[]
	 */
	protected function satir_ici_scriptler( $dosya ) {
		$html = (string) file_get_contents( $dosya ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		preg_match_all( '#<script(?![^>]*\bsrc=)(?![^>]*ld\+json)[^>]*>(.*?)</script>#s', $html, $m );

		return array_values( array_filter( array_map( 'trim', $m[1] ) ) );
	}

	/**
	 * Boşluksuz karşılaştırma metni.
	 *
	 * @param string $s Metin.
	 * @return string
	 */
	protected function normal( $s ) {
		return preg_replace( '/\s+/', '', (string) preg_replace( '#^/\*.*?\*/#s', '', trim( $s ) ) );
	}

	/* ------------------------------------------------------------------
	 * Adres eşleme, kayıt oluşturma, çeviri bağlama
	 * ---------------------------------------------------------------- */

	/**
	 * Paketteki bir bağlantıyı (en/ dizinine göreli) İngilizce sitenin adresine çevirir.
	 *
	 * "../" ile başlayanlar Türkçe sayfalara/dosyalara gider.
	 *
	 * @param string $href Bağlantı.
	 * @return string
	 */
	protected function adres( $href ) {
		$href = trim( $href );

		if ( '' === $href || '#' === $href[0] || preg_match( '#^(https?:|mailto:|tel:)#i', $href ) ) {
			return $href;
		}

		if ( 0 === strpos( $href, '../' ) ) {
			return parent::adres( substr( $href, 3 ) );
		}

		$parca = preg_split( '/(?=[?#])/', $href, 2 );
		$yol   = $parca[0];
		$ek    = isset( $parca[1] ) ? $parca[1] : '';

		if ( preg_match( '/\.(pdf|xlsx|zip)$/i', $yol ) ) {
			return parent::adres( $href );
		}

		if ( ! preg_match( '/^(.+)\.html$/', $yol, $m ) ) {
			return $href;
		}

		$ad = $m[1];

		if ( 'index' === $ad ) {
			return home_url( '/en/' ) . $ek;
		}
		if ( 0 === strpos( $ad, 'guide-' ) ) {
			return home_url( '/en/guide/' . substr( $ad, 6 ) . '/' ) . $ek;
		}
		if ( 0 === strpos( $ad, 'application-' ) ) {
			return home_url( '/en/application/' . substr( $ad, 12 ) . '/' ) . $ek;
		}

		return home_url( '/en/' . $ad . '/' ) . $ek;
	}

	/**
	 * Uygulama sayfası hero şemasının tema dosyası adı (application-<slug>.html → uygulama-<tr slug>).
	 *
	 * @param string $dosya Paket dosyası (en/ ile).
	 * @return string
	 */
	protected function hero_svg_adi( $dosya ) {
		if ( ! preg_match( '#application-([a-z-]+)\.html$#', $dosya, $m ) ) {
			return '';
		}

		foreach ( $this->uygulama_haritasi() as $tr => $en ) {
			if ( $en[0] === $m[1] ) {
				return 'uygulama-' . $tr;
			}
		}

		return '';
	}

	/**
	 * Formla istenen dokümanlar için İngilizce talep formu.
	 *
	 * @return string
	 */
	protected function talep_adresi() {
		return home_url( '/en/documents/#request' );
	}

	/**
	 * Türkçe sayfanın (veya kaydın) ID'si.
	 *
	 * @param string $slug Slug.
	 * @param string $tur  Kayıt türü.
	 * @return int
	 */
	protected function tr_kayit( $slug, $tur = 'page' ) {
		$p = servo_kayit_bul( $slug, $tur, 'tr' );

		return $p ? (int) $p->ID : 0;
	}

	/**
	 * İngilizce kaydı bulur ya da oluşturur ve Türkçe karşılığına bağlar.
	 *
	 * @param string $tur     Kayıt türü.
	 * @param string $slug    İngilizce slug.
	 * @param string $baslik  İngilizce başlık.
	 * @param int    $tr_id   Türkçe karşılık (0 ise bağlanmaz).
	 * @param string $durum   Kayıt durumu.
	 * @param array  $ek      wp_insert_post'a eklenecek alanlar.
	 * @return int
	 */
	protected function en_kayit( $tur, $slug, $baslik, $tr_id, $durum = 'publish', $ek = array() ) {
		$var = servo_kayit_bul( $slug, $tur, 'en' );
		$id  = $var ? (int) $var->ID : 0;

		if ( $this->dry ) {
			return $id;
		}

		$veri = array_merge(
			array(
				'post_type'   => $tur,
				'post_status' => $durum,
				'post_title'  => $baslik,
				'post_name'   => $slug,
			),
			$ek
		);

		if ( $id ) {
			$veri['ID'] = $id;
			wp_update_post( wp_slash( $veri ) );
		} else {
			$id = (int) wp_insert_post( wp_slash( $veri ) );
		}

		pll_set_post_language( $id, 'en' );

		// Polylang aynı slug'a iki dilde izin verir; ancak slug dil atanmadan önce benzersizleştirildiği için
		// (örn. lastik-2) dil atandıktan sonra istenen slug yeniden yazılır.
		if ( get_post_field( 'post_name', $id ) !== $slug ) {
			wp_update_post(
				array(
					'ID'        => $id,
					'post_name' => $slug,
				)
			);
		}

		if ( $tr_id ) {
			$cevirler = pll_get_post_translations( $tr_id );
			$cevirler['tr'] = $tr_id;
			$cevirler['en'] = $id;
			pll_save_post_translations( $cevirler );
		}

		return $id;
	}

	/**
	 * Medya: İngilizce kılavuz dosyaları (uygulama notları FAZ 2'de alındı).
	 */
	protected function adim_medya() {
		foreach ( array( 'servomotion-installation-notes.pdf', 'servomotion-installation-checklist.xlsx' ) as $d ) {
			$this->medya( $d );
		}
		foreach ( array( 'film-foil', 'labels', 'nonwoven', 'packaging', 'rubber', 'textiles' ) as $ad ) {
			$this->medya( 'application-' . $ad . '.pdf' );
		}
	}

	/**
	 * İngilizce vaka kayıtları (success-stories.html).
	 */
	protected function adim_vakalar() {
		$x    = $this->xp( 'en/success-stories.html' );
		$sira = 0;

		foreach ( $x->query( '//div[contains(@class,"cases")]/article[contains(@class,"case")]' ) as $a ) {
			$slug  = $a->getAttribute( 'id' );
			$tr_id = $this->tr_kayit( $slug, 'vaka' );
			$baslik = $this->metin( $x->query( './/h3', $a )->item( 0 ) );

			$bilgi = array();
			foreach ( $x->query( './/*[contains(@class,"cfg")]/div', $a ) as $d ) {
				$b     = $x->query( './b', $d )->item( 0 );
				$kucuk = $b ? $x->query( './small', $b )->item( 0 ) : null;
				$deger = '';
				foreach ( $b ? $b->childNodes : array() as $c ) {
					if ( XML_TEXT_NODE === $c->nodeType ) {
						$deger .= $c->textContent;
					}
				}
				$bilgi[] = array(
					'deger'    => trim( $deger ),
					'birim'    => $this->metin( $kucuk ),
					'aciklama' => $this->metin( $x->query( './span', $d )->item( 0 ) ),
				);
			}

			$metinler = array();
			foreach ( $x->query( './/*[contains(@class,"body")]/div', $a ) as $d ) {
				$metinler[ $this->metin( $x->query( './h4', $d )->item( 0 ) ) ] = $this->ic( $x->query( './p', $d )->item( 0 ) );
			}
			$metinler = array_values( $metinler );

			$alanlar = array(
				'etiket'    => $this->metin( $x->query( './/*[contains(@class,"tag")]', $a )->item( 0 ) ),
				'giris'     => $this->ic( $x->query( './/*[contains(@class,"lead")]', $a )->item( 0 ) ),
				'svg'       => 'uygulama-' . $slug,
				'bilgiler'  => $bilgi,
				'baslangic' => isset( $metinler[0] ) ? $metinler[0] : '',
				'kurulan'   => isset( $metinler[1] ) ? $metinler[1] : '',
			);

			$cap = $x->query( './/figcaption', $a )->item( 0 );

			WP_CLI::log( ( servo_kayit_bul( $slug, 'vaka', 'en' ) ? 'Güncelle: ' : 'Oluştur: ' ) . 'vaka (EN) ' . $slug );

			if ( $this->dry ) {
				++$sira;
				continue;
			}

			$id = $this->en_kayit( 'vaka', $slug, $baslik, $tr_id, 'publish', array( 'menu_order' => $sira ) );

			foreach ( $alanlar as $ad => $deger ) {
				update_field( $ad, $deger, $id );
			}

			// Fotoğraf Türkçe vakayla aynı Medya dosyasıdır; yalnızca alt yazı çevrilir.
			$foto = $tr_id ? (int) get_post_meta( $tr_id, 'foto', true ) : 0;
			update_field( 'foto', $foto ? $foto : '', $id );
			update_field( 'foto_aciklama', $cap ? $this->metin( $cap ) : '', $id );
			++$sira;
		}
	}

	/**
	 * İngilizce uygulama kayıtları, kartları ve sayfaları.
	 */
	protected function adim_uygulamalar() {
		// Kart metinleri: İngilizce "Applications" sayfasındaki altı kart.
		$x      = $this->xp( 'en/applications.html' );
		$kartlar = array();

		foreach ( $x->query( '//div[contains(@class,"applead")]/article' ) as $a ) {
			$link = $x->query( './a[contains(@class,"appnote")]', $a )->item( 0 );
			if ( ! $link || ! preg_match( '#^application-([a-z-]+)\.html$#', $link->getAttribute( 'href' ), $m ) ) {
				continue;
			}
			$key = $x->query( './p[contains(@class,"appkey")]', $a )->item( 0 );
			$kartlar[ $m[1] ] = array(
				'kart_baslik'      => $this->metin( $x->query( './h3', $a )->item( 0 ) ),
				'kart_metin'       => $this->ic( $x->query( './p[contains(@class,"appsym")]', $a )->item( 0 ) ),
				'kart_belirleyici' => $this->metin_em_sonrasi( $key ),
				'kart_ornek'       => $this->ic( $x->query( './p[contains(@class,"appscale")]', $a )->item( 0 ) ),
			);
		}

		foreach ( $this->uygulama_haritasi() as $tr => $en ) {
			$tr_id = $this->tr_kayit( $tr, 'uygulama' );
			if ( ! $tr_id ) {
				$tr_id = $this->uygulama_tr( $tr );
			}
			$dosya = 'en/application-' . $en[0] . '.html';

			WP_CLI::log( 'Uygulama (EN): ' . $en[0] );

			$satirlar = $this->sayfa_satirlari( $dosya );

			if ( $this->dry ) {
				continue;
			}

			$id = $this->en_kayit( 'uygulama', $en[0], $en[1], $tr_id );
			update_field( 'detay_sayfasi', 1, $id );
			update_field( 'kart_svg', 'uygulama-' . $tr, $id );
			if ( isset( $kartlar[ $en[0] ] ) ) {
				foreach ( $kartlar[ $en[0] ] as $alan => $deger ) {
					update_field( $alan, $deger, $id );
				}
			}
			$this->satirlari_yaz( $id, $satirlar );
			$this->seo( $id, $dosya );
		}
	}

	/**
	 * Türkçe uygulama kaydı (dilden bağımsız arama).
	 *
	 * @param string $tr Türkçe slug.
	 * @return int
	 */
	protected function uygulama_tr( $tr ) {
		$onceki   = $this->dil;
		$this->dil = 'tr';
		$id       = $this->uygulama_bul( $tr );
		$this->dil = $onceki;

		return $id;
	}

	/**
	 * İngilizce sayfalar: Türkçe karşılıklarıyla bağlanır; paket yapısı mevcut bloklara uyan sayfalar kurulur.
	 */
	protected function adim_sayfalar() {
		$hazir = array( 'ana-sayfa', 'servis', 'servis-destek-hatti', 'servis-iade-sevkiyat', 'servis-kilavuzlar', 'basari-hikayeleri', 'kurumsal', 'dokumanlar', 'iletisim', 'sss', 'sozluk', 'kvkk' );

		// Sadece İngilizce olan sayfa: Uygulamalar (Türkçe karşılığı yok; dil düğmesi ana sayfadaki #uygulama-alanlari'na gider).
		$ozel = 'en/applications.html';
		WP_CLI::log( 'Kur: sayfa (EN) applications (yalnızca İngilizce)' );
		if ( ! $this->dry ) {
			$id = $this->en_kayit( 'page', 'applications', 'Applications', 0 );
			$this->satirlari_yaz( $id, $this->sayfa_satirlari( $ozel ) );
			$this->seo( $id, $ozel );
		}

		foreach ( $this->sayfa_haritasi() as $tr => $b ) {
			// Rehber liste sayfasını `rehber` adımı kurar ve yayınlar; burada taslağa çekilmez.
			if ( 'rehber' === $tr ) {
				continue;
			}

			$tr_id = $this->tr_kayit( $tr );
			if ( ! $tr_id ) {
				WP_CLI::warning( 'Türkçe sayfa yok: ' . $tr );
				continue;
			}

			$kur  = in_array( $tr, $hazir, true );
			$dosya = 'en/' . $b[1];

			WP_CLI::log( ( $kur ? 'Kur: ' : 'Taslak: ' ) . 'sayfa (EN) ' . $b[0] );

			if ( $this->dry ) {
				continue;
			}

			$id = $this->en_kayit( 'page', $b[0], $b[2], $tr_id, $kur ? 'publish' : 'draft' );

			if ( $kur ) {
				$satirlar = in_array( $tr, array( 'iletisim', 'sss', 'sozluk', 'kvkk' ), true )
					? $this->kalan_satirlari( $tr, $dosya )
					: $this->sayfa_satirlari( $dosya );
				$this->satirlari_yaz( $id, $satirlar );
				$this->seo( $id, $dosya );
			}
		}
	}

	/**
	 * İletişim, SSS, sözlük ve KVKK sayfalarının blok satırları (TR'deki blok düzeniyle).
	 *
	 * @param string $tr    Türkçe sayfa slug'ı.
	 * @param string $dosya Paket dosyası (en/…).
	 * @return array
	 */
	protected function kalan_satirlari( $tr, $dosya ) {
		$x    = $this->xp( $dosya );
		$hero = $this->hero_satiri( $x, null, $dosya );

		if ( 'iletisim' === $tr ) {
			return $this->iletisim_satirlari( $x, $dosya );
		}

		$satirlar = array( $hero );

		if ( 'sss' === $tr ) {
			$not        = $x->query( '//p[contains(@class,"note")]' )->item( 0 );
			$satirlar[] = array(
				'acf_fc_layout' => 'sss_listesi',
				'baslik_stili'  => 'standart',
				'gruplar'       => false,
				'not'           => $this->ic( $not ),
				'arka_plan'     => 'beyaz',
				'capa'          => '',
			);
		} elseif ( 'sozluk' === $tr ) {
			$satirlar[] = array(
				'acf_fc_layout' => 'sozluk',
				'siralama'      => 'elle',
				'arama_etiket'  => $this->metin( $x->query( '//div[contains(@class,"gsearchbox")]/label' )->item( 0 ) ),
				'arama_ornek'   => (string) $x->query( '//input[@id="q"]/@placeholder' )->item( 0 )->nodeValue,
				'bos_mesaj'     => $this->ic( $x->query( '//p[@id="empty"]' )->item( 0 ) ),
				'arka_plan'     => 'beyaz',
				'capa'          => '',
			);
			$cta = $x->query( '//section[contains(@class,"cta-band")]' )->item( 0 );
			if ( $cta ) {
				$satirlar[] = $this->cta_satiri( $x, $cta );
			}
		} elseif ( 'kvkk' === $tr ) {
			$kok      = $x->query( '//div[contains(@class,"prose")]' )->item( 0 );
			$bolumler = array();
			$sira     = -1;
			$ust      = '';
			foreach ( $kok->childNodes as $n ) {
				if ( XML_ELEMENT_NODE !== $n->nodeType ) {
					continue;
				}
				if ( 'h2' === $n->nodeName ) {
					$bolumler[ ++$sira ] = array(
						'baslik' => $this->metin( $n ),
						'kutu'   => '',
						'metin'  => '',
					);
				} elseif ( 'p' === $n->nodeName && false !== strpos( $n->getAttribute( 'class' ), 'upd' ) ) {
					$ust = $this->metin( $n );
				} elseif ( $sira >= 0 ) {
					$bolumler[ $sira ]['metin'] .= '<p>' . $this->ic( $n ) . "</p>\n";
				}
			}
			$satirlar[] = array(
				'acf_fc_layout' => 'uzun_metin',
				'ust_not'       => $ust,
				'bolumler'      => $bolumler,
			);
		}

		return $satirlar;
	}

	/**
	 * Bölüm başlığı (.sec-hd / .section-head) alanları: {önek}_etiket, _baslik, _aciklama.
	 *
	 * @param DOMXPath $x     XPath.
	 * @param DOMNode  $kok   Bölüm.
	 * @param string   $onek  Alan öneki.
	 * @return array
	 */
	protected function urun_baslik( $x, $kok, $onek ) {
		$hd = $x->query( './/*[contains(@class,"sec-hd") or contains(@class,"section-head")]', $kok )->item( 0 );
		$p  = array();
		foreach ( $x->query( './p[not(@class)]', $hd ) as $n ) {
			$p[] = $this->ic( $n );
		}

		return array(
			$onek . '_etiket'   => $this->metin( $x->query( './p[contains(@class,"tagline") or contains(@class,"label")]', $hd )->item( 0 ) ),
			$onek . '_baslik'   => str_replace( "\n", ' ', $this->ic( $x->query( './h2', $hd )->item( 0 ) ) ),
			$onek . '_aciklama' => implode( "\n\n", $p ),
		);
	}

	/**
	 * Kart listesi: [{numara?, baslik, metin}].
	 *
	 * @param DOMXPath $x    XPath.
	 * @param DOMNode  $kok  Bölüm.
	 * @param string   $sorgu Kart sorgusu.
	 * @param bool     $no   Numara (span.n) okunsun mu.
	 * @return array
	 */
	protected function urun_kartlar( $x, $kok, $sorgu, $no ) {
		$out = array();
		foreach ( $x->query( $sorgu, $kok ) as $a ) {
			$k = array(
				'baslik' => $this->metin( $x->query( './/h3', $a )->item( 0 ) ),
				'metin'  => $this->ic( $x->query( './p[not(@class)]', $a )->item( 0 ) ),
			);
			if ( $no ) {
				$k = array( 'numara' => $this->metin( $x->query( './span[contains(@class,"n")]', $a )->item( 0 ) ) ) + $k;
			}
			$out[] = $k;
		}

		return $out;
	}

	/**
	 * Teknik özellik tabloları (.spec-grid > .spec).
	 *
	 * @param DOMXPath $x XPath.
	 * @return array
	 */
	protected function urun_teknik( $x ) {
		$out = array();
		foreach ( $x->query( '//div[contains(@class,"spec-grid")]/div[contains(@class,"spec")]' ) as $t ) {
			$satirlar = array();
			foreach ( $x->query( './/tr', $t ) as $tr ) {
				$satirlar[] = array(
					'ozellik' => $this->metin( $x->query( './th', $tr )->item( 0 ) ),
					'deger'   => $this->ic( $x->query( './td', $tr )->item( 0 ) ),
				);
			}
			$out[] = array(
				'baslik'   => $this->metin( $x->query( './h3', $t )->item( 0 ) ),
				'satirlar' => $satirlar,
			);
		}

		return $out;
	}

	/**
	 * Sistem bileşenleri kutuları (.cgrid2 > .cbox).
	 *
	 * @param DOMXPath $x XPath.
	 * @return array
	 */
	protected function urun_altyapi( $x ) {
		$out = array();
		foreach ( $x->query( '//div[contains(@class,"cgrid2")]/div[contains(@class,"cbox")]' ) as $b ) {
			$m = array();
			foreach ( $x->query( './/li', $b ) as $li ) {
				$m[] = $this->ic( $li );
			}
			$out[] = array(
				'ust'      => $this->metin( $x->query( './div[contains(@class,"r")]', $b )->item( 0 ) ),
				'baslik'   => $this->metin( $x->query( './h3', $b )->item( 0 ) ),
				'maddeler' => implode( "\n", $m ),
			);
		}

		return $out;
	}

	/**
	 * Hero: etiket, başlık, giriş, rakamlar (EN paketten); düzen TR'deki gibi kalır.
	 *
	 * @param DOMXPath $x XPath.
	 * @return array
	 */
	protected function urun_hero( $x ) {
		$h  = $this->hero_satiri( $x, null, '' );
		$kpi = array();
		foreach ( (array) $h['rakamlar'] as $r ) {
			$kpi[] = array(
				'etiket' => $r['aciklama'],
				'deger'  => $r['deger'],
				'birim'  => $r['birim'],
			);
		}

		return array(
			'hero_ust' => $h['etiket'],
			'hero_kpi' => $kpi,
		);
	}

	/**
	 * Core ve Shuttle ürün sayfaları: TR sabit şablonu ve alan yapısı; metin EN paketten, EN'de karşılığı olmayanlar çeviri.
	 */
	protected function adim_urunler() {
		$sayfa = function ( $slug ) {
			$p = servo_kayit_bul( $slug, 'page', 'en' );

			return $p ? get_permalink( $p->ID ) : '';
		};
		$servis = $sayfa( 'service' );
		$dok    = $sayfa( 'documents' );
		$cta_butonlar = array(
			array( 'link' => array( 'title' => 'INSTALLATION AND COMMISSIONING', 'url' => $servis, 'target' => '' ) ),
			array( 'link' => array( 'title' => 'REQUEST A DATA SHEET', 'url' => $dok, 'target' => '' ) ),
		);

		$urunler = array(
			'core-precisor'    => 'core',
			'shuttle-precisor' => 'shuttle',
		);

		foreach ( $urunler as $tr_slug => $urun ) {
			$tr_id = $this->tr_kayit( $tr_slug );
			$en    = servo_kayit_bul( $tr_slug, 'page', 'en' );
			if ( ! $tr_id || ! $en ) {
				WP_CLI::warning( 'Sayfa bulunamadı: ' . $tr_slug );
				continue;
			}

			$dosya = 'en/' . $tr_slug . '.html';
			$x     = $this->xp( $dosya );
			$f     = array();

			$f += $this->urun_hero( $x );
			$bol = function ( $sorgu ) use ( $x ) {
				return $x->query( $sorgu )->item( 0 );
			};

			if ( 'core' === $urun ) {
				$f += array(
					'hero_butonlar' => array(
						array( 'link' => array( 'title' => 'System Structure', 'url' => '#calisma', 'target' => '' ), 'sari' => true ),
						array( 'link' => array( 'title' => 'Specifications', 'url' => '#teknik', 'target' => '' ), 'sari' => false ),
						array( 'link' => array( 'title' => 'Request a Quote', 'url' => '#iletisim', 'target' => '' ), 'sari' => false ),
					),
					'hero_serit'    => array(
						array( 'baslik' => 'Recipe-Based Control', 'metin' => 'Automatic laser positioning to defined targets' ),
						array( 'baslik' => 'PLC Control', 'metin' => 'Coordinated control of the servo axes' ),
						array( 'baslik' => 'PROFINET · EtherNet/IP™', 'metin' => 'Communication options to suit line control' ),
						array( 'baslik' => 'Industrial Panel PC', 'metin' => 'Recipe entry and operator interface' ),
					),
				);
				$f['hero_giris'] = $this->ic( $x->query( '//section[contains(@class,"phero")]//p[contains(@class,"intro")]' )->item( 0 ) );

				$u = $bol( '//section[@id="applications"]' );
				$f += $this->urun_baslik( $x, $u, 'uygulama' );
				$f['uygulama_kartlar'] = $this->urun_kartlar( $x, $u, './/article[contains(@class,"rail")]', true );

				$b = $bol( '//section[@id="benefits"]' );
				$f += $this->urun_baslik( $x, $b, 'faydalar' );
				$f['faydalar_kartlar'] = $this->urun_kartlar( $x, $b, './/article[contains(@class,"rail")]', false );

				// Çalışma prensibi: EN'deki "working principle" bölümü (4 adım) ilk satıra; ikinci satır TR'den çeviri.
				$c = $x->query( '//section[.//*[contains(@class,"steps2")]]' )->item( 0 );
				$f['calisma_etiket']   = 'Working principle';
				$f['calisma_baslik']   = 'From recipe to automatic laser positioning';
				$f['calisma_aciklama'] = $this->ic( $x->query( './/*[contains(@class,"section-head")]/p', $c )->item( 0 ) );
				$f['calisma_satirlar'] = array(
					array(
						'baslik'  => 'Working cycle',
						'adimlar' => $this->urun_adimlar( $x, $c ),
					),
					array(
						'baslik'  => 'What This Architecture Delivers',
						'adimlar' => array(
							array( 'numara' => '', 'baslik' => 'Protected housing', 'metin' => 'The laser modules, servo axes and cabling sit inside a closed housing. Dust, fibre and moisture from the line cannot reach the moving parts; cleaning is limited to the outer surface and the laser window.' ),
							array( 'numara' => '', 'baslik' => 'Permanent centre reference', 'metin' => 'The fixed laser in the centre never moves. Symmetrical recipes are set up around this line, so the machine centre can be verified at any time without measuring.' ),
							array( 'numara' => '', 'baslik' => 'Symmetrical motion', 'metin' => 'In dual-channel and symmetrical configurations two lasers move at the same time; the same number of steps shows twice as many line positions.' ),
							array( 'numara' => '', 'baslik' => 'Compact mounting', 'metin' => 'The system is mounted over the line as a single housing. No rail along the machine, carriage park zone or extra chassis is required.' ),
							array( 'numara' => '', 'baslik' => 'Proportionate investment', 'metin' => 'On lines with few reference lines, one or two axes are enough. Cost follows the number of lines actually needed, not the width of the machine.' ),
						),
					),
				);

				$f += $this->urun_baslik( $x, $bol( '//section[@id="konfigurasyonlar"]' ), 'konfig' );

				$s = $x->query( '//section[.//*[contains(@class,"fgrid")]]' )->item( 0 );
				$f += $this->urun_baslik( $x, $s, 'secim' );
				$kutular = array();
				foreach ( $x->query( './/div[contains(@class,"fbox")]', $s ) as $k ) {
					$kutular[] = array(
						'etiket' => $this->metin( $x->query( './div[contains(@class,"t")]', $k )->item( 0 ) ),
						'deger'  => $this->metin( $x->query( './div[contains(@class,"v")]', $k )->item( 0 ) ),
						'metin'  => $this->ic( $x->query( './p', $k )->item( 0 ) ),
					);
				}
				$f['secim_kutular'] = $kutular;

				$g = $x->query( '//section[.//*[@id="hsg" or contains(@class,"hsg")]]' )->item( 0 );
				$f += $this->urun_baslik( $x, $g, 'govde' );

				$f['gecis_ust']    = 'Same Brand · Different Architecture';
				$f['gecis_baslik'] = 'Do All the Lines Need to Be Visible at the Same Time?';
				$f['gecis_metin']  = 'If the number of cutting lines exceeds six and the lines must stay on together throughout production, Shuttle Precisor — one servo shuttle positioning dozens of laser carriages — is designed for the job.';
				$f['gecis_buton']  = array( 'title' => 'Explore Shuttle Precisor', 'url' => $sayfa( 'shuttle-precisor' ), 'target' => '' );

				$f['cta_baslik'] = 'Let us define the right system for your application';
				$f['cta_metin']  = 'Send us the alignment requirements of your line; we will design an optimised laser layout, precise motion control and PLC/automation integration specifically for your system.';
				$f['cta_konu']   = 'ServoMotion Core Precisor Enquiry';
			} else {
				$f += array(
					'hero_liste_baslik' => 'The right solution for high-density industrial lines where several cutting lines must be positioned <b>at the same time</b>:',
					'hero_maddeler'     => array(
						array( 'metin' => '<b>One servo, unlimited lines:</b> No motor, drive or cable per laser; system cost and cable complexity do not grow as the number of lines increases.' ),
						array( 'metin' => '<b>Recipe changeover in seconds:</b> The shuttle picks up the locked carriages in turn and moves them to the new size; the line does not wait for minutes.' ),
						array( 'metin' => '<b>Stability after setup:</b> Each carriage locks mechanically to the rail once positioned, so the lines stay where they were aligned after the shuttle withdraws.' ),
					),
					'hero_butonlar'     => array(
						array( 'link' => array( 'title' => 'Configure Your System', 'url' => '#konfigurator', 'target' => '' ), 'sari' => true ),
						array( 'link' => array( 'title' => 'Watch the Motion', 'url' => '#video', 'target' => '' ), 'sari' => false ),
						array( 'link' => array( 'title' => 'Specifications', 'url' => '#teknik', 'target' => '' ), 'sari' => false ),
					),
					'hero_serit'        => array(
						array( 'baslik' => 'Simultaneous Visibility', 'metin' => 'All cutting lines stay active throughout operation and are projected simultaneously along the line.' ),
						array( 'baslik' => 'Locked Position', 'metin' => 'Once a carriage is seated it locks to the rail; vibration or loss of power does not shift the line.' ),
						array( 'baslik' => 'Reserve Zone', 'metin' => 'Laser carriages not in active use wait in their own park zone at the end of the rail.' ),
						array( 'baslik' => 'Single-Axis Drive Architecture', 'metin' => 'No per-laser motor drive cost, cable complexity or maintenance load.' ),
					),
				);
				$svg = $x->query( '//section[contains(@class,"phero")]//div[contains(@class,"pvis")]/svg/@aria-label' )->item( 0 );
				$f['hero_sema_yazi'] = $svg ? $svg->nodeValue : '';
				$f['hero_sema']      = 'shuttle-sema-hero-en'; // assets/img/svg/: paketteki İngilizce şema.

				// Mimari: EN'deki üç ray + rezerve alanı; kartlara ilk üçü sığar, rezerve alanı alanı olmadığı için bırakılır.
				$m = $x->query( '//section[.//*[contains(@class,"symlist")]]' )->item( 0 );
				$f['mimari_etiket']   = 'Architecture';
				$f['mimari_baslik']   = 'Three-Rail Shuttle Architecture';
				$f['mimari_aciklama'] = $this->ic( $x->query( './/*[contains(@class,"section-head")]/p', $m )->item( 0 ) );
				$kartlar = array();
				foreach ( $x->query( './/div[contains(@class,"sym")]', $m ) as $i => $sy ) {
					if ( $i > 2 ) {
						break;
					}
					$bas = $this->metin( $x->query( './b', $sy )->item( 0 ) );
					$par = preg_split( '/\s+—\s+/u', $bas, 2 );
					$kartlar[] = array(
						'numara' => strtoupper( $par[0] ),
						'baslik' => isset( $par[1] ) ? ucfirst( $par[1] ) : $par[0],
						'metin'  => $this->ic( $x->query( './p', $sy )->item( 0 ) ),
					);
				}
				$f['mimari_kartlar'] = $kartlar;
				$dongu = $x->query( '//section[.//*[contains(@class,"steps2")]]' )->item( 0 );
				$f['mimari_satirlar'] = array(
					array(
						'baslik'  => 'Setup cycle',
						'adimlar' => $this->urun_adimlar( $x, $dongu ),
					),
					array(
						'baslik'  => 'What This Architecture Delivers',
						'adimlar' => array(
							array( 'numara' => '', 'baslik' => 'Fewer Parts', 'metin' => 'No servo motor, drive or cable per laser. As the system grows only the number of carriages increases, not the parts list.' ),
							array( 'numara' => '', 'baslik' => 'Simple Maintenance', 'metin' => 'Periodic maintenance is concentrated on the single servo axis. The carriages have no moving electrical connections.' ),
							array( 'numara' => '', 'baslik' => 'Easy Access', 'metin' => 'Carriages are added, removed and lasers replaced from the rail; the housing does not have to be removed from the line.' ),
							array( 'numara' => '', 'baslik' => 'Scalable Investment', 'metin' => 'When the number of cutting lines grows, the drive and control infrastructure stays the same; only carriages are added.' ),
							array( 'numara' => '', 'baslik' => 'Low Operating Cost', 'metin' => 'Few moving components mean few spare parts and short downtime.' ),
						),
					),
				);

				$v = $bol( '//section[@id="video"]' );
				$f += $this->urun_baslik( $x, $v, 'video' );
				$vc = $x->query( './/p[contains(@class,"vcap")]', $v )->item( 0 );
				$f['video_alt_yazi'] = $vc ? htmlspecialchars_decode( $this->ic( $vc ) ) : '';
				if ( ! $this->dry ) {
					$mp4 = $this->medya( 'video/servomotion-shuttle-precisor-en.mp4' );
					$kap = $this->medya( 'video/servomotion-shuttle-poster-en.jpg' );
					if ( $mp4 ) {
						$f['video_dosya'] = $mp4;
					}
					if ( $kap ) {
						$f['video_kapak'] = $kap;
					}
				}

				$f += $this->urun_baslik( $x, $bol( '//section[@id="konfigurator"]' ), 'konfig' );
				$f['foto_aciklama'] = 'Field photo · rail and carriage layout';

				$f['gecis_ust']    = 'Same Brand · Different Architecture';
				$f['gecis_baslik'] = 'Is Sequential Positioning of Your Cutting Lines Enough?';
				$f['gecis_metin']  = 'For lines that do not need several simultaneous projections, Core Precisor provides the same automation strength and accuracy in a leaner structure with an optimised budget.';
				$f['gecis_buton']  = array( 'title' => 'Explore Core Precisor', 'url' => $sayfa( 'core-precisor' ), 'target' => '' );

				$cta = $x->query( '//section[contains(@class,"cta-band")]' )->item( 0 );
				$f['cta_baslik'] = $this->ic( $x->query( './/h2', $cta )->item( 0 ) );
				$f['cta_metin']  = $this->ic( $x->query( './/h2/following-sibling::p', $cta )->item( 0 ) );
				$f['cta_konu']   = 'ServoMotion Shuttle Precisor Enquiry';
			}

			$f += $this->urun_baslik( $x, $bol( '//section[@id="components"]' ), 'altyapi' );
			$f['altyapi_kutular'] = $this->urun_altyapi( $x );
			$f['teknik_etiket']   = 'Specifications';
			$f['teknik_baslik']   = 'Technical data';
			$f['teknik_tablolar'] = $this->urun_teknik( $x );
			$f['cta_butonlar']    = $cta_butonlar;

			WP_CLI::log( 'Kur: ürün sayfası (EN) ' . $tr_slug . ' (' . count( $f ) . ' alan)' );
			if ( $this->dry ) {
				continue;
			}

			$id = $this->en_kayit( 'page', $tr_slug, 'core' === $urun ? 'Core Precisor' : 'Shuttle Precisor', $tr_id, 'publish' );
			update_post_meta( $id, '_wp_page_template', 'page-templates/' . $tr_slug . '.php' );

			// TR'deki tüm alanlar (görsel, simge, anahtar vb.) önce kopyalanır, sonra EN metinleri üzerine yazılır.
			foreach ( (array) get_fields( $tr_id ) as $ad => $deger ) {
				if ( ! array_key_exists( $ad, $f ) ) {
					update_field( $ad, $deger, $id );
				}
			}
			foreach ( $f as $ad => $deger ) {
				update_field( $ad, $deger, $id );
			}
			$this->seo( $id, $dosya );
		}
	}

	/**
	 * Adım dizisi (.steps2 > article) → [{numara, baslik, metin}].
	 *
	 * @param DOMXPath $x   XPath.
	 * @param DOMNode  $kok Bölüm.
	 * @return array
	 */
	protected function urun_adimlar( $x, $kok ) {
		$out = array();
		foreach ( $x->query( './/*[contains(@class,"steps2")]/article', $kok ) as $a ) {
			$out[] = array(
				'numara' => $this->metin( $x->query( './p[contains(@class,"n")]', $a )->item( 0 ) ),
				'baslik' => ucfirst( strtolower( $this->metin( $x->query( './/h3', $a )->item( 0 ) ) ) ),
				'metin'  => $this->ic( $x->query( './/h3/following-sibling::p', $a )->item( 0 ) ),
			);
		}

		return $out;
	}

	/**
	 * İletişim sayfası: hero (.ch), uygulama özeti, akış kartları, künye.
	 *
	 * @param DOMXPath $x     XPath.
	 * @param string   $dosya Paket dosyası.
	 * @return array
	 */
	protected function iletisim_satirlari( $x, $dosya ) {
		$ch  = $x->query( '//section[contains(@class,"ch")]' )->item( 0 );
		$sol = $x->query( './div[contains(@class,"wrap")]/div[1]', $ch )->item( 0 );
		$h1  = $x->query( './/h1', $sol )->item( 0 );
		$em  = $x->query( './em', $h1 )->item( 0 );
		$ana = '';
		foreach ( $h1->childNodes as $c ) {
			if ( XML_TEXT_NODE === $c->nodeType ) {
				$ana .= $c->textContent;
			}
		}

		$butonlar = $this->butonlar( $x, $sol );
		foreach ( $butonlar as &$b ) {
			if ( '#summary' === $b['link']['url'] ) {
				$b['link']['url'] = '#ozet'; // Blok kimliği sabit (stile ve betiğe bağlı).
			}
		}
		unset( $b );

		$kanal = array();
		foreach ( $x->query( './/*[contains(@class,"chan")]/a/span[2]/span[contains(@class,"k")]', $ch ) as $k ) {
			$kanal[] = $this->metin( $k );
		}

		$serit = array();
		foreach ( $x->query( './/*[contains(@class,"chstrip")]//div[b]', $ch ) as $d ) {
			$baslik = $this->metin( $x->query( './b', $d )->item( 0 ) );
			$metin  = '';
			foreach ( $d->childNodes as $c ) {
				if ( 'b' !== $c->nodeName ) {
					$metin .= $d->ownerDocument->saveHTML( $c );
				}
			}
			$serit[] = array(
				'baslik' => $baslik,
				'metin'  => trim( preg_replace( '/\s+/u', ' ', $metin ) ),
			);
		}

		$hero = array(
			'acf_fc_layout'  => 'sayfa_hero',
			'duzen'          => 'iletisim',
			'etiket'         => $this->metin( $x->query( './p[contains(@class,"label")]', $sol )->item( 0 ) ),
			'baslik'         => trim( preg_replace( '/\s+/u', ' ', $ana ) ),
			'vurgu'          => $this->metin( $em ),
			'giris'          => $this->ic( $x->query( './p[contains(@class,"intro")]', $sol )->item( 0 ) ),
			'butonlar'       => $butonlar,
			'yan_icerik'     => 'yok',
			'kanal_telefon'  => isset( $kanal[0] ) ? $kanal[0] : '',
			'kanal_whatsapp' => isset( $kanal[1] ) ? $kanal[1] : '',
			'kanal_eposta'   => isset( $kanal[2] ) ? $kanal[2] : '',
			'serit'          => $serit,
		);

		$baslik = function ( $sorgu ) use ( $x ) {
			$hd = $x->query( $sorgu )->item( 0 );

			return array(
				'baslik_stili' => 'standart',
				'etiket'       => $this->metin( $x->query( './p[contains(@class,"label")]', $hd )->item( 0 ) ),
				'baslik'       => $this->ic( $x->query( './h2', $hd )->item( 0 ) ),
				'aciklama'     => $this->ic( $x->query( './p[not(@class)]', $hd )->item( 0 ) ),
			);
		};

		$ogeler = array();
		foreach ( $x->query( '//article[contains(@class,"fstep")]' ) as $a ) {
			$ogeler[] = array(
				'numara'     => $this->metin( $x->query( './p[contains(@class,"n")]', $a )->item( 0 ) ),
				'alt_etiket' => '',
				'baslik'     => $this->metin( $x->query( './h3', $a )->item( 0 ) ),
				'metin'      => $this->ic( $x->query( './p[not(@class)]', $a )->item( 0 ) ),
				'sure'       => $this->metin( $x->query( './span[contains(@class,"t")]', $a )->item( 0 ) ),
			);
		}

		return array(
			$hero,
			$baslik( '//section[contains(@class,"brief")]//*[contains(@class,"section-head")]' ) + array(
				'acf_fc_layout' => 'uygulama_ozeti',
				'arka_plan'     => 'varsayilan',
				'capa'          => 'ozet',
			),
			$baslik( '//section[not(@class="section brief") and not(contains(@class,"info"))][.//div[contains(@class,"flow")]]//*[contains(@class,"section-head")]' ) + array(
				'acf_fc_layout' => 'numarali_kartlar',
				'gorunum'       => 'akis',
				'ogeler'        => $ogeler,
			),
			$baslik( '//section[contains(@class,"info")]//*[contains(@class,"section-head")]' ) + array(
				'acf_fc_layout' => 'iletisim_bilgileri',
				'gorunum'       => 'dort_kutu',
				'ikinci_baslik' => '',
				'arka_plan'     => 'varsayilan',
				'capa'          => 'kunye',
			),
		);
	}

	/**
	 * İngilizce SSS (soru + grup) ve sözlük (terim) kayıtları; Türkçe karşılıklarıyla sırayla eşleşir.
	 */
	protected function adim_sss_terim() {
		// SSS: gruplar paketteki sırayla Türkçe gruplarla (sira) eşleşir.
		$x       = $this->xp( 'en/faq.html' );
		$tr_gr   = get_terms(
			array(
				'taxonomy'   => 'sss_grup',
				'hide_empty' => false,
				'lang'       => 'tr',
			)
		);
		usort(
			$tr_gr,
			function ( $a, $b ) {
				return (int) get_term_meta( $a->term_id, 'sira', true ) - (int) get_term_meta( $b->term_id, 'sira', true );
			}
		);

		$g = 0;
		foreach ( $x->query( '//h2[contains(@class,"qgroup")]' ) as $h ) {
			++$g;
			$tr_term = isset( $tr_gr[ $g - 1 ] ) ? $tr_gr[ $g - 1 ] : null;
			if ( ! $tr_term ) {
				WP_CLI::warning( 'Türkçe SSS grubu yok: ' . $g );
				continue;
			}

			$ad      = $this->metin( $h );
			$term_id = 0;
			if ( ! $this->dry ) {
				$term_id = $this->terim_en( 'sss_grup', $tr_term->slug, $ad );
				update_term_meta( $term_id, 'sira', (int) get_term_meta( $tr_term->term_id, 'sira', true ) );
			}

			$n = 0;
			for ( $e = $h->nextSibling; $e; $e = $e->nextSibling ) {
				if ( XML_ELEMENT_NODE !== $e->nodeType ) {
					continue;
				}
				if ( 'details' !== $e->nodeName ) {
					break;
				}
				++$n;
				$slug   = 'sss-' . $g . '-' . $n;
				$soru   = $this->metin( $x->query( './summary', $e )->item( 0 ) );
				$yanit  = '';
				foreach ( $x->query( './/*[contains(@class,"qa-a")]/p', $e ) as $p ) {
					$yanit .= '<p>' . $this->ic( $p ) . "</p>\n";
				}

				WP_CLI::log( 'SSS (EN) ' . $slug . ': ' . $soru );
				if ( $this->dry ) {
					continue;
				}

				$id = $this->en_kayit(
					'sss',
					$slug,
					$soru,
					$this->tr_kayit( $slug, 'sss' ),
					'publish',
					array(
						'post_content' => $yanit,
						'menu_order'   => $n - 1,
					)
				);
				wp_set_object_terms( $id, array( $term_id ), 'sss_grup' );
			}
		}

		// Sözlük: terimler paketteki sırayla Türkçe terimlerle eşleşir (menu_order).
		$x     = $this->xp( 'en/glossary.html' );
		$tr_tr = get_posts(
			array(
				'post_type'        => 'terim',
				'lang'             => 'tr',
				'numberposts'      => -1,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

		$i = 0;
		foreach ( $x->query( '//article[contains(@class,"term")]' ) as $a ) {
			$tr_p = isset( $tr_tr[ $i ] ) ? $tr_tr[ $i ] : null;
			if ( ! $tr_p ) {
				WP_CLI::warning( 'Türkçe terim yok: ' . $i );
				++$i;
				continue;
			}

			$baslik = $this->metin( $x->query( './h3', $a )->item( 0 ) );
			$metin  = array();
			foreach ( $x->query( './p[not(@class)]', $a ) as $p ) {
				$metin[] = $this->ic( $p );
			}
			$alt = $this->metin( $x->query( './p[contains(@class,"alt")]', $a )->item( 0 ) );

			WP_CLI::log( 'Terim (EN) ' . $tr_p->post_name . ': ' . $baslik );
			if ( ! $this->dry ) {
				$id = $this->en_kayit(
					'terim',
					$tr_p->post_name,
					$baslik,
					(int) $tr_p->ID,
					'publish',
					array(
						'post_content' => implode( "\n\n", $metin ),
						'menu_order'   => $i,
					)
				);
				update_field( 'alternatif', $alt, $id );
			}
			++$i;
		}
	}

	/**
	 * İngilizce rehber: liste sayfası, 11 yazı (Gutenberg), kategori ve ürün alanı terimleri.
	 */
	protected function adim_rehber() {
		$x      = $this->xp( 'en/guide.html' );
		$cevir  = new Servo_CLI_Icerik( array( $this, 'adres_disari' ) );
		$sira   = 0;

		// Liste sayfası: yazılar sayfası (page_for_posts) çevirisi.
		$tr_liste = $this->tr_kayit( 'rehber' );
		$en_liste = $this->en_kayit( 'page', 'guide', 'Guide', $tr_liste );
		WP_CLI::log( 'Rehber listesi (EN): #' . $en_liste );

		foreach ( $x->query( '//*[@id="yazilar"]//article[contains(@class,"featured") or contains(@class,"story")]' ) as $a ) {
			$link = $x->query( './/h2/a | .//h3/a', $a )->item( 0 );
			if ( ! $link ) {
				continue;
			}
			$dosya = basename( $link->getAttribute( 'href' ) );
			$slug  = preg_replace( '/^guide-|\.html$/', '', $dosya );
			$px    = $this->xp( 'en/' . $dosya );

			// Türkçe karşılık: hreflang="tr" bağlantısından.
			$tr_slug = '';
			$alt     = $px->query( '//link[@rel="alternate"][@hreflang="tr"]' )->item( 0 );
			if ( $alt && preg_match( '#rehber-([a-z0-9-]+)\.html#', $alt->getAttribute( 'href' ), $m ) ) {
				$tr_slug = $m[1];
			}
			$tr_yazi = $tr_slug ? $this->tr_kayit( $tr_slug, 'post' ) : 0;

			$kategori = array( $a->getAttribute( 'data-category' ), $this->metin( $x->query( './/span[contains(@class,"ctag")]', $a )->item( 0 ) ) );
			$alan_adlari = array();
			foreach ( $x->query( './/span[contains(@class,"atag")]', $a ) as $sp ) {
				$alan_adlari[] = $this->metin( $sp );
			}
			$alanlar = array_combine( preg_split( '/\s+/', trim( $a->getAttribute( 'data-areas' ) ) ), $alan_adlari );

			$baslik = $this->metin( $link );
			$ozet   = $this->metin( $x->query( './/p[contains(@class,"sum")]', $a )->item( 0 ) );
			$crumb  = $this->metin( $px->query( '//p[contains(@class,"crumb")]' )->item( 0 ) );
			$kirinti = trim( (string) preg_replace( '/^[^·]*·/u', '', $crumb ) );
			$okuma  = (int) $this->metin( $px->query( '//p[contains(@class,"topmeta")]/span' )->item( 1 ) );
			$giris  = $this->metin( $px->query( '//section[contains(@class,"pdoc")]//p[contains(@class,"intro")]' )->item( 0 ) );
			$prose  = $px->query( '//div[contains(@class,"prose")]' )->item( 0 );

			// Editoryal kutu ayrıca Gutenberg grubuna çevrilir; ham HTML olarak tekrarlanmasın.
			$kutu    = $px->query( './div[contains(@class,"editorial-note")]', $prose )->item( 0 );
			$kutu_blok = '';
			if ( $kutu ) {
				$kutu_blok = $this->editoryal_blogu( $px, $kutu );
				$prose->removeChild( $kutu );
			}
			$icerik = $cevir->donustur( $prose );
			if ( $kutu_blok ) {
				$icerik = $kutu_blok . "\n\n" . $icerik;
			}

			WP_CLI::log( 'Yazı (EN): ' . $slug . ' ← TR ' . $tr_slug );

			if ( $this->dry ) {
				++$sira;
				continue;
			}

			$tarih = $tr_yazi ? get_post_field( 'post_date', $tr_yazi ) : '2026-09-23 12:00:00';
			$id    = $this->en_kayit(
				'post',
				$slug,
				$baslik,
				$tr_yazi,
				'publish',
				array(
					'post_excerpt'  => $ozet,
					'post_content'  => $icerik,
					'post_date'     => $tarih,
					'post_date_gmt' => get_gmt_from_date( $tarih ),
				)
			);

			// Terimler: TR terimle aynı slug (ek -en), Polylang'de çeviri olarak bağlı.
			wp_set_post_categories( $id, array( $this->terim_en( 'category', $kategori[0], $kategori[1] ) ) );
			$terimler = array();
			foreach ( $alanlar as $ts => $tad ) {
				$terimler[] = $this->terim_en( 'urun_alani', $ts, $tad );
			}
			wp_set_object_terms( $id, $terimler, 'urun_alani' );

			update_field( 'field_servo_rehber_kirinti', $kirinti, $id );
			update_field( 'field_servo_rehber_giris', $giris, $id );
			update_field( 'field_servo_rehber_okuma_suresi', $okuma ? $okuma : '', $id );
			update_field( 'field_servo_rehber_kart_svg', $tr_slug && file_exists( get_template_directory() . '/assets/img/svg/rehber-' . $tr_slug . '.svg' ) ? 'rehber-' . $tr_slug : '', $id );
			update_post_meta( $id, '_servo_kaynak', 'en/' . $dosya );

			$this->seo( $id, 'en/' . $dosya );
			++$sira;
		}

		// İlgili yazılar alanı, Türkçe yazıdaki seçimin İngilizce karşılıklarıyla doldurulur.
		if ( ! $this->dry ) {
			foreach ( $this->en_yazilar() as $en_id ) {
				$tr = pll_get_post( $en_id, 'tr' );
				if ( ! $tr ) {
					continue;
				}
				$ilgili = get_field( 'ilgili', $tr, false );
				if ( is_array( $ilgili ) && $ilgili ) {
					$yeni = array();
					foreach ( $ilgili as $i ) {
						$e = pll_get_post( (int) $i, 'en' );
						if ( $e ) {
							$yeni[] = $e;
						}
					}
					update_field( 'ilgili', $yeni, $en_id );
				}
			}
		}
	}

	/**
	 * İngilizce yazıların ID'leri.
	 *
	 * @return int[]
	 */
	protected function en_yazilar() {
		$out = array();
		foreach ( get_posts(
			array(
				'post_type'   => 'post',
				'post_status' => 'any',
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		) as $id ) {
			if ( 'en' === pll_get_post_language( $id ) ) {
				$out[] = $id;
			}
		}

		return $out;
	}

	/**
	 * İngilizce terim: Türkçe terimle aynı kısa ad (slug'a -en eklenir) ve Polylang çeviri bağı.
	 *
	 * @param string $tax Taksonomi.
	 * @param string $slug Türkçe terimin slug'ı (paketteki data-* değeri).
	 * @param string $ad   İngilizce ad.
	 * @return int
	 */
	protected function terim_en( $tax, $slug, $ad ) {
		$en_slug = $slug . '-en';
		$t       = get_term_by( 'slug', $en_slug, $tax );
		$id      = $t ? (int) $t->term_id : 0;

		if ( ! $id ) {
			$r  = wp_insert_term( $ad, $tax, array( 'slug' => $en_slug ) );
			$id = is_wp_error( $r ) ? 0 : (int) $r['term_id'];
		}

		if ( $id ) {
			pll_set_term_language( $id, 'en' );
			$tr = get_term_by( 'slug', $slug, $tax );
			if ( $tr && 'tr' === pll_get_term_language( $tr->term_id ) ) {
				$cevirler       = pll_get_term_translations( $tr->term_id );
				$cevirler['tr'] = (int) $tr->term_id;
				$cevirler['en'] = $id;
				pll_save_term_translations( $cevirler );
			}
		}

		return $id;
	}

	/**
	 * Ana menü ve footer menüsünün İngilizce karşılıkları (Polylang menü eşlemesi dahil).
	 *
	 * Öğeler Türkçe menüden türetilir: sayfa ve uygulama öğeleri çevirileriyle, özel başlıklar paketin metinleriyle.
	 * Tekrar çalıştırılınca menü sıfırdan kurulur (kopya oluşmaz).
	 */
	protected function adim_menuler() {
		$tr_ana = wp_get_nav_menu_object( 'ana-menu' );
		$tr_alt = wp_get_nav_menu_object( 'footer-menusu' );

		if ( ! $tr_ana || ! $tr_alt || ! function_exists( 'pll_get_post' ) ) {
			WP_CLI::warning( 'Türkçe menüler ya da Polylang bulunamadı.' );
			return;
		}

		$basliklar = array(
			// Ana menü.
			'ANA SAYFA'                    => 'HOME',
			'CORE PRECISOR'                => 'CORE PRECISOR',
			'SHUTTLE PRECISOR'             => 'SHUTTLE PRECISOR',
			'BAŞARI HİKAYELERİ'            => 'SUCCESS STORIES',
			'SERVİS'                       => 'SERVICE',
			'KURUMSAL'                     => 'COMPANY',
			'REHBER'                       => 'GUIDE',
			'İLETİŞİM'                     => 'CONTACT',
			// Footer.
			'SİSTEMLER'                    => 'SYSTEMS',
			'Core Precisor'                => 'Core Precisor',
			'Shuttle Precisor'             => 'Shuttle Precisor',
			'Teknik dokümanlar'            => 'Technical documents',
			'UYGULAMA ALANLARI'            => 'APPLICATIONS',
			'Lastik ve kompozit'           => 'Rubber and composite',
			'Hijyenik ürünler ve nonwoven' => 'Hygiene products and nonwoven',
			'Ambalaj ve kağıt'             => 'Packaging and paper',
			'Film ve folyo'                => 'Film and foil',
			'Tekstil ve teknik kumaşlar'   => 'Textiles and technical fabrics',
			'Etiket ve yapışkanlı bant'    => 'Labels and adhesive tape',
			'Sahadan altı uygulama'        => 'Proven applications across six industries',
			'Servis ve devreye alma'       => 'Service and commissioning',
			'Destek hattı'                 => 'Helpdesk',
			'Kılavuzlar'                   => 'Manuals',
			'Rehber yazıları'              => 'Guide articles',
			'Hakkımızda'                   => 'About us',
			'İletişim'                     => 'Contact',
			'Sıkça sorulan sorular'        => 'Frequently asked questions',
			'Sözlük'                       => 'Glossary',
			'KVKK ve çerezler'             => 'Privacy and cookies',
		);

		$kur = function ( $ad, $tr_menu ) use ( $basliklar ) {
			$menu = wp_get_nav_menu_object( $ad );
			if ( $menu ) {
				foreach ( wp_get_nav_menu_items( $menu->term_id, array( 'post_status' => 'any' ) ) as $it ) {
					wp_delete_post( $it->ID, true );
				}
				$menu_id = (int) $menu->term_id;
			} else {
				$menu_id = (int) wp_create_nav_menu( $ad );
			}

			$harita = array(); // TR öğe kimliği → EN öğe kimliği.
			$sira   = 1;

			foreach ( wp_get_nav_menu_items( $tr_menu->term_id ) as $it ) {
				$baslik = isset( $basliklar[ $it->title ] ) ? $basliklar[ $it->title ] : $it->title;
				$args   = array(
					'menu-item-title'     => $baslik,
					'menu-item-position'  => $sira++,
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $it->menu_item_parent && isset( $harita[ $it->menu_item_parent ] ) ? $harita[ $it->menu_item_parent ] : 0,
				);

				if ( 'post_type' === $it->type ) {
					$en = pll_get_post( (int) $it->object_id, 'en' );
					if ( ! $en ) {
						WP_CLI::warning( 'EN karşılığı yok, atlandı: ' . $it->title );
						continue;
					}
					$args['menu-item-type']      = 'post_type';
					$args['menu-item-object']    = $it->object;
					$args['menu-item-object-id'] = $en;
				} else {
					$args['menu-item-type'] = 'custom';
					$args['menu-item-url']  = '#';
				}

				$harita[ $it->ID ] = (int) wp_update_nav_menu_item( $menu_id, 0, $args );
			}

			return $menu_id;
		};

		WP_CLI::log( 'Kur: menüler (EN)' );
		if ( $this->dry ) {
			return;
		}

		$ana_id = $kur( 'Main menu EN', $tr_ana );
		$alt_id = $kur( 'Footer menu EN', $tr_alt );

		// Polylang menü eşlemesi: tema konumu başına dil → menü.
		$secenek = get_option( 'polylang' );
		$tema    = get_option( 'stylesheet' );
		$konum   = get_nav_menu_locations();
		$tr_loc  = array( 'ana_menu' => $tr_ana->term_id, 'footer_menu' => $tr_alt->term_id );

		$secenek['nav_menus'][ $tema ]['ana_menu']['tr']     = $tr_loc['ana_menu'];
		$secenek['nav_menus'][ $tema ]['ana_menu']['en']     = $ana_id;
		$secenek['nav_menus'][ $tema ]['footer_menu']['tr']  = $tr_loc['footer_menu'];
		$secenek['nav_menus'][ $tema ]['footer_menu']['en']  = $alt_id;
		update_option( 'polylang', $secenek );

		WP_CLI::log( 'Polylang menü eşlemesi yazıldı.' );
		unset( $konum );
	}

	/**
	 * Site Ayarları (EN): bülten, rehber ve iletişim etiketleri paketin İngilizce sayfalarından yazılır.
	 *
	 * Boş bırakılanlar Türkçe değeri kullanır (telefon, e-posta, adres, vergi bilgisi, form alıcısı).
	 */
	protected function adim_ayar_metinleri() {
		$id = servo_options_id( 'en' );

		// Bülten bandı.
		$g   = $this->xp( 'en/guide.html' );
		$news = $g->query( '//section[contains(@class,"news")]' )->item( 0 );
		$this->ayar_en( 'bulten_baslik', $news ? $this->metin( $g->query( './/h2', $news )->item( 0 ) ) : '' );
		$this->ayar_en( 'bulten_metin', $news ? $this->ic( $g->query( './/div[1]/p', $news )->item( 0 ) ) : '' );

		// Rehber listesi.
		$gh   = $g->query( '//section[contains(@class,"gh")]' )->item( 0 );
		$h1   = $gh ? $g->query( './/h1', $gh )->item( 0 ) : null;
		$kutu = $g->query( '//*[@id="yazilar"]//div[contains(@class,"editorial-note")]' )->item( 0 );
		$btn  = $kutu ? $g->query( './/a', $kutu )->item( 0 ) : null;
		$yazar = $g->query( '//p[contains(@class,"sfoot")]' )->item( 0 );

		$this->ayar_en( 'rehber_etiket', $gh ? $this->metin( $g->query( './/p[contains(@class,"label")]', $gh )->item( 0 ) ) : '' );
		$this->ayar_en( 'rehber_baslik', $h1 ? $this->ic( $h1 ) : '' );
		$this->ayar_en( 'rehber_aciklama', $gh ? $this->ic( $g->query( './/p[not(@class)]', $gh )->item( 0 ) ) : '' );
		$this->ayar_en( 'rehber_kutu_etiket', $kutu ? $this->metin( $g->query( './p[contains(@class,"eyebrow")]', $kutu )->item( 0 ) ) : '' );
		$this->ayar_en( 'rehber_kutu_baslik', $kutu ? $this->metin( $g->query( './h2', $kutu )->item( 0 ) ) : '' );
		$this->ayar_en( 'rehber_kutu_metin', $kutu ? $this->ic( $g->query( './p[not(@class)][1]', $kutu )->item( 0 ) ) : '' );
		$this->ayar_en(
			'rehber_kutu_buton',
			$btn ? array(
				'url'    => $this->adres( $btn->getAttribute( 'href' ) ),
				'title'  => $this->metin( $btn ),
				'target' => '',
			) : ''
		);
		// Kart alt satırı: paketteki büyük harfli yazı; panelde normal yazımıyla tutulur.
		$yazar_ad = $yazar ? $this->metin( $yazar ) : '';
		$this->ayar_en( 'rehber_yazar', 'SERVOMOTION' === $yazar_ad ? 'ServoMotion' : ucwords( strtolower( $yazar_ad ) ) );

		// Yazı sonu kutusu ve yazı altı notu (ilk yazıdan).
		$p    = $this->xp( 'en/guide-accuracy.html' );
		$son  = $p->query( '//div[contains(@class,"endbox")]' )->item( 0 );
		$sbtn = $son ? $p->query( './/a', $son )->item( 0 ) : null;
		$not  = $p->query( '//section[contains(@style,"padding:0")]//p' )->item( 0 );
		$this->ayar_en( 'rehber_son_baslik', $son ? $this->metin( $son->getElementsByTagName( 'h3' )->item( 0 ) ) : '' );
		$this->ayar_en( 'rehber_son_metin', $son ? $this->metin( $son->getElementsByTagName( 'p' )->item( 0 ) ) : '' );
		$this->ayar_en(
			'rehber_son_buton',
			$sbtn ? array(
				'url'    => $this->adres( $sbtn->getAttribute( 'href' ) ),
				'title'  => $this->metin( $sbtn ),
				'target' => '',
			) : ''
		);
		$this->ayar_en( 'rehber_not', $not ? str_replace( array( '<strong>', '</strong>' ), array( '<b>', '</b>' ), $this->ic( $not ) ) : '' );

		// İletişim etiketleri (iletişim sayfasındaki "Email" kutusu).
		$c = $this->xp( 'en/contact.html' );
		$et = array();
		foreach ( $c->query( '//div[contains(@class,"igrid")]/div[contains(@class,"ibox")][3]//p[contains(@class,"lbl")]' ) as $l ) {
			$et[] = $this->metin( $l );
		}
		$this->ayar_en( 'eposta_etiket', isset( $et[0] ) ? $et[0] : '' );
		$this->ayar_en( 'eposta_2_etiket', isset( $et[1] ) ? $et[1] : '' );

		// Ürün güvenliği (İngilizce Core sayfasındaki güvenlik bölümü).
		$k  = $this->xp( 'en/core-precisor.html' );
		$gs = $k->query( '//section[.//h2[contains(.,"LASER SAFETY")]]' )->item( 0 );
		if ( $gs ) {
			$this->ayar_en( 'guvenlik_etiket', $this->metin( $k->query( './/p[contains(@class,"label")]', $gs )->item( 0 ) ) );
			$this->ayar_en( 'guvenlik_baslik', $this->metin( $k->query( './/h2', $gs )->item( 0 ) ) );
			$maddeler = array();
			foreach ( $k->query( './/*[contains(@class,"symlist")]/div', $gs ) as $d ) {
				$metin = $this->ic( $k->query( './p', $d )->item( 0 ) );
				// Sertifika bağlantısı: doğrudan indirme yerine talep formu (karar: veri sayfaları ve sertifika formla).
				$metin = preg_replace( '#<a [^>]*href="[^"]*tuv[^"]*"[^>]*>[^<]*</a>#i', '<a href="' . esc_url( $this->talep_adresi() ) . '" style="font-weight:600;color:#0b0f14">Request the certificate</a>', $metin );
				$maddeler[] = array(
					'baslik' => $this->metin( $k->query( './b', $d )->item( 0 ) ),
					'metin'  => $metin,
				);
			}
			$this->ayar_en( 'guvenlik_maddeler', $maddeler );
		}
	}

	/**
	 * İngilizce Site Ayarları alanını yazar (boş değer yazılmaz; böylece Türkçe değere düşülür).
	 *
	 * @param string $ad    Alan adı.
	 * @param mixed  $deger Değer.
	 */
	protected function ayar_en( $ad, $deger ) {
		if ( servo_is_empty( $deger ) ) {
			return;
		}
		$onceki = get_field( $ad, servo_options_id( 'en' ), false );
		if ( $onceki === $deger ) {
			return;
		}
		WP_CLI::log( 'Site Settings (EN): ' . $ad );
		if ( ! $this->dry ) {
			update_field( 'field_servo_' . $ad, $deger, servo_options_id( 'en' ) );
		}
	}

	/**
	 * Tanı (yazmaz): İngilizce paket sayfalarının hangi bloklara çevrildiğini listeler.
	 */
	protected function adim_tani() {
		foreach ( array( 'index', 'company', 'applications', 'documents', 'glossary', 'faq', 'privacy', 'contact', 'service-manuals' ) as $ad ) {
			$satirlar = $this->sayfa_satirlari( 'en/' . $ad . '.html' );
			WP_CLI::log( str_pad( $ad, 16 ) . ' ' . implode( ' → ', wp_list_pluck( $satirlar, 'acf_fc_layout' ) ) );
		}
	}

	/**
	 * Servo_CLI_Icerik'in kullandığı adres çevirici (korumalı adres()'e köprü).
	 *
	 * @param string $href Bağlantı.
	 * @return string
	 */
	public function adres_disari( $href ) {
		return $this->adres( $href );
	}
}

/**
 * Rehber yazı gövdesini (div.prose) Gutenberg bloklarına çevirir (Servo_CLI'nin dönüştürücüsü, İngilizce adreslerle).
 */
class Servo_CLI_Icerik extends Servo_CLI {

	/**
	 * Adres çevirici.
	 *
	 * @var callable
	 */
	private $cevirici;

	/**
	 * @param callable $cevirici Paket bağlantısını site adresine çeviren fonksiyon.
	 */
	public function __construct( $cevirici ) {
		$this->cevirici = $cevirici;
	}

	/**
	 * Yazı gövdesini Gutenberg içeriğine çevirir.
	 *
	 * @param DOMElement $prose Gövde.
	 * @return string
	 */
	public function donustur( $prose ) {
		return $this->bloklar( $prose );
	}

	/**
	 * @param string $href Bağlantı.
	 * @return string
	 */
	protected function adres( $href ) {
		return call_user_func( $this->cevirici, $href );
	}
}
