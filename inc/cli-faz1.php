<?php
/**
 * WP-CLI: canlı site denetimi, FAZ 1 içerik betiği (`wp servo faz1`).
 *
 * `servomotion-site-tum131026/` klasöründeki son teslim paketini kaynak alır
 * ve yerel veritabanındaki içeriği ona getirir. Her adım tekrar çalıştırılabilir:
 * kayıtlar kaynak dosya adıyla (`_servo_kaynak`) veya slug ile eşleşir, kopya
 * oluşmaz, aynı sonuç yeniden üretilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAZ 1 içerik adımları.
 */
class Servo_Faz1 {

	/**
	 * Adımlar, çalışma sırasıyla.
	 *
	 * @var string[]
	 */
	const ADIMLAR = array(
		'temizlik',
		'profinet',
		'svg',
		'rehber',
		'kurumsal',
		'shuttle',
		'vakalar',
		'uygulamalar',
		'basari',
		'servis',
		'anasayfa',
		'metinler',
		'menuler',
		'arama',
	);

	/**
	 * Paket dizini (sonunda eğik çizgi).
	 *
	 * @var string
	 */
	protected $dir;

	/**
	 * Hiçbir şey yazmadan çalış.
	 *
	 * @var bool
	 */
	protected $dry;

	/**
	 * İçerik dili (tr | en): kayıt aramaları bu dille yapılır.
	 *
	 * @var string
	 */
	protected $dil = 'tr';

	/**
	 * Paket dosyası DOM önbelleği.
	 *
	 * @var array
	 */
	protected $doms = array();

	/**
	 * Giriş noktası.
	 *
	 * @param array $args       Adım adları (boşsa hepsi).
	 * @param array $assoc_args Seçenekler.
	 */
	public static function calistir( $args, $assoc_args ) {
		$e = new static();
		$e->baslat( $args, $assoc_args );
	}

	/**
	 * Adımları çalıştırır.
	 *
	 * @param array $args       Adım adları.
	 * @param array $assoc_args Seçenekler.
	 */
	protected function baslat( $args, $assoc_args ) {
		$this->dir = rtrim( isset( $assoc_args['kaynak'] ) ? $assoc_args['kaynak'] : get_template_directory() . '/servomotion-site-tum131026', '/' ) . '/';
		$this->dry = ! empty( $assoc_args['dry-run'] );

		if ( ! is_readable( $this->dir . 'index.html' ) ) {
			WP_CLI::error( 'Paket bulunamadı: ' . $this->dir );
		}

		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'Secure Custom Fields (SCF) etkin değil.' );
		}

		$adimlar = $args ? $args : static::ADIMLAR;

		foreach ( $adimlar as $adim ) {
			if ( ! in_array( $adim, static::ADIMLAR, true ) ) {
				WP_CLI::error( 'Bilinmeyen adım: ' . $adim . ' (geçerli: ' . implode( ', ', static::ADIMLAR ) . ')' );
			}
		}

		// Betik yazıları defalarca güncelleyeceği için gereksiz revizyon üretilmez.
		remove_action( 'post_updated', 'wp_save_post_revision' );

		foreach ( static::ADIMLAR as $adim ) {
			if ( in_array( $adim, $adimlar, true ) ) {
				WP_CLI::log( '' );
				WP_CLI::log( '── ' . $adim . ' ──' );
				$this->{'adim_' . $adim}();
			}
		}

		WP_CLI::success( $this->dry ? 'Kuru çalıştırma tamamlandı (hiçbir şey yazılmadı).' : 'Adımlar tamamlandı.' );
	}

	/* ------------------------------------------------------------------
	 * Adımlar
	 * ---------------------------------------------------------------- */

	/**
	 * M02: WordPress örnek sayfası, taslak gizlilik sayfası ve çöpteki örnek yazılar kalıcı silinir.
	 */
	protected function adim_temizlik() {
		$kayitlar = get_posts(
			array(
				'post_type'      => array( 'page', 'post' ),
				'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
				'posts_per_page' => -1,
				'post_name__in'  => array( 'ornek-sayfa', 'sample-page', 'gizlilik-ilkesi', 'merhaba-dunya__trashed', 'deneme-yazisi__trashed', 'hello-world__trashed' ),
			)
		);

		foreach ( $kayitlar as $k ) {
			WP_CLI::log( 'Sil: #' . $k->ID . ' ' . $k->post_type . ' ' . $k->post_name . ' (' . $k->post_status . ')' );
			if ( ! $this->dry ) {
				wp_delete_post( $k->ID, true );
			}
		}

		if ( ! $kayitlar ) {
			WP_CLI::log( 'Silinecek varsayılan kayıt yok.' );
		}
	}

	/**
	 * m03: ProfiNet → PROFINET (yazılar, alanlar ve ayarlar). Aynı uzunlukta olduğu için serileştirilmiş değerler bozulmaz.
	 */
	protected function adim_profinet() {
		global $wpdb;

		$tablolar = array(
			$wpdb->posts    => array( 'post_content', 'post_title', 'post_excerpt' ),
			$wpdb->postmeta => array( 'meta_value' ),
			$wpdb->options  => array( 'option_value' ),
		);

		foreach ( $tablolar as $tablo => $sutunlar ) {
			foreach ( $sutunlar as $sutun ) {
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
				$adet = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$tablo` WHERE BINARY `$sutun` LIKE '%ProfiNet%'" );
				if ( $adet && ! $this->dry ) {
					$wpdb->query( "UPDATE `$tablo` SET `$sutun` = REPLACE(`$sutun`, 'ProfiNet', 'PROFINET') WHERE BINARY `$sutun` LIKE '%ProfiNet%'" );
				}
				// phpcs:enable
				if ( $adet ) {
					WP_CLI::log( sprintf( '%s.%s: %d kayıt', $tablo, $sutun, $adet ) );
				}
			}
		}

		wp_cache_flush();
	}

	/**
	 * Şema SVG'leri: paketteki güncel çizimler tema dosyalarına yazılır.
	 *
	 * Uygulama şemaları (kart, sayfa ve vaka görselleri aynı çizimdir) ve
	 * rehber kartı şemaları. Eski vaka-ahsap.svg ve uygulama-film.svg kaldırılır.
	 */
	protected function adim_svg() {
		$hedef = get_template_directory() . '/assets/img/svg/';
		$yaz   = array();

		foreach ( array( 'lastik', 'nonwoven', 'ambalaj', 'film-folyo', 'tekstil', 'etiket' ) as $slug ) {
			$x   = $this->xp( 'uygulama-' . $slug . '.html' );
			$svg = $x->query( '//div[contains(@class,"pvis")]/svg' )->item( 0 );
			if ( $svg ) {
				$yaz[ 'uygulama-' . $slug ] = $this->svg_html( $svg );
			}
		}

		$x = $this->xp( 'rehber.html' );
		foreach ( $x->query( '//a[contains(@class,"svis") or contains(@class,"fvis")]' ) as $a ) {
			$svg = $x->query( './svg', $a )->item( 0 );
			if ( $svg && preg_match( '#^(rehber-[a-z0-9-]+)\.html$#', $a->getAttribute( 'href' ), $m ) ) {
				$yaz[ $m[1] ] = $this->svg_html( $svg );
			}
		}

		foreach ( $yaz as $ad => $html ) {
			$dosya = $hedef . $ad . '.svg';
			$eski  = file_exists( $dosya ) ? trim( (string) file_get_contents( $dosya ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( $eski !== $html ) {
				WP_CLI::log( ( $eski ? 'Güncelle: ' : 'Yeni: ' ) . $ad . '.svg' );
				if ( ! $this->dry ) {
					file_put_contents( $dosya, $html . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
				}
			}
		}

		foreach ( array( 'uygulama-film', 'vaka-ahsap' ) as $eski ) {
			if ( file_exists( $hedef . $eski . '.svg' ) ) {
				WP_CLI::log( 'Kaldır: ' . $eski . '.svg' );
				if ( ! $this->dry ) {
					unlink( $hedef . $eski . '.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
				}
			}
		}
	}

	/**
	 * M09, M10: rehber yazıları 23 Eylül 2026; Mühendislik değerlendirmesi kutuları; Site Ayarları > Rehber metinleri.
	 */
	protected function adim_rehber() {
		$liste = $this->xp( 'rehber.html' );
		$sira  = 0;

		foreach ( $liste->query( '//*[@id="yazilar"]//article[contains(@class,"featured") or contains(@class,"story")]' ) as $kart ) {
			$a = $liste->query( './/h2/a | .//h3/a', $kart )->item( 0 );
			if ( ! $a ) {
				continue;
			}
			$dosya = basename( $a->getAttribute( 'href' ) );
			$slug  = preg_replace( '/^rehber-|\.html$/', '', $dosya );
			$yazi  = $this->sayfa_bul( $slug, 'post' );

			if ( ! $yazi ) {
				WP_CLI::warning( 'Yazı bulunamadı: ' . $slug );
				continue;
			}

			// Tarih: 23 Eylül 2026; listedeki sıra dakikayla korunur.
			$tarih  = sprintf( '2026-09-23 12:%02d:00', max( 0, 59 - $sira ) );
			$icerik = $yazi->post_content;

			$px   = $this->xp( $dosya );
			$kutu = $px->query( '//div[contains(@class,"prose")]/div[contains(@class,"editorial-note")]' )->item( 0 );
			if ( $kutu && false === strpos( $icerik, 'is-style-muhendislik' ) && false === strpos( $icerik, 'is-style-editoryal' ) ) {
				$icerik = $this->editoryal_blogu( $px, $kutu ) . "\n\n" . ltrim( $icerik );
				WP_CLI::log( 'Editoryal kutu eklendi: ' . $slug );
			}

			if ( $yazi->post_date !== $tarih || $icerik !== $yazi->post_content ) {
				WP_CLI::log( sprintf( 'Güncelle: %s (%s → %s)', $slug, $yazi->post_date, $tarih ) );
				if ( ! $this->dry ) {
					wp_update_post(
						array(
							'ID'            => $yazi->ID,
							'post_content'  => $icerik,
							'post_date'     => $tarih,
							'post_date_gmt' => get_gmt_from_date( $tarih ),
							'edit_date'     => true,
						)
					);
				}
			}
			++$sira;
		}

		// Site Ayarları > Rehber: liste başlığı ve bilgi kutusu.
		$gh   = $liste->query( '//section[contains(@class,"gh")]' )->item( 0 );
		$h1   = $gh ? $liste->query( './/h1', $gh )->item( 0 ) : null;
		$kutu = $liste->query( '//*[@id="yazilar"]//div[contains(@class,"editorial-note")]' )->item( 0 );
		$btn  = $kutu ? $liste->query( './/a', $kutu )->item( 0 ) : null;

		$this->ayar( 'rehber_etiket', $gh ? $this->metin( $liste->query( './/p[contains(@class,"label")]', $gh )->item( 0 ) ) : '' );
		$this->ayar( 'rehber_baslik', $h1 ? $this->ic( $h1 ) : '' );
		$this->ayar( 'rehber_aciklama', $gh ? $this->ic( $liste->query( './/p[not(@class)]', $gh )->item( 0 ) ) : '' );
		$this->ayar( 'rehber_kutu_etiket', $kutu ? $this->metin( $liste->query( './p[contains(@class,"eyebrow")]', $kutu )->item( 0 ) ) : '' );
		$this->ayar( 'rehber_kutu_baslik', $kutu ? $this->metin( $liste->query( './h2', $kutu )->item( 0 ) ) : '' );
		$this->ayar( 'rehber_kutu_metin', $kutu ? $this->ic( $liste->query( './p[not(@class)][1]', $kutu )->item( 0 ) ) : '' );
		$this->ayar(
			'rehber_kutu_buton',
			$btn ? array(
				'url'    => $this->adres( $btn->getAttribute( 'href' ) ),
				'title'  => $this->metin( $btn ),
				'target' => '',
			) : ''
		);
	}

	/**
	 * M11: kurumsal sayfasında üretim/saha görselleri kalkar, grup markaları alan adını gösterir.
	 */
	protected function adim_kurumsal() {
		$sayfa = $this->sayfa_bul( 'kurumsal' );
		if ( ! $sayfa ) {
			WP_CLI::warning( 'Kurumsal sayfası bulunamadı.' );
			return;
		}

		$paket = $this->xp( 'kurumsal.html' );
		$alan  = array();
		foreach ( $paket->query( '//*[contains(@class,"brand-grid")]//a' ) as $a ) {
			$alan[ $this->adres( $a->getAttribute( 'href' ) ) ] = $this->metin( $a );
		}

		$satirlar = $this->satirlar( $sayfa->ID );
		$degisti  = false;

		foreach ( $satirlar as $i => $s ) {
			if ( 'kart_izgarasi' !== $s['acf_fc_layout'] ) {
				continue;
			}
			if ( ! empty( $s['fotograflar'] ) ) {
				$satirlar[ $i ]['fotograflar'] = array();
				$degisti                       = true;
				WP_CLI::log( 'Kurumsal: fotoğraf satırı (üretim/saha) kaldırıldı.' );
			}
			foreach ( (array) $s['kartlar'] as $k => $kart ) {
				$link = $this->alt( $kart, 'link' );
				if ( is_array( $link ) && isset( $alan[ $link['url'] ] ) && $alan[ $link['url'] ] !== $link['title'] ) {
					WP_CLI::log( sprintf( 'Kurumsal: bağlantı "%s" → "%s"', $link['title'], $alan[ $link['url'] ] ) );
					$link['title'] = $alan[ $link['url'] ];
					$satirlar[ $i ]['kartlar'][ $k ] = $this->alt_yaz( $kart, 'link', $link );
					$degisti                         = true;
				}
			}
		}

		if ( $degisti ) {
			$this->satirlari_yaz( $sayfa->ID, $satirlar );
		}
	}

	/**
	 * Shuttle sayfası: saha fotoğrafı bölümü kapatılır; m02 için Türkçe başlık ve açıklama.
	 */
	protected function adim_shuttle() {
		$sayfa = $this->sayfa_bul( 'shuttle-precisor' );
		if ( ! $sayfa ) {
			WP_CLI::warning( 'Shuttle sayfası bulunamadı.' );
			return;
		}

		if ( ! $this->dry ) {
			update_field( 'foto_goster', 0, $sayfa->ID );
		}
		WP_CLI::log( 'Shuttle: saha fotoğrafı bölümü kapatıldı.' );

		$this->seo( $sayfa->ID, 'shuttle-precisor.html' );
	}

	/**
	 * M03, M04: vaka kayıtları; ağaç vakası kalkar, film-folyo, tekstil, etiket gelir.
	 */
	protected function adim_vakalar() {
		$x     = $this->xp( 'basari-hikayeleri.html' );
		$sira  = 0;
		$slugs = array();

		foreach ( $x->query( '//div[contains(@class,"cases")]/article[contains(@class,"case")]' ) as $a ) {
			$slug    = $a->getAttribute( 'id' );
			$slugs[] = $slug;
			$mevcut  = $this->sayfa_bul( $slug, 'vaka' );
			$id      = $mevcut ? $mevcut->ID : 0;

			$baslik = $this->metin( $x->query( './/h3', $a )->item( 0 ) );
			$bilgi  = array();
			foreach ( $x->query( './/*[contains(@class,"cfg")]/div', $a ) as $d ) {
				$b       = $x->query( './b', $d )->item( 0 );
				$kucuk   = $b ? $x->query( './small', $b )->item( 0 ) : null;
				$deger   = '';
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
				$h4                  = $this->metin( $x->query( './h4', $d )->item( 0 ) );
				$metinler[ $h4 ] = $this->ic( $x->query( './p', $d )->item( 0 ) );
			}

			$alanlar = array(
				'etiket'     => $this->metin( $x->query( './/*[contains(@class,"tag")]', $a )->item( 0 ) ),
				'giris'      => $this->ic( $x->query( './/*[contains(@class,"lead")]', $a )->item( 0 ) ),
				'svg'        => 'uygulama-' . $slug,
				'bilgiler'   => $bilgi,
				'baslangic'  => isset( $metinler['Başlangıç durumu'] ) ? $metinler['Başlangıç durumu'] : '',
				'kurulan'    => isset( $metinler['Kurulan yapı'] ) ? $metinler['Kurulan yapı'] : '',
			);

			$cap = $x->query( './/figcaption', $a )->item( 0 );
			if ( $cap ) {
				$alanlar['foto_aciklama'] = $this->metin( $cap );
			}

			WP_CLI::log( ( $id ? 'Güncelle: ' : 'Oluştur: ' ) . 'vaka ' . $slug );

			if ( $this->dry ) {
				++$sira;
				continue;
			}

			$veri = array(
				'post_type'   => 'vaka',
				'post_status' => 'publish',
				'post_title'  => $baslik,
				'post_name'   => $slug,
				'menu_order'  => $sira,
			);
			if ( $id ) {
				$veri['ID'] = $id;
				wp_update_post( $veri );
			} else {
				$id = wp_insert_post( $veri );
			}

			foreach ( $alanlar as $ad => $deger ) {
				update_field( $ad, $deger, $id );
			}

			// Fotoğrafı olmayan vakada (film-folyo, tekstil, etiket) eski fotoğraf kalmasın.
			if ( ! $x->query( './/figure', $a )->length ) {
				update_field( 'foto', '', $id );
				update_field( 'foto_aciklama', '', $id );
			}
			++$sira;
		}

		// Pakette olmayan vakalar (ağaç ve kereste) ve görselleri silinir.
		foreach ( get_posts(
			array(
				'post_type'   => 'vaka',
				'post_status' => 'any',
				'numberposts' => -1,
			)
		) as $v ) {
			if ( pll_get_post_language( $v->ID ) !== $this->dil || in_array( $v->post_name, $slugs, true ) ) {
				continue;
			}
			WP_CLI::log( 'Sil: vaka #' . $v->ID . ' ' . $v->post_name );
			if ( $this->dry ) {
				continue;
			}
			$foto = (int) get_post_meta( $v->ID, 'foto', true );
			wp_delete_post( $v->ID, true );
			if ( $foto && 'attachment' === get_post_type( $foto ) ) {
				wp_delete_attachment( $foto, true );
				WP_CLI::log( 'Sil: ek #' . $foto . ' (vaka fotoğrafı)' );
			}
		}
	}

	/**
	 * M05, M08 (TR notlar): film-folyo, tekstil, etiket uygulama sayfaları; mevcut üç sayfanın not butonu.
	 */
	protected function adim_uygulamalar() {
		$kartlar = $this->uygulama_kartlari();
		$baslik  = array(
			'lastik'     => 'Lastik ve Kompozit',
			'nonwoven'   => 'Hijyenik Ürünler ve Nonwoven',
			'ambalaj'    => 'Ambalaj ve Kağıt',
			'film-folyo' => 'Film ve Folyo',
			'tekstil'    => 'Tekstil ve Teknik Kumaşlar',
			'etiket'     => 'Etiket ve Yapışkanlı Bant',
		);

		foreach ( $baslik as $slug => $ad ) {
			$id = $this->uygulama_bul( $slug );

			if ( ! $id ) {
				WP_CLI::warning( 'Uygulama kaydı yok: ' . $slug );
				continue;
			}

			$yeni = in_array( $slug, array( 'film-folyo', 'tekstil', 'etiket' ), true );

			if ( $yeni ) {
				WP_CLI::log( 'Uygulama: ' . $slug . ' (#' . $id . ') kendi sayfasıyla yayınlanıyor.' );
				if ( ! $this->dry ) {
					wp_update_post(
						array(
							'ID'         => $id,
							'post_title' => $ad,
							'post_name'  => $slug,
						)
					);
					update_field( 'detay_sayfasi', 1, $id );
					foreach ( $kartlar[ $slug ] as $alan => $deger ) {
						update_field( $alan, $deger, $id );
					}
					$satirlar = $this->sayfa_satirlari( 'uygulama-' . $slug . '.html' );
					$this->satirlari_yaz( $id, $satirlar );
					$this->seo( $id, 'uygulama-' . $slug . '.html' );
				}
			} else {
				// Mevcut üç sayfa: sadece hero not butonu (PDF indirme) güncellenir.
				$paket = $this->hero_butonlari( 'uygulama-' . $slug . '.html' );
				$satir = $this->satirlar( $id );
				if ( $satir && 'sayfa_hero' === $satir[0]['acf_fc_layout'] && $paket ) {
					$satir[0]['butonlar'] = $paket;
					WP_CLI::log( 'Uygulama: ' . $slug . ' hero butonları güncellendi.' );
					$this->satirlari_yaz( $id, $satir );
					if ( ! $this->dry ) {
						foreach ( $kartlar[ $slug ] as $alan => $deger ) {
							update_field( $alan, $deger, $id );
						}
					}
				}
			}
		}
	}

	/**
	 * M03, M04: Vaka çalışmaları → Başarı Hikayeleri.
	 */
	protected function adim_basari() {
		$sayfa = $this->sayfa_bul( 'referanslar' );
		if ( ! $sayfa ) {
			$sayfa = $this->sayfa_bul( 'basari-hikayeleri' );
		}
		if ( ! $sayfa ) {
			WP_CLI::warning( 'Vaka çalışmaları sayfası bulunamadı.' );
			return;
		}

		WP_CLI::log( 'Sayfa #' . $sayfa->ID . ' → Başarı Hikayeleri (/basari-hikayeleri/).' );

		$satirlar = $this->sayfa_satirlari( 'basari-hikayeleri.html' );

		if ( ! $this->dry ) {
			wp_update_post(
				array(
					'ID'         => $sayfa->ID,
					'post_title' => 'Başarı Hikayeleri',
					'post_name'  => 'basari-hikayeleri',
				)
			);
			$this->satirlari_yaz( $sayfa->ID, $satirlar );
			$this->seo( $sayfa->ID, 'basari-hikayeleri.html' );
		}
	}

	/**
	 * M06: servis sayfası ve üç alt sayfa (destek hattı, iade ve sevkiyat, kılavuzlar).
	 *
	 * Alt sayfalardaki talep formları FAZ 2'de (talep_formu bloğu ve /talep uç noktası) eklenir.
	 */
	protected function adim_servis() {
		$sayfalar = array(
			'servis'              => array( 'servis.html', 'Servis', 0 ),
			'servis-destek-hatti' => array( 'servis-destek-hatti.html', 'Destek Hattı', 1 ),
			'servis-iade-sevkiyat' => array( 'servis-iade-sevkiyat.html', 'İade ve Sevkiyat', 1 ),
			'servis-kilavuzlar'   => array( 'servis-kilavuzlar.html', 'Kılavuzlar', 1 ),
		);

		foreach ( $sayfalar as $slug => $b ) {
			$mevcut = $this->sayfa_bul( $slug );
			$id     = $mevcut ? $mevcut->ID : 0;
			WP_CLI::log( ( $id ? 'Güncelle: ' : 'Oluştur: ' ) . 'sayfa ' . $slug );

			$satirlar = $this->sayfa_satirlari( $b[0] );

			if ( $this->dry ) {
				continue;
			}

			if ( ! $id ) {
				$id = wp_insert_post(
					array(
						'post_type'   => 'page',
						'post_status' => 'publish',
						'post_title'  => $b[1],
						'post_name'   => $slug,
					)
				);
			}

			$this->satirlari_yaz( $id, $satirlar );
			$this->seo( $id, $b[0] );
		}
	}

	/**
	 * Ana sayfa: altı uygulama kartı (örnek kurulum ve bağlantılarla) ve alt notlar.
	 */
	protected function adim_anasayfa() {
		$sayfa = $this->sayfa_bul( 'ana-sayfa' );
		if ( ! $sayfa ) {
			$onceki = (int) get_option( 'page_on_front' );
			$sayfa  = $onceki ? get_post( $onceki ) : null;
		}
		if ( ! $sayfa ) {
			WP_CLI::warning( 'Ana sayfa bulunamadı.' );
			return;
		}

		$paket = $this->xp( 'index.html' );
		$bolum = $paket->query( '//section[@id="uygulama-alanlari"]' )->item( 0 );
		if ( ! $bolum ) {
			WP_CLI::warning( 'index.html içinde #uygulama-alanlari yok.' );
			return;
		}

		$notlar = array();
		foreach ( $paket->query( './/p[contains(@class,"appfoot")]', $bolum ) as $p ) {
			$notlar[] = array( 'metin' => $this->ic( $p ) );
		}

		$one = array();
		foreach ( array( 'lastik', 'nonwoven', 'ambalaj', 'film-folyo', 'tekstil', 'etiket' ) as $slug ) {
			$id = $this->uygulama_bul( $slug );
			if ( $id ) {
				$one[] = $id;
			}
		}

		$satirlar = $this->satirlar( $sayfa->ID );
		$degisti  = false;

		foreach ( $satirlar as $i => $s ) {
			if ( 'uygulama_alanlari' !== $s['acf_fc_layout'] ) {
				continue;
			}
			$satirlar[ $i ]['one_cikanlar'] = $one;
			$satirlar[ $i ]['digerleri']    = array();
			$satirlar[ $i ]['ara_etiket']   = '';
			$satirlar[ $i ]['notlar']       = $notlar;
			$degisti                        = true;
			WP_CLI::log( 'Ana sayfa: uygulama alanları bloğu (6 kart, ' . count( $notlar ) . ' not).' );
		}

		if ( $degisti ) {
			$this->satirlari_yaz( $sayfa->ID, $satirlar );
		}
	}

	/**
	 * Üst metinler: SSS, sözlük ve KVKK sayfalarının hero ve KVKK metin notları.
	 */
	protected function adim_metinler() {
		foreach ( array(
			'sss'    => 'sss.html',
			'sozluk' => 'sozluk.html',
			'kvkk'   => 'kvkk.html',
		) as $slug => $dosya ) {
			$sayfa = $this->sayfa_bul( $slug );
			if ( ! $sayfa ) {
				continue;
			}

			$hero     = $this->hero_satiri( $this->xp( $dosya ) );
			$satirlar = $this->satirlar( $sayfa->ID );
			if ( ! $satirlar || 'sayfa_hero' !== $satirlar[0]['acf_fc_layout'] ) {
				continue;
			}

			foreach ( array( 'etiket', 'baslik', 'vurgu', 'giris' ) as $alan ) {
				$satirlar[0][ $alan ] = isset( $hero[ $alan ] ) ? $hero[ $alan ] : '';
			}

			// Buton yazıları paketten, adresler olduğu gibi.
			$paket_btn = isset( $hero['butonlar'] ) ? $hero['butonlar'] : array();
			foreach ( (array) $satirlar[0]['butonlar'] as $k => $b ) {
				if ( isset( $paket_btn[ $k ] ) ) {
					$link = $this->alt( $b, 'link' );
					if ( is_array( $link ) ) {
						$link['title']                 = $paket_btn[ $k ]['link']['title'];
						$satirlar[0]['butonlar'][ $k ] = $this->alt_yaz( $b, 'link', $link );
					}
				}
			}

			if ( 'kvkk' === $slug ) {
				$x   = $this->xp( $dosya );
				$upd = $x->query( '//*[contains(@class,"upd")]' )->item( 0 );
				foreach ( $satirlar as $i => $s ) {
					if ( 'uzun_metin' === $s['acf_fc_layout'] ) {
						$satirlar[ $i ]['ust_not'] = $this->metin( $upd );
						$satirlar[ $i ]['alt_not'] = '';
					}
				}
			}

			WP_CLI::log( 'Metin: ' . $slug );
			$this->satirlari_yaz( $sayfa->ID, $satirlar );
		}
	}

	/**
	 * M03, m01, M05, M06: ana menü ve footer menüsü.
	 */
	protected function adim_menuler() {
		$ana = wp_get_nav_menu_object( 'ana-menu' );
		$alt = wp_get_nav_menu_object( 'footer-menusu' );

		if ( ! $ana || ! $alt ) {
			WP_CLI::warning( 'Menüler bulunamadı (ana-menu, footer-menusu).' );
			return;
		}

		// Ana menü: etiket.
		foreach ( wp_get_nav_menu_items( $ana->term_id ) as $it ) {
			if ( 'VAKA ÇALIŞMALARI' === $it->title ) {
				$this->menu_ad( $ana->term_id, $it, 'BAŞARI HİKAYELERİ' );
			}
		}

		// Footer: kolon başlıkları ve öğeler, tasarımdaki sırayla.
		$sayfa = function ( $slug ) {
			$p = $this->sayfa_bul( $slug );
			return $p ? array( 'post_type', 'page', $p->ID ) : null;
		};
		$uyg   = function ( $slug ) {
			$id = $this->uygulama_bul( $slug );
			return $id ? array( 'post_type', 'uygulama', $id ) : null;
		};

		$istenen = array(
			'UYGULAMA ALANLARI' => array(
				array( 'Lastik ve kompozit', $uyg( 'lastik' ) ),
				array( 'Hijyenik ürünler ve nonwoven', $uyg( 'nonwoven' ) ),
				array( 'Ambalaj ve kağıt', $uyg( 'ambalaj' ) ),
				array( 'Film ve folyo', $uyg( 'film-folyo' ) ),
				array( 'Tekstil ve teknik kumaşlar', $uyg( 'tekstil' ) ),
				array( 'Etiket ve yapışkanlı bant', $uyg( 'etiket' ) ),
			),
			'BAŞARI HİKAYELERİ' => array(
				array( 'Sahadan altı uygulama', $sayfa( 'basari-hikayeleri' ) ),
				array( 'Servis ve devreye alma', $sayfa( 'servis' ) ),
				array( 'Destek hattı', $sayfa( 'servis-destek-hatti' ) ),
				array( 'Kılavuzlar', $sayfa( 'servis-kilavuzlar' ) ),
				array( 'Rehber yazıları', $sayfa( 'rehber' ) ),
			),
		);

		// Kolon başlığı: VAKA ÇALIŞMALARI → BAŞARI HİKAYELERİ.
		foreach ( wp_get_nav_menu_items( $alt->term_id ) as $it ) {
			if ( 'custom' === $it->type && 'VAKA ÇALIŞMALARI' === $it->title ) {
				$this->menu_ad( $alt->term_id, $it, 'BAŞARI HİKAYELERİ' );
			}
		}

		if ( $this->dry ) {
			WP_CLI::log( 'Menü: kuru çalıştırmada footer öğeleri yazılmadı.' );
			return;
		}

		$tum   = wp_get_nav_menu_items( $alt->term_id );
		$kolon = array();
		foreach ( $tum as $it ) {
			if ( 'custom' === $it->type && '#' === $it->url ) {
				$kolon[ $it->title ] = $it;
			}
		}

		// Öğeleri oluştur veya bul; her kolon için istenen sıradaki ID listesi.
		$sira = array();
		foreach ( $istenen as $kolon_ad => $liste ) {
			if ( ! isset( $kolon[ $kolon_ad ] ) ) {
				WP_CLI::warning( 'Footer kolonu yok: ' . $kolon_ad );
				continue;
			}
			$ebeveyn = $kolon[ $kolon_ad ]->ID;
			foreach ( $liste as $o ) {
				if ( ! $o[1] ) {
					WP_CLI::warning( 'Menü hedefi yok: ' . $o[0] );
					continue;
				}
				$var = null;
				foreach ( $tum as $it ) {
					if ( (int) $it->menu_item_parent === (int) $ebeveyn && (int) $it->object_id === (int) $o[1][2] && $it->object === $o[1][1] ) {
						$var = $it;
					}
				}
				$yeni = wp_update_nav_menu_item(
					$alt->term_id,
					$var ? $var->ID : 0,
					array(
						'menu-item-title'     => $o[0],
						'menu-item-type'      => $o[1][0],
						'menu-item-object'    => $o[1][1],
						'menu-item-object-id' => $o[1][2],
						'menu-item-parent-id' => $ebeveyn,
						'menu-item-status'    => 'publish',
					)
				);
				if ( ! $var ) {
					WP_CLI::log( 'Menü: eklendi "' . $o[0] . '" (' . $kolon_ad . ')' );
				} elseif ( $var->title !== $o[0] ) {
					WP_CLI::log( 'Menü: "' . $var->title . '" → "' . $o[0] . '"' );
				}
				$sira[ $ebeveyn ][] = (int) $yeni;
			}
		}

		// Sıra numaraları baştan, ardışık: kolon, ardından kendi öğeleri.
		$tum    = wp_get_nav_menu_items( $alt->term_id );
		$kokler = array_values(
			array_filter(
				$tum,
				function ( $i ) {
					return 0 === (int) $i->menu_item_parent;
				}
			)
		);
		usort(
			$kokler,
			function ( $a, $b ) {
				return $a->menu_order <=> $b->menu_order;
			}
		);

		$pos = 1;
		foreach ( $kokler as $k ) {
			wp_update_post(
				array(
					'ID'         => $k->ID,
					'menu_order' => $pos++,
				)
			);

			if ( isset( $sira[ $k->ID ] ) ) {
				$cocuk = $sira[ $k->ID ];
			} else {
				$c = array_values(
					array_filter(
						$tum,
						function ( $i ) use ( $k ) {
							return (int) $i->menu_item_parent === (int) $k->ID;
						}
					)
				);
				usort(
					$c,
					function ( $a, $b ) {
						return $a->menu_order <=> $b->menu_order;
					}
				);
				$cocuk = wp_list_pluck( $c, 'ID' );
			}

			foreach ( $cocuk as $cid ) {
				wp_update_post(
					array(
						'ID'         => $cid,
						'menu_order' => $pos++,
					)
				);
			}
		}
	}

	/**
	 * Arama dizinini yeniler (blok metinleri).
	 */
	protected function adim_arama() {
		if ( $this->dry || ! function_exists( 'servo_arama_dizinle' ) ) {
			return;
		}

		foreach ( get_posts(
			array(
				'post_type'   => 'page',
				'post_status' => 'any',
				'numberposts' => -1,
				'fields'      => 'ids',
			)
		) as $id ) {
			servo_arama_dizinle( $id );
		}
		if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
			servo_arama_tumunu_dizinle();
		}
		WP_CLI::log( 'Arama dizini yenilendi.' );
	}

	/* ------------------------------------------------------------------
	 * Paket okuma: sayfa → blok satırları
	 * ---------------------------------------------------------------- */

	/**
	 * Paket sayfasının <main> bölümlerini blok satırlarına çevirir.
	 *
	 * @param string $dosya Paket dosyası (ör. servis.html).
	 * @return array Blok satırları (alan adlarıyla).
	 */
	protected function sayfa_satirlari( $dosya ) {
		$x    = $this->xp( $dosya );
		$main = $x->query( '//main' )->item( 0 );
		$out  = array();

		foreach ( $main->childNodes as $s ) {
			if ( XML_ELEMENT_NODE !== $s->nodeType || 'section' !== $s->nodeName ) {
				continue;
			}
			$sinif = ' ' . $s->getAttribute( 'class' ) . ' ';

			if ( false !== strpos( $sinif, ' phero ' ) ) {
				$out[] = $this->hero_satiri( $x, $s, $dosya );
			} elseif ( false !== strpos( $sinif, ' cta-band ' ) ) {
				$out[] = $this->cta_satiri( $x, $s );
			} elseif ( false !== strpos( $sinif, ' news ' ) ) {
				continue; // Bülten bandı footer şablonundan gelir.
			} elseif ( $this->var_mi( $x, $s, './/form[not(contains(@class,"newsform"))]' ) ) {
				$out[] = $this->form_satiri( $x, $s );
			} else {
				$satir = $this->bolum_satiri( $x, $s, $dosya );
				if ( $satir ) {
					$out[] = $satir;
				}
			}
		}

		return $out;
	}

	/**
	 * Hero bölümü.
	 *
	 * @param DOMXPath     $x     XPath.
	 * @param DOMNode|null $s     Bölüm (boşsa ilk .phero).
	 * @param string       $dosya Dosya adı (görsel adı çıkarmak için).
	 * @return array
	 */
	protected function hero_satiri( $x, $s = null, $dosya = '' ) {
		$s    = $s ? $s : $x->query( '//section[contains(@class,"phero")]' )->item( 0 );
		$sol  = $x->query( './div[contains(@class,"wrap")]/div[1]', $s )->item( 0 );
		$h1   = $x->query( './/h1', $sol )->item( 0 );
		$em   = $h1 ? $x->query( './em', $h1 )->item( 0 ) : null;
		$ana  = '';
		if ( $h1 ) {
			foreach ( $h1->childNodes as $c ) {
				if ( $c !== $em && XML_TEXT_NODE === $c->nodeType ) {
					$ana .= $c->textContent;
				}
			}
		}

		$satir = array(
			'acf_fc_layout' => 'sayfa_hero',
			'duzen'         => 'standart',
			'etiket'        => $this->metin( $x->query( './p[contains(@class,"label")]', $sol )->item( 0 ) ),
			'baslik'        => trim( preg_replace( '/\s+/u', ' ', $ana ) ),
			'vurgu'         => $this->metin( $em ),
			'giris'         => $this->ic( $x->query( './p[contains(@class,"intro")]', $sol )->item( 0 ) ),
			'butonlar'      => $this->butonlar( $x, $sol ),
			'yan_icerik'    => 'yok',
		);

		$crumb = $x->query( './p[contains(@class,"crumb")]', $sol )->item( 0 );
		if ( $crumb ) {
			$a                    = $x->query( './a', $crumb )->item( 0 );
			$satir['ust_yol']     = array(
				'url'    => $this->adres( $a->getAttribute( 'href' ) ),
				'title'  => $this->metin( $a ),
				'target' => '',
			);
			$son                  = '';
			foreach ( $crumb->childNodes as $c ) {
				if ( XML_TEXT_NODE === $c->nodeType ) {
					$son .= $c->textContent;
				}
			}
			$satir['ust_yol_son'] = trim( preg_replace( '/^\s*·\s*/u', '', preg_replace( '/\s+/u', ' ', $son ) ) );
		}

		// Yan içerik: rakamlar (sağ sütun) veya görsel + rakamlar.
		$rakamlar = $x->query( './/*[contains(@class,"pfacts")]', $s )->item( 0 );
		$pvis     = $x->query( './/*[contains(@class,"pvis")]', $s )->item( 0 );

		if ( $rakamlar ) {
			$satir['rakamlar'] = array();
			foreach ( $x->query( './div', $rakamlar ) as $d ) {
				$b     = $x->query( './b', $d )->item( 0 );
				$kucuk = $b ? $x->query( './small', $b )->item( 0 ) : null;
				$deger = '';
				foreach ( $b ? $b->childNodes : array() as $c ) {
					if ( XML_TEXT_NODE === $c->nodeType ) {
						$deger .= $c->textContent;
					}
				}
				$satir['rakamlar'][] = array(
					'deger'    => trim( $deger ),
					'birim'    => $this->metin( $kucuk ),
					'aciklama' => $this->metin( $x->query( './span', $d )->item( 0 ) ),
				);
			}
			$satir['yan_icerik'] = $pvis ? 'gorsel' : 'rakamlar';
		}

		if ( $pvis && $this->hero_svg_adi( $dosya ) ) {
			$satir['gorsel_svg'] = $this->hero_svg_adi( $dosya );
		}

		// Fotoğraflı hero (.pvis > img): görsel Medya'daki aynı dosyadan seçilir.
		$img = $pvis ? $x->query( './img', $pvis )->item( 0 ) : null;
		if ( $img ) {
			$satir['yan_icerik'] = 'gorsel';
			$satir['gorsel']     = $this->gorsel_bul( $img->getAttribute( 'src' ) );
		}

		return $satir;
	}

	/**
	 * Buton listesi (.actions > a).
	 *
	 * @param DOMXPath $x   XPath.
	 * @param DOMNode  $sol Hero sol sütunu.
	 * @return array
	 */
	protected function butonlar( $x, $sol ) {
		$out = array();
		foreach ( $x->query( './/*[contains(@class,"actions")]/a', $sol ) as $a ) {
			$href  = $a->getAttribute( 'href' );
			$rozet = '';
			$baslik = '';
			foreach ( $a->childNodes as $c ) {
				if ( XML_ELEMENT_NODE === $c->nodeType && 'span' === $c->nodeName ) {
					$rozet = $this->metin( $c );
				} else {
					$baslik .= $c->textContent;
				}
			}
			$out[] = array(
				'link'     => array(
					'url'    => $this->adres( $href ),
					'title'  => trim( preg_replace( '/\s+/u', ' ', $baslik ) ),
					'target' => '',
				),
				'birincil' => false !== strpos( ' ' . $a->getAttribute( 'class' ) . ' ', ' primary ' ) ? 1 : 0,
				'rozet'    => $rozet,
				'indir'    => $a->hasAttribute( 'download' ) ? 1 : 0,
			);
		}

		return $out;
	}

	/**
	 * Bir paket sayfasının hero butonları.
	 *
	 * @param string $dosya Dosya.
	 * @return array
	 */
	protected function hero_butonlari( $dosya ) {
		$h = $this->hero_satiri( $this->xp( $dosya ), null, $dosya );

		return $h['butonlar'];
	}

	/**
	 * Çağrı bandı (cta-band).
	 *
	 * @param DOMXPath $x XPath.
	 * @param DOMNode  $s Bölüm.
	 * @return array
	 */
	protected function cta_satiri( $x, $s ) {
		$linkler = array();
		foreach ( $x->query( './/a[contains(@class,"btn")]', $s ) as $a ) {
			$href = $a->getAttribute( 'href' );
			if ( 0 === strpos( $href, 'tel:' ) ) {
				$linkler[] = array(
					'tur'  => 'telefon',
					'konu' => '',
				);
			} elseif ( 0 === strpos( $href, 'mailto:' ) ) {
				$linkler[] = array(
					'tur'  => 'eposta',
					'konu' => '',
				);
			} else {
				$linkler[] = array(
					'tur'  => 'baglanti',
					'link' => array(
						'url'    => $this->adres( $href ),
						'title'  => $this->metin( $a ),
						'target' => '',
					),
				);
			}
		}

		return array(
			'acf_fc_layout' => 'cta',
			'gorunum'       => 'bant',
			'baslik'        => $this->ic( $x->query( './/h2', $s )->item( 0 ) ),
			'metin'         => $this->ic( $x->query( './/h2/following-sibling::p', $s )->item( 0 ) ),
			'linkler'       => $linkler,
			'capa'          => '',
		);
	}

	/**
	 * Paketteki bir görselin (ör. ../assets/core-precisor.jpg) Medya'daki karşılığı (dosya adına göre).
	 *
	 * @param string $src Paketteki yol.
	 * @return int Ek ID'si (yoksa 0).
	 */
	protected function gorsel_bul( $src ) {
		global $wpdb;

		$ad = basename( $src );
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND (meta_value LIKE %s OR meta_value = %s) ORDER BY post_id ASC LIMIT 1", '%/' . $wpdb->esc_like( $ad ), $ad ) ); // phpcs:ignore WordPress.DB

		return $id ? (int) $id : 0;
	}

	/**
	 * Uygulama sayfası hero şemasının tema dosyası adı (uygulama-<slug>); uygulama sayfası değilse boş.
	 *
	 * @param string $dosya Paket dosyası.
	 * @return string
	 */
	protected function hero_svg_adi( $dosya ) {
		return preg_match( '#^uygulama-([a-z-]+)\.html$#', $dosya, $m ) ? 'uygulama-' . $m[1] : '';
	}

	/**
	 * Formla istenen dokümanlar için talep formunun adresi.
	 *
	 * @return string
	 */
	protected function talep_adresi() {
		return home_url( '/dokumanlar/#talep' );
	}

	/**
	 * Talep formu bölümü (talep_formu bloğu).
	 *
	 * Form türü ilk alanın kimliğinden anlaşılır (v- doküman, d- destek, i- iade, k- kılavuz).
	 *
	 * @param DOMXPath $x XPath.
	 * @param DOMNode  $s Bölüm.
	 * @return array
	 */
	protected function form_satiri( $x, $s ) {
		$ilk   = $x->query( './/form//input[@id] | .//form//textarea[@id] | .//form//select[@id]', $s )->item( 0 );
		$onek  = $ilk ? substr( $ilk->getAttribute( 'id' ), 0, 2 ) : '';
		$turler = array(
			'v-' => 'dokuman',
			'e-' => 'dokuman',
			'd-' => 'destek',
			'h-' => 'destek',
			'i-' => 'iade',
			'r-' => 'iade',
			'k-' => 'kilavuz',
			'm-' => 'kilavuz',
		);
		$hd    = $x->query( './/*[contains(@class,"section-head")]', $s )->item( 0 );
		$acik  = array();
		foreach ( $hd ? $x->query( './p[not(@class)]', $hd ) : array() as $p ) {
			$acik[] = $this->ic( $p );
		}
		$btn  = $x->query( './/button[contains(@class,"formbtn")]', $s )->item( 0 );
		$not  = $x->query( './/p[contains(@class,"formnote")]', $s )->item( 0 );

		return array(
			'acf_fc_layout' => 'talep_formu',
			'baslik_stili'  => 'standart',
			'etiket'        => $hd ? $this->metin( $x->query( './p[contains(@class,"label")]', $hd )->item( 0 ) ) : '',
			'baslik'        => $hd ? $this->ic( $x->query( './h2', $hd )->item( 0 ) ) : '',
			'aciklama'      => implode( "\n\n", $acik ),
			'tur'           => isset( $turler[ $onek ] ) ? $turler[ $onek ] : 'destek',
			'buton'         => $this->metin( $btn ),
			// Paketteki "KVKK metnimiz burada" cümlesi, formların altındaki standart aydınlatma notuyla (Site Ayarları > Formlar) tekrarlanmasın diye çıkarılır.
			'not'           => $not ? trim( (string) preg_replace( '#\s*(?:Talepleriniz yalnızca dokümanların iletilmesi için kullanılır;\s*<a[^>]*>KVKK metnimiz</a>\s*burada\.|Your details are used only to send the requested documents\.\s*See our\s*<a[^>]*>privacy notice</a>\s*\.)#u', '', $this->ic( $not ) ) ) : '',
			'arka_plan'     => $this->arka_plan( $s ),
			'capa'          => $s->getAttribute( 'id' ),
		);
	}

	/**
	 * Başlıklı içerik bölümü: gövdesine göre blok seçilir.
	 *
	 * @param DOMXPath $x     XPath.
	 * @param DOMNode  $s     Bölüm.
	 * @param string   $dosya Dosya adı.
	 * @return array|null
	 */
	protected function bolum_satiri( $x, $s, $dosya ) {
		$ortak = array(
			'baslik_stili' => 'standart',
			'arka_plan'    => $this->arka_plan( $s ),
			'capa'         => $s->getAttribute( 'id' ),
		);
		$hd    = $x->query( './/*[contains(@class,"section-head")]', $s )->item( 0 );
		if ( $hd ) {
			$aciklama = array();
			foreach ( $x->query( './p[not(@class)]', $hd ) as $p ) {
				$aciklama[] = $this->ic( $p );
			}
			$ortak['etiket']   = $this->metin( $x->query( './p[contains(@class,"label")]', $hd )->item( 0 ) );
			$ortak['baslik']   = $this->ic( $x->query( './h2', $hd )->item( 0 ) );
			$ortak['aciklama'] = implode( "\n\n", $aciklama );
		}

		$g = function ( $sorgu ) use ( $x, $s ) {
			return $x->query( $sorgu, $s )->item( 0 );
		};

		if ( $g( './/*[contains(@class,"benefits")]' ) ) {
			$ogeler = array();
			foreach ( $x->query( './/*[contains(@class,"benefits")]/article', $s ) as $a ) {
				$ogeler[] = array(
					'numara'     => $this->metin( $x->query( './*[contains(@class,"number")]', $a )->item( 0 ) ),
					'alt_etiket' => '',
					'baslik'     => $this->metin( $x->query( './h3', $a )->item( 0 ) ),
					'metin'      => $this->ic( $x->query( './p', $a )->item( 0 ) ),
					'sure'       => '',
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'numarali_kartlar',
				'gorunum'       => 'kazanim',
				'ogeler'        => $ogeler,
			);
		}

		if ( $g( './/*[contains(@class,"milestones")]' ) ) {
			$ogeler = array();
			foreach ( $x->query( './/*[contains(@class,"milestones")]/article', $s ) as $a ) {
				$key = $x->query( './*[contains(@class,"milestone-key")]', $a )->item( 0 );
				$sp  = $key ? $x->query( './span', $key )->item( 0 ) : null;
				$no  = '';
				foreach ( $key ? $key->childNodes : array() as $c ) {
					if ( XML_TEXT_NODE === $c->nodeType ) {
						$no .= $c->textContent;
					}
				}
				$ogeler[] = array(
					'numara'     => trim( preg_replace( '/\s+/u', ' ', $no ) ),
					'alt_etiket' => $this->metin( $sp ),
					'baslik'     => $this->metin( $x->query( './div[2]/h3', $a )->item( 0 ) ),
					'metin'      => $this->ic( $x->query( './div[2]/p', $a )->item( 0 ) ),
					'sure'       => '',
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'numarali_kartlar',
				'gorunum'       => 'zaman',
				'ogeler'        => $ogeler,
			);
		}

		if ( $g( './div[contains(@class,"wrap") and contains(@class,"company-story")]' ) ) {
			$par = array();
			foreach ( $x->query( './div[contains(@class,"company-story")]/div[not(contains(@class,"section-head"))]/p', $s ) as $p ) {
				$par[] = $this->ic( $p );
			}
			return $ortak + array(
				'acf_fc_layout' => 'metin',
				'duzen'         => 'hikaye',
				'paragraflar'   => implode( "\n\n", $par ),
			);
		}

		if ( $g( './div[contains(@class,"wrap") and contains(@class,"about")]' ) ) {
			$sol = $x->query( './div[contains(@class,"about")]/div[1]', $s )->item( 0 );
			$sag = $x->query( './div[contains(@class,"about")]/div[2]', $s )->item( 0 );
			$par = array();
			foreach ( $x->query( './p', $sag ) as $p ) {
				$par[] = $this->ic( $p );
			}
			$bag = array();
			foreach ( $x->query( './a', $sag ) as $a ) {
				$bag[] = array(
					'link' => array(
						'url'    => $this->adres( $a->getAttribute( 'href' ) ),
						'title'  => $this->metin( $a ),
						'target' => '',
					),
				);
			}
			return array(
				'acf_fc_layout' => 'metin',
				'duzen'         => 'iki_kolon',
				'etiket'        => $this->metin( $x->query( './p[contains(@class,"label")]', $sol )->item( 0 ) ),
				'baslik'        => $this->ic( $x->query( './h2', $sol )->item( 0 ) ),
				'paragraflar'   => implode( "\n\n", $par ),
				'baglantilar'   => $bag,
				'arka_plan'     => $this->arka_plan( $s ),
				'capa'          => $s->getAttribute( 'id' ),
			);
		}

		if ( $g( './/*[contains(@class,"brand-grid")]' ) ) {
			$kartlar = array();
			foreach ( $x->query( './/*[contains(@class,"brand-card")]', $s ) as $k ) {
				$h3 = $x->query( './h3', $k )->item( 0 );
				$a  = $x->query( './a', $k )->item( 0 );
				$kartlar[] = array(
					'baslik'       => $this->metin( $h3 ),
					'marka_yazisi' => $h3 && false !== strpos( (string) $h3->getAttribute( 'style' ), 'Poppins' ) ? 1 : 0,
					'metin'        => $this->ic( $x->query( './p', $k )->item( 0 ) ),
					'link'         => $a ? array(
						'url'    => $this->adres( $a->getAttribute( 'href' ) ),
						'title'  => $this->metin( $a ),
						'target' => '',
					) : '',
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'kart_izgarasi',
				'gorunum'       => 'marka',
				'kartlar'       => $kartlar,
				'fotograflar'   => array(),
			);
		}

		if ( $g( './/table[contains(@class,"ctable")]' ) ) {
			$sut = array();
			foreach ( $x->query( './/table[contains(@class,"ctable")]/thead//th', $s ) as $th ) {
				$sut[] = array( 'baslik' => $this->ic( $th ) );
			}
			$sat = array();
			$alt = array();
			$alt_et = '';
			foreach ( $x->query( './/table[contains(@class,"ctable")]/tbody/tr', $s ) as $tr ) {
				$tds = $x->query( './td', $tr );
				if ( $x->query( './/*[contains(@class,"infra")]', $tr )->length ) {
					$alt_et = $this->metin( $tds->item( 0 ) );
					foreach ( $x->query( './/*[contains(@class,"inf")]', $tr ) as $inf ) {
						$svg = $x->query( './svg', $inf )->item( 0 );
						$alt[] = array(
							'ikon'  => $svg ? $this->svg_adi_grup( $svg, 'ikon-' ) : '',
							'metin' => $this->metin( $x->query( './span', $inf )->item( 0 ) ),
						);
					}
					continue;
				}
				$hucre = array();
				for ( $i = 1; $i < $tds->length; $i++ ) {
					$td    = $tds->item( $i );
					$sm    = $x->query( './span[contains(@class,"sm")]', $td )->item( 0 );
					$sinif = ' ' . $td->getAttribute( 'class' ) . ' ';
					$kopya = $td->cloneNode( true );
					foreach ( iterator_to_array( $kopya->getElementsByTagName( 'span' ) ) as $sp ) {
						if ( false !== strpos( ' ' . $sp->getAttribute( 'class' ) . ' ', ' sm ' ) ) {
							$sp->parentNode->removeChild( $sp );
						}
					}
					$hucre[] = array(
						'metin'     => $this->ic( $kopya ),
						'kucuk_not' => $this->metin( $sm ),
						'durum'     => false !== strpos( $sinif, ' yes ' ) ? 'evet' : ( false !== strpos( $sinif, ' no ' ) ? 'hayir' : 'yok' ),
					);
				}
				$sat[] = array(
					'etiket'   => $this->metin( $tds->item( 0 ) ),
					'hucreler' => $hucre,
				);
			}
			return $ortak + array(
				'acf_fc_layout'  => 'tablo',
				'gorunum'        => 'karsilastirma',
				'sutunlar'       => $sut,
				'satirlar'       => $sat,
				'altyapi_etiket' => $alt_et,
				'altyapi'        => $alt,
				'not'            => ( $cn = $g( './/p[contains(@class,"catnote")]' ) ) ? $this->ic( $cn ) : '',
			);
		}

		if ( $g( './/*[contains(@class,"applead")]' ) ) {
			$one = array();
			foreach ( $x->query( './/*[contains(@class,"applead")]/article/a[contains(@class,"appnote")]', $s ) as $l ) {
				if ( preg_match( '#(?:uygulama|application)-([a-z-]+)\.html#', $l->getAttribute( 'href' ), $m ) ) {
					$id = $this->uygulama_bul( $m[1] );
					if ( $id ) {
						$one[] = $id;
					}
				}
			}
			$ihtiyac = array();
			foreach ( $x->query( './/*[contains(@class,"needrow")]/*[contains(@class,"need")]', $s ) as $n ) {
				$ihtiyac[] = array(
					'baslik' => $this->metin( $x->query( './b', $n )->item( 0 ) ),
					'metin'  => $this->ic( $x->query( './p', $n )->item( 0 ) ),
				);
			}
			$notlar = array();
			foreach ( $x->query( './/p[contains(@class,"appfoot")]', $s ) as $p ) {
				$notlar[] = array( 'metin' => $this->ic( $p ) );
			}
			return $ortak + array(
				'acf_fc_layout' => 'uygulama_alanlari',
				'ihtiyaclar'    => $ihtiyac,
				'one_cikanlar'  => $one,
				'digerleri'     => array(),
				'ara_etiket'    => '',
				'notlar'        => $notlar,
			);
		}

		if ( $g( './/*[contains(@class,"dgrid")]' ) ) {
			// Doküman listesi: kartlar doküman kayıtlarından (talep formuyla erişilenler, sıra kayıt sırası).
			$notlar = array();
			foreach ( $x->query( './/p[contains(@class,"catnote")]', $s ) as $n ) {
				$notlar[] = array( 'metin' => $this->ic( $n ) );
			}
			return $ortak + array(
				'acf_fc_layout' => 'dokuman_listesi',
				'dokumanlar'    => wp_list_pluck( servo_talep_dokumanlari( $this->dil ), 'id' ),
				'notlar'        => $notlar,
			);
		}

		if ( $g( './/*[contains(@class,"reqgrid")]' ) ) {
			$kartlar = array();
			foreach ( $x->query( './/article[contains(@class,"req")]', $s ) as $k ) {
				$a         = $x->query( './a', $k )->item( 0 );
				$kartlar[] = array(
					'baslik'       => $this->metin( $x->query( './h3', $k )->item( 0 ) ),
					'marka_yazisi' => 0,
					'metin'        => $this->ic( $x->query( './p', $k )->item( 0 ) ),
					'link'         => $a ? array(
						'url'    => $this->adres( $a->getAttribute( 'href' ) ),
						'title'  => $this->metin( $a ),
						'target' => '',
					) : '',
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'kart_izgarasi',
				'gorunum'       => 'talep',
				'kartlar'       => $kartlar,
				'fotograflar'   => array(),
			);
		}

		if ( $g( './/*[contains(@class,"cases")]' ) || $g( './/article[contains(@class,"case")]' ) ) {
			// Vaka listesi: vakalar kayıtlardan; sıra paketteki sıra.
			$vakalar = array();
			foreach ( $x->query( './/article[contains(@class,"case")]', $s ) as $a ) {
				$v = get_posts(
					array(
						'post_type'   => 'vaka',
						'post_status' => 'publish',
						'name'        => $a->getAttribute( 'id' ),
						'numberposts' => -1,
						'fields'      => 'ids',
					)
				);
				$v = servo_dil_filtre( $v, $this->dil );
				if ( $v ) {
					$vakalar[] = $v[0];
				}
			}
			return $ortak + array(
				'acf_fc_layout' => 'vaka_listesi',
				'gorunum'       => $g( './/*[contains(@class,"cases")]' ) ? 'iki_sutun' : 'liste',
				'vakalar'       => $vakalar,
				'not'           => $this->ic( $g( './/p[contains(@class,"anon")]' ) ),
			);
		}

		if ( $g( './/*[contains(@class,"svcgrid")]' ) ) {
			$kartlar = array();
			foreach ( $x->query( './/*[contains(@class,"svc")]', $s ) as $k ) {
				if ( 'svcgrid' === $k->parentNode->getAttribute( 'class' ) ) {
					$ikon = $x->query( './/*[contains(@class,"ic")]/svg', $k )->item( 0 );
					$a    = $x->query( './a', $k )->item( 0 );
					$kartlar[] = array(
						'ikon'   => $ikon ? $this->svg_adi( $ikon ) : '',
						'baslik' => $this->metin( $x->query( './h3', $k )->item( 0 ) ),
						'metin'  => $this->ic( $x->query( './p', $k )->item( 0 ) ),
						'link'   => $a ? array(
							'url'    => $this->adres( $a->getAttribute( 'href' ) ),
							'title'  => $this->metin( $a ),
							'target' => '',
						) : '',
					);
				}
			}
			return $ortak + array(
				'acf_fc_layout' => 'servis_kanallari',
				'kartlar'       => $kartlar,
			);
		}

		if ( $g( './/*[contains(@class,"steps2") or contains(@class,"pack")]' ) ) {
			$pack  = (bool) $g( './/*[contains(@class,"pack")]' );
			$ogeler = array();
			foreach ( $x->query( './/article', $s ) as $a ) {
				$ogeler[] = array(
					'numara'     => $this->metin( $x->query( './p[contains(@class,"n")]', $a )->item( 0 ) ),
					'alt_etiket' => '',
					'baslik'     => $this->metin( $x->query( './/h3', $a )->item( 0 ) ),
					'metin'      => $this->ic( $x->query( './/div/p', $a )->item( 0 ) ),
					'sure'       => '',
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'numarali_kartlar',
				'gorunum'       => $pack ? 'paket' : 'adim',
				'ogeler'        => $ogeler,
			);
		}

		if ( $g( './/*[contains(@class,"symlist")]' ) ) {
			$maddeler = array();
			foreach ( $x->query( './/*[contains(@class,"sym")]', $s ) as $m ) {
				if ( 'symlist' !== $m->parentNode->getAttribute( 'class' ) ) {
					continue;
				}
				$maddeler[] = array(
					'baslik' => $this->metin( $x->query( './b', $m )->item( 0 ) ),
					'metin'  => $this->ic( $x->query( './p', $m )->item( 0 ) ),
				);
			}
			$not = $g( './/p[contains(@class,"note") or contains(@class,"appfoot")]' );
			$rakamlar = array();
			$pf       = $g( './/*[contains(@class,"pfacts")]' );
			foreach ( $pf ? $x->query( './div', $pf ) : array() as $d ) {
				$b     = $x->query( './b', $d )->item( 0 );
				$kucuk = $b ? $x->query( './small', $b )->item( 0 ) : null;
				$deger = '';
				foreach ( $b ? $b->childNodes : array() as $c ) {
					if ( XML_TEXT_NODE === $c->nodeType ) {
						$deger .= $c->textContent;
					}
				}
				$rakamlar[] = array(
					'deger'    => trim( $deger ),
					'birim'    => $this->metin( $kucuk ),
					'aciklama' => $this->metin( $x->query( './span', $d )->item( 0 ) ),
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'madde_listesi',
				'gorunum'       => 'belirti',
				'maddeler'      => $maddeler,
				'rakamlar'      => $rakamlar,
				'not'           => $not ? $this->ic( $not ) : '',
			);
		}

		if ( $g( './/table[contains(@class,"meas")]' ) ) {
			$par = array();
			foreach ( $x->query( './/table[contains(@class,"meas")]//tr', $s ) as $tr ) {
				$par[] = array(
					'baslik'   => $this->metin( $x->query( './th', $tr )->item( 0 ) ),
					'aciklama' => $this->ic( $x->query( './td', $tr )->item( 0 ) ),
				);
			}
			return $ortak + array(
				'acf_fc_layout' => 'tablo',
				'gorunum'       => 'parametre',
				'parametreler'  => $par,
				'not'           => '',
			);
		}

		if ( $g( './/*[contains(@class,"pickbox")]' ) ) {
			$sec = array();
			foreach ( $x->query( './/*[contains(@class,"pick")]', $s ) as $p ) {
				if ( 'pickbox' !== $p->parentNode->getAttribute( 'class' ) ) {
					continue;
				}
				$a = $x->query( './a', $p )->item( 0 );
				$sec[] = array(
					'ust_etiket' => $this->metin( $x->query( './p[contains(@class,"t")]', $p )->item( 0 ) ),
					'baslik'     => $this->metin( $x->query( './h3', $p )->item( 0 ) ),
					'metin'      => $this->ic( $x->query( './p[not(@class)]', $p )->item( 0 ) ),
					'link'       => $a ? array(
						'url'    => $this->adres( $a->getAttribute( 'href' ) ),
						'title'  => $this->metin( $a ),
						'target' => '',
					) : '',
					'one_cikan'  => false !== strpos( ' ' . $p->getAttribute( 'class' ) . ' ', ' on ' ) ? 1 : 0,
				);
			}
			$cb = $g( './/*[contains(@class,"casebox")]' );
			return $ortak + array(
				'acf_fc_layout' => 'sistem_onerisi',
				'secenekler'    => $sec,
				'ornek_etiket'  => $cb ? $this->metin( $x->query( './p[contains(@class,"k")]', $cb )->item( 0 ) ) : '',
				'ornek_deger'   => $cb ? $this->metin( $x->query( './p[contains(@class,"v")]', $cb )->item( 0 ) ) : '',
				'ornek_metin'   => $cb ? $this->ic( $x->query( './p[not(@class)]', $cb )->item( 0 ) ) : '',
			);
		}

		if ( $g( './/*[contains(@class,"related-links")]' ) ) {
			$bag = array();
			foreach ( $x->query( './/*[contains(@class,"related-links")]/a', $s ) as $a ) {
				$href = $a->getAttribute( 'href' );
				if ( preg_match( '~^(?:basari-hikayeleri|success-stories)\.html#(.+)$~', $href, $m ) ) {
					$v     = get_posts(
						array(
							'post_type'   => 'vaka',
							'name'        => $m[1],
							'numberposts' => -1,
							'fields'      => 'ids',
						)
					);
					$v = servo_dil_filtre( $v, $this->dil );
					$bag[] = array(
						'tur'   => 'vaka',
						'vaka'  => $v ? $v[0] : '',
						'metin' => $this->metin( $a ),
					);
				} else {
					$bag[] = array(
						'tur'  => 'baglanti',
						'link' => array(
							'url'    => $this->adres( $href ),
							'title'  => $this->metin( $a ),
							'target' => '',
						),
					);
				}
			}
			return $ortak + array(
				'acf_fc_layout' => 'ilgili_baglantilar',
				'baglantilar'   => $bag,
			);
		}

		if ( $g( './/*[contains(@class,"dl")]' ) ) {
			$dosyalar = array();
			foreach ( $x->query( './/*[contains(@class,"dl")]/a', $s ) as $a ) {
				$href  = $a->getAttribute( 'href' );
				$sp    = $x->query( './span', $a )->item( 0 );
				$rozet = $this->metin( $sp );
				$bas   = '';
				foreach ( $a->childNodes as $c ) {
					if ( $c !== $sp ) {
						$bas .= $c->textContent;
					}
				}
				$satir = array(
					'baslik' => trim( preg_replace( '/\s+/u', ' ', $bas ) ),
					'rozet'  => $rozet,
				);

				if ( preg_match( '/\.(pdf|xlsx)$/i', $href ) && ! in_array( basename( $href ), self::KORUMALI, true ) ) {
					$satir['dosya'] = $this->medya( basename( $href ) );
					$satir['rozet'] = '';
				} elseif ( preg_match( '/\.(pdf|xlsx)$/i', $href ) ) {
					// Formla istenen doküman: doğrudan indirme yerine dokümanlar sayfasındaki talep formu.
					$satir['link']  = array(
						'url'    => $this->talep_adresi(),
						'title'  => $satir['baslik'],
						'target' => '',
					);
					$satir['rozet'] = 'en' === $this->dil ? 'REQUEST' : 'TALEP';
				} else {
					$satir['link'] = array(
						'url'    => $this->adres( $href ),
						'title'  => $satir['baslik'],
						'target' => '',
					);
				}
				$dosyalar[] = $satir;
			}
			$notlar = array();
			foreach ( $x->query( './/p[contains(@class,"catnote")]', $s ) as $n ) {
				$metin = $this->ic( $n );
				// İngilizce paket sayfasında, uygulama notlarının "Türkçe yayımlandığını" söyleyen bir cümle var; oysa
				// İngilizce notlar pakette ve listede yayında. Çelişen cümle alınmaz (rapora yazıldı).
				if ( false !== stripos( $metin, 'currently published in Turkish' ) ) {
					WP_CLI::warning( 'Atlandı (listelenen İngilizce PDF\'lerle çelişiyor): "' . mb_substr( $metin, 0, 60 ) . '…"' );
					continue;
				}
				$notlar[] = array( 'metin' => $metin );
			}
			return $ortak + array(
				'acf_fc_layout' => 'indirme_listesi',
				'dosyalar'      => $dosyalar,
				'notlar'        => $notlar,
			);
		}

		WP_CLI::warning( $dosya . ': tanınmayan bölüm atlandı (' . $s->getAttribute( 'class' ) . ').' );

		return null;
	}

	/**
	 * Formla istenen (doğrudan indirilemeyen) dokümanlar.
	 */
	const KORUMALI = array(
		'servomotion-core-precisor-veri-sayfasi.pdf',
		'servomotion-shuttle-precisor-veri-sayfasi.pdf',
		'servomotion-tuv-uygunluk-sertifikasi.pdf',
		'servomotion-core-precisor-datasheet.pdf',
		'servomotion-shuttle-precisor-datasheet.pdf',
	);

	/* ------------------------------------------------------------------
	 * Uygulama kartları
	 * ---------------------------------------------------------------- */

	/**
	 * Ana sayfa paketindeki uygulama kartlarını okur.
	 *
	 * @return array<string,array> slug => alanlar.
	 */
	protected function uygulama_kartlari() {
		$x   = $this->xp( 'index.html' );
		$out = array();

		foreach ( $x->query( '//section[@id="uygulama-alanlari"]//div[contains(@class,"applead")]/article' ) as $a ) {
			$link = $x->query( './a[contains(@class,"appnote")]', $a )->item( 0 );
			if ( ! $link || ! preg_match( '#^uygulama-([a-z-]+)\.html$#', $link->getAttribute( 'href' ), $m ) ) {
				continue;
			}
			$key = $x->query( './p[contains(@class,"appkey")]', $a )->item( 0 );
			$out[ $m[1] ] = array(
				'detay_sayfasi'    => 1,
				'kart_baslik'      => $this->metin( $x->query( './h3', $a )->item( 0 ) ),
				'kart_svg'         => 'uygulama-' . $m[1],
				'kart_metin'       => $this->ic( $x->query( './p[contains(@class,"appsym")]', $a )->item( 0 ) ),
				'kart_belirleyici' => $this->metin_em_sonrasi( $key ),
				'kart_ornek'       => $this->ic( $x->query( './p[contains(@class,"appscale")]', $a )->item( 0 ) ),
			);
		}

		return $out;
	}

	/**
	 * "Belirleyici olan" etiketinden sonraki metin.
	 *
	 * @param DOMNode|null $p Paragraf.
	 * @return string
	 */
	protected function metin_em_sonrasi( $p ) {
		$t = '';
		if ( $p ) {
			foreach ( $p->childNodes as $c ) {
				if ( XML_TEXT_NODE === $c->nodeType ) {
					$t .= $c->textContent;
				}
			}
		}

		return trim( preg_replace( '/\s+/u', ' ', $t ) );
	}

	/**
	 * Uygulama kaydı ID'si (slug veya eski slug ile).
	 *
	 * @param string $slug Slug.
	 * @return int
	 */
	protected function uygulama_bul( $slug ) {
		$adaylar = array( $slug );
		if ( 'film-folyo' === $slug ) {
			$adaylar[] = 'film';
		}
		foreach ( $adaylar as $ad ) {
			$p = get_posts(
				array(
					'post_type'   => 'uygulama',
					'post_status' => 'any',
					'name'        => $ad,
					'numberposts' => -1,
					'fields'      => 'ids',
				)
			);
			$p = servo_dil_filtre( $p, $this->dil );
			if ( $p ) {
				return (int) $p[0];
			}
		}

		return 0;
	}

	/* ------------------------------------------------------------------
	 * Yardımcılar
	 * ---------------------------------------------------------------- */

	/**
	 * Slug'ı verilen kaydı içerik dilinde bulur (Polylang aynı slug'ı iki dilde kullandırır).
	 *
	 * @param string $slug Slug.
	 * @param string $tur  Kayıt türü.
	 * @return WP_Post|null
	 */
	protected function sayfa_bul( $slug, $tur = 'page' ) {
		return servo_kayit_bul( $slug, $tur, $this->dil );
	}

	/**
	 * Paket dosyasını XPath olarak döndürür.
	 *
	 * @param string $dosya Dosya.
	 * @return DOMXPath
	 */
	protected function xp( $dosya ) {
		if ( ! isset( $this->doms[ $dosya ] ) ) {
			if ( ! is_readable( $this->dir . $dosya ) ) {
				WP_CLI::error( 'Paket dosyası okunamadı: ' . $dosya );
			}
			$dom = new DOMDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( '<?xml encoding="utf-8"?>' . file_get_contents( $this->dir . $dosya ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			libxml_clear_errors();
			$this->doms[ $dosya ] = new DOMXPath( $dom );
		}

		return $this->doms[ $dosya ];
	}

	/**
	 * Düğümün metni (boşluklar tek).
	 *
	 * @param DOMNode|null $node Düğüm.
	 * @return string
	 */
	protected function metin( $node ) {
		return $node ? trim( (string) preg_replace( '/\s+/u', ' ', $node->textContent ) ) : '';
	}

	/**
	 * Düğümün içeriği: satır içi HTML korunur, <br> satır sonu olur, marka işaretleri (bdi) atılır.
	 *
	 * @param DOMNode|null $node Düğüm.
	 * @return string
	 */
	protected function ic( $node ) {
		if ( ! $node ) {
			return '';
		}

		$h = '';
		foreach ( $node->childNodes as $c ) {
			$h .= $node->ownerDocument->saveHTML( $c );
		}

		$h = preg_replace( '#</?bdi[^>]*>#i', '', $h );
		$h = preg_replace( '#<span lang="en">(.*?)</span>#is', '$1', $h );
		$h = str_ireplace( array( '<strong>', '</strong>' ), array( '<b>', '</b>' ), $h );
		$h = preg_replace( '/\s+/u', ' ', $h );
		$h = preg_replace( '#\s*<br\s*/?>\s*#i', "\n", $h );
		$h = preg_replace_callback(
			'#href="([^"]*)"#',
			function ( $m ) {
				return 'href="' . esc_attr( $this->adres( html_entity_decode( $m[1] ) ) ) . '"';
			},
			$h
		);

		return trim( $this->kacis_koru( $h ) );
	}

	/**
	 * HTML kaçışlarını (&amp; vb.) olduğu gibi bırakır, sadece &nbsp; ve benzerlerini düz karaktere çevirir.
	 *
	 * @param string $h HTML.
	 * @return string
	 */
	protected function kacis_koru( $h ) {
		return str_replace( array( '&nbsp;', '&rsquo;', '&lsquo;', '&rdquo;', '&ldquo;' ), array( "\u{00A0}", '’', '‘', '”', '“' ), $h );
	}

	/**
	 * Paketteki bir adresi site adresine çevirir.
	 *
	 * @param string $href Paket adresi (ör. servis.html, basari-hikayeleri.html#lastik).
	 * @return string
	 */
	protected function adres( $href ) {
		$href = trim( $href );

		if ( '' === $href || '#' === $href[0] || preg_match( '#^(https?:|mailto:|tel:)#i', $href ) ) {
			return $href;
		}

		$parca = preg_split( '/(?=[?#])/', $href, 2 );
		$yol   = $parca[0];
		$ek    = isset( $parca[1] ) ? $parca[1] : '';

		if ( 'index.html' === $yol || '' === $yol ) {
			return home_url( '/' ) . $ek;
		}

		if ( preg_match( '/\.(pdf|xlsx|zip)$/i', $yol ) ) {
			// Formla istenen dokümanlar Medya'ya (herkese açık) alınmaz; talep formuna yönlendirilir.
			if ( in_array( basename( $yol ), self::KORUMALI, true ) ) {
				return $this->talep_adresi();
			}

			$url = $this->medya( basename( $yol ) );
			$u   = $url ? wp_get_attachment_url( $url ) : '';

			return $u ? $u : $href;
		}

		if ( ! preg_match( '/^(.+)\.html$/', $yol, $m ) ) {
			return $href;
		}

		$ad = $m[1];

		if ( 0 === strpos( $ad, 'uygulama-' ) ) {
			return home_url( '/uygulama/' . substr( $ad, 9 ) . '/' ) . $ek;
		}
		if ( 0 === strpos( $ad, 'rehber-' ) ) {
			return home_url( '/rehber/' . substr( $ad, 7 ) . '/' ) . $ek;
		}

		return home_url( '/' . $ad . '/' ) . $ek;
	}

	/**
	 * Medya Kitaplığı'na dosya alır (daha önce alındıysa mevcut ek).
	 *
	 * Ekler kaynak dosya adıyla (_servo_kaynak) eşleşir.
	 *
	 * @param string $dosya Paket kökündeki dosya yolu (ör. servis.pdf veya og/index.png).
	 * @param string $ad    Medya'daki dosya adı (boşsa paketteki ad).
	 * @return int Ek ID'si (kuru çalıştırmada veya hata varsa 0).
	 */
	protected function medya( $dosya, $ad = '' ) {
		$var = get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'meta_key'    => '_servo_kaynak', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'  => $dosya, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		if ( $var ) {
			return (int) $var[0];
		}

		$yol = $this->dir . $dosya;
		if ( $this->dry || ! is_readable( $yol ) ) {
			if ( ! is_readable( $yol ) ) {
				WP_CLI::warning( 'Medya dosyası yok: ' . $dosya );
			} else {
				WP_CLI::log( 'Medya: ' . $dosya . ' alınacak.' );
			}
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$ad  = '' !== $ad ? $ad : basename( $dosya );
		$tmp = wp_tempnam( $ad );
		copy( $yol, $tmp );
		$id = media_handle_sideload(
			array(
				'name'     => $ad,
				'tmp_name' => $tmp,
			),
			0,
			pathinfo( $ad, PATHINFO_FILENAME )
		);

		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( 'Medya alınamadı (' . $dosya . '): ' . $id->get_error_message() );
			return 0;
		}

		update_post_meta( $id, '_servo_kaynak', $dosya );
		WP_CLI::log( 'Medya: ' . $dosya . ' → ek #' . $id );

		return (int) $id;
	}

	/**
	 * Düğümün XPath'te var mı kontrolü.
	 *
	 * @param DOMXPath $x      XPath.
	 * @param DOMNode  $s      Bağlam.
	 * @param string   $sorgu  Sorgu.
	 * @return bool
	 */
	protected function var_mi( $x, $s, $sorgu ) {
		return $x->query( $sorgu, $s )->length > 0;
	}

	/**
	 * Bölüm arka planı.
	 *
	 * @param DOMNode $s Bölüm.
	 * @return string
	 */
	protected function arka_plan( $s ) {
		if ( false !== strpos( ' ' . $s->getAttribute( 'class' ) . ' ', ' dark ' ) ) {
			return 'koyu';
		}
		if ( preg_match( '/background:\s*(#fff|white)/i', $s->getAttribute( 'style' ) ) ) {
			return 'beyaz';
		}

		return 'varsayilan';
	}

	/**
	 * Satır içi SVG'nin HTML'i.
	 *
	 * @param DOMNode $svg SVG düğümü.
	 * @return string
	 */
	protected function svg_html( $svg ) {
		return trim( $svg->ownerDocument->saveHTML( $svg ) );
	}

	/**
	 * Satır içi SVG'nin tema dosyası adı (servis simgeleri).
	 *
	 * @param DOMNode $svg SVG düğümü.
	 * @return string
	 */
	protected function svg_adi_grup( $svg, $onek ) {
		$norm = preg_replace( '/\s+/', '', $this->svg_html( $svg ) );

		foreach ( (array) glob( get_template_directory() . '/assets/img/svg/' . $onek . '*.svg' ) as $dosya ) {
			if ( preg_replace( '/\s+/', '', (string) file_get_contents( $dosya ) ) === $norm ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				return basename( $dosya, '.svg' );
			}
		}

		return '';
	}

	/**
	 * Satır içi SVG'nin tema dosyası adı (servis simgeleri).
	 *
	 * @param DOMNode $svg SVG düğümü.
	 * @return string
	 */
	protected function svg_adi( $svg ) {
		$norm = preg_replace( '/\s+/', '', $this->svg_html( $svg ) );

		foreach ( (array) glob( get_template_directory() . '/assets/img/svg/servis-*.svg' ) as $dosya ) {
			if ( preg_replace( '/\s+/', '', (string) file_get_contents( $dosya ) ) === $norm ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				return basename( $dosya, '.svg' );
			}
		}

		return '';
	}

	/**
	 * Mühendislik değerlendirmesi kutusunun Gutenberg bloğu.
	 *
	 * @param DOMXPath $px   Yazı sayfası.
	 * @param DOMNode  $kutu Kutu.
	 * @return string
	 */
	protected function editoryal_blogu( $px, $kutu ) {
		$muhendislik = false !== strpos( ' ' . $kutu->getAttribute( 'class' ) . ' ', ' engineering ' );
		$stil        = $muhendislik ? 'muhendislik' : 'editoryal';
		$buton       = $px->query( './p/a[contains(@class,"btn")]', $kutu )->item( 0 );
		$buton_blok  = '';

		if ( $buton ) {
			$buton_blok = "\n\n" . '<!-- wp:paragraph {"className":"is-style-buton"} -->' . "\n"
				. '<p class="is-style-buton"><a href="' . esc_url( $this->adres( $buton->getAttribute( 'href' ) ) ) . '">' . esc_html( $this->metin( $buton ) ) . '</a></p>' . "\n"
				. '<!-- /wp:paragraph -->';
		}

		$etiket = $this->ic( $px->query( './p[contains(@class,"eyebrow")]', $kutu )->item( 0 ) );
		$baslik = $this->ic( $px->query( './h2', $kutu )->item( 0 ) );
		$li     = '';

		foreach ( $px->query( './ul/li', $kutu ) as $l ) {
			$li .= "<!-- wp:list-item -->\n<li>" . $this->ic( $l ) . "</li>\n<!-- /wp:list-item -->\n\n";
		}

		return '<!-- wp:group {"className":"is-style-' . $stil . '"} -->' . "\n"
			. '<div class="wp-block-group is-style-' . $stil . '"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->' . "\n"
			. '<p class="is-style-eyebrow">' . $etiket . '</p>' . "\n"
			. '<!-- /wp:paragraph -->' . "\n\n"
			. '<!-- wp:heading -->' . "\n"
			. '<h2 class="wp-block-heading">' . $baslik . '</h2>' . "\n"
			. '<!-- /wp:heading -->' . "\n\n"
			. '<!-- wp:list -->' . "\n"
			. '<ul class="wp-block-list">' . rtrim( $li ) . '</ul>' . "\n"
			. '<!-- /wp:list -->' . $buton_blok . '</div>' . "\n"
			. '<!-- /wp:group -->';
	}

	/**
	 * Site Ayarları alanını yazar (boş değer de yazılır).
	 *
	 * @param string $ad    Alan adı.
	 * @param mixed  $deger Değer.
	 */
	protected function ayar( $ad, $deger ) {
		$onceki = get_field( $ad, servo_options_id(), false );

		if ( $onceki === $deger || ( is_array( $deger ) && is_array( $onceki ) && $deger === $onceki ) ) {
			return;
		}

		WP_CLI::log( 'Site Ayarları: ' . $ad . ' güncellendi.' );
		if ( ! $this->dry ) {
			update_field( 'field_servo_' . $ad, $deger, servo_options_id() );
		}
	}

	/**
	 * Sayfa başlığı ve açıklaması (Rank Math alanları) paket sayfasından alınır.
	 *
	 * @param int    $id    Kayıt.
	 * @param string $dosya Paket dosyası.
	 */
	protected function seo( $id, $dosya ) {
		$x     = $this->xp( $dosya );
		$title = $this->metin( $x->query( '//head/title' )->item( 0 ) );
		$aciklama = $x->query( '//meta[@name="description"]' )->item( 0 );
		$aciklama = $aciklama ? trim( $aciklama->getAttribute( 'content' ) ) : '';

		if ( $this->dry ) {
			return;
		}

		if ( $title ) {
			update_post_meta( $id, 'rank_math_title', $title );
		}
		if ( $aciklama ) {
			update_post_meta( $id, 'rank_math_description', $aciklama );
		}
	}

	/* ------------------------------------------------------------------
	 * Blok satırı okuma / yazma
	 * ---------------------------------------------------------------- */

	/**
	 * Yazının bloklarını alan adlarıyla (üst düzey) okur; iç içe değerler alan anahtarlı kalır.
	 *
	 * @param int $id Kayıt.
	 * @return array
	 */
	protected function satirlar( $id ) {
		$ham = get_field( 'bloklar', $id, false );
		$out = array();

		foreach ( (array) $ham as $s ) {
			$L    = $s['acf_fc_layout'];
			$onek = 'field_servo_blk_' . $L . '_';
			$yeni = array( 'acf_fc_layout' => $L );
			foreach ( $s as $k => $v ) {
				if ( 'acf_fc_layout' === $k ) {
					continue;
				}
				$ad          = 0 === strpos( $k, $onek ) ? substr( $k, strlen( $onek ) ) : $k;
				$yeni[ $ad ] = $v;
			}
			$out[] = $yeni;
		}

		return $out;
	}

	/**
	 * Alt satırdaki (anahtarlı) bir alanı okur.
	 *
	 * @param array  $satir Alt satır.
	 * @param string $ad    Alan adı.
	 * @return mixed
	 */
	protected function alt( $satir, $ad ) {
		foreach ( (array) $satir as $k => $v ) {
			if ( $k === $ad || substr( (string) $k, -strlen( '_' . $ad ) ) === '_' . $ad ) {
				return $v;
			}
		}

		return null;
	}

	/**
	 * Alt satırdaki bir alanı yazar.
	 *
	 * @param array  $satir Alt satır.
	 * @param string $ad    Alan adı.
	 * @param mixed  $deger Değer.
	 * @return array
	 */
	protected function alt_yaz( $satir, $ad, $deger ) {
		foreach ( (array) $satir as $k => $v ) {
			if ( $k === $ad || substr( (string) $k, -strlen( '_' . $ad ) ) === '_' . $ad ) {
				$satir[ $k ] = $deger;
				return $satir;
			}
		}
		$satir[ $ad ] = $deger;

		return $satir;
	}

	/**
	 * Blokları yazar ve arama dizinini tazeler.
	 *
	 * @param int   $id       Kayıt.
	 * @param array $satirlar Satırlar.
	 */
	protected function satirlari_yaz( $id, $satirlar ) {
		if ( $this->dry ) {
			return;
		}

		update_field( 'bloklar', $satirlar, $id );

		if ( function_exists( 'servo_arama_dizinle' ) && 'page' === get_post_type( $id ) ) {
			servo_arama_dizinle( $id );
		}
	}

	/**
	 * Menü öğesinin başlığını değiştirir.
	 *
	 * @param int      $menu_id Menü.
	 * @param stdClass $it      Öğe.
	 * @param string   $ad      Yeni başlık.
	 */
	protected function menu_ad( $menu_id, $it, $ad ) {
		if ( $it->title === $ad ) {
			return;
		}
		WP_CLI::log( 'Menü: "' . $it->title . '" → "' . $ad . '"' );
		if ( $this->dry ) {
			return;
		}
		wp_update_nav_menu_item(
			$menu_id,
			$it->ID,
			array(
				'menu-item-title'     => $ad,
				'menu-item-type'      => $it->type,
				'menu-item-object'    => $it->object,
				'menu-item-object-id' => $it->object_id,
				'menu-item-parent-id' => $it->menu_item_parent,
				'menu-item-position'  => $it->menu_order,
				'menu-item-url'       => 'custom' === $it->type ? $it->url : '',
				'menu-item-status'    => 'publish',
			)
		);
	}
}
