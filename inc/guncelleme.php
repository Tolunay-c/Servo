<?php
/**
 * Veritabanı içeriği güncellemeleri (bir kez çalışan, sürüm numaralı).
 *
 * Canlıda SSH/WP-CLI olmadığı için içerik düzeltmeleri tema yüklendikten sonra
 * ilk yönetici ziyaretinde (admin_init) çalışır. Uygulanan sürüm `servo_db_surum`
 * seçeneğinde tutulur; her sürüm bir kez çalışır ve her adım tekrar çalıştırılsa
 * da kopya/çift değişiklik üretmez (eski metin yoksa dokunmaz, boş alanı doldurur).
 * Yeni düzeltme için `SERVO_DB_SURUM` artırılır ve `servo_guncelleme_adimlari()`
 * listesine yeni sürüm eklenir. Yerelde: `wp servo guncelle [--zorla]`; panelde: Araçlar >
 * Veritabanı güncellemeleri (sürüm başına uygulandı / atlandı / hata sonucu gösterilir).
 *
 * Sürüm 1 (denetim V1.0, 15 Ekim 2026): iletişim sayfası veri akışı cümlesi (MAJ-02),
 * EN Core/Shuttle tablo notları (MAJ-05), EN rehberdeki kırık çapa (MIN-02),
 * EN sertifika bağlantısı (MIN-04), ana sayfa veri sayfası bağlantıları (MIN-05),
 * "strok'u" yazımı ve EN logo alt yazısı (MIN-07).
 *
 * Sürüm 2 (EN içerik eksikleri, 16 Ekim 2026): EN Shuttle "üç raylı mimari" bölüm açıklaması ve
 * ilk kart (kartlar bir sıra kaymıştı), EN Core "çalışma prensibi" açıklaması (referans paketin
 * EN dosyalarından).
 *
 * Sürüm 3 (EN ana sayfa hero, 16 Ekim 2026): EN ana sayfanın ilk bloğu `sayfa_hero` yerine TR ile
 * aynı `ana_hero` (aynı görsel, oran ve yerleşim) olur; metinler İngilizcedir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

const SERVO_DB_SURUM = 3;

/**
 * Sürüm => adım işlevi. Her işlev log satırlarından oluşan dizi döndürür.
 *
 * @return array<int,callable>
 */
function servo_guncelleme_adimlari() {
	$adimlar = array(
		1 => 'servo_guncelle_1',
		2 => 'servo_guncelle_2',
		3 => 'servo_guncelle_3',
	);
	return apply_filters( 'servo_guncelleme_adimlari', $adimlar );
}

/**
 * Bekleyen güncellemeleri sırayla çalıştırır ve her sürümün sonucunu döndürür.
 *
 * Durumlar: `uygulandi` (değişiklik yapıldı), `atlandi` (zaten uygulanmış ya da değiştirilecek bir
 * şey yok), `hata` (adım istisna fırlattı; sürüm işaretlenmez ve sonraki sürümler çalışmaz, `bekliyor`).
 *
 * @param bool $zorla Uygulanmış sürümleri de yeniden çalıştırır (adımlar tekrar güvenlidir).
 * @return array<int,array{surum:int,durum:string,log:string[],mesaj:string}> Sürüm => sonuç.
 */
function servo_guncelle_sonuclari( $zorla = false ) {
	$sonuc  = array();
	$mevcut = (int) get_option( 'servo_db_surum', 0 );
	$kilit  = 'servo_guncelleme_kilit';

	if ( get_transient( $kilit ) ) {
		return array(
			0 => array(
				'surum' => 0,
				'durum' => 'hata',
				'log'   => array(),
				'mesaj' => 'Güncelleme başka bir istekte çalışıyor (kilit 5 dakika içinde kendiliğinden kalkar).',
			),
		);
	}
	set_transient( $kilit, 1, 5 * MINUTE_IN_SECONDS );

	$durdu = false;
	foreach ( servo_guncelleme_adimlari() as $surum => $islev ) {
		if ( $durdu ) {
			$sonuc[ $surum ] = array(
				'surum' => $surum,
				'durum' => 'bekliyor',
				'log'   => array(),
				'mesaj' => 'Önceki sürümdeki hata nedeniyle çalıştırılmadı.',
			);
			continue;
		}

		if ( $surum <= $mevcut && ! $zorla ) {
			$sonuc[ $surum ] = array(
				'surum' => $surum,
				'durum' => 'atlandi',
				'log'   => array(),
				'mesaj' => 'Daha önce uygulanmış.',
			);
			continue;
		}

		try {
			// "0 alan" satırları değişiklik sayılmaz.
			$log = array_values( array_filter( (array) call_user_func( $islev ), static function ( $satir ) {
				return ! preg_match( '/: 0 alan$/', (string) $satir );
			} ) );
		} catch ( \Throwable $e ) {
			$sonuc[ $surum ] = array(
				'surum' => $surum,
				'durum' => 'hata',
				'log'   => array(),
				'mesaj' => $e->getMessage() . ' (' . basename( $e->getFile() ) . ':' . $e->getLine() . ')',
			);
			$durdu           = true;
			continue;
		}

		$sonuc[ $surum ] = array(
			'surum' => $surum,
			'durum' => $log ? 'uygulandi' : 'atlandi',
			'log'   => $log,
			'mesaj' => $log ? '' : 'Değiştirilecek bir şey bulunmadı.',
		);

		if ( $surum > $mevcut ) {
			update_option( 'servo_db_surum', $surum, true );
			$mevcut = $surum;
		}
	}

	delete_transient( $kilit );
	update_option(
		'servo_db_son_sonuc',
		array(
			'zaman'  => time(),
			'sonuc'  => $sonuc,
		),
		false
	);

	return $sonuc;
}

/**
 * Bekleyen güncellemeleri sırayla çalıştırır (WP-CLI ve eski çağrılar için düz log).
 *
 * @param bool $zorla Uygulanmış sürümleri de yeniden çalıştırır.
 * @return string[] Log satırları.
 */
function servo_guncelle_calistir( $zorla = false ) {
	$log = array();

	foreach ( servo_guncelle_sonuclari( $zorla ) as $r ) {
		$log = array_merge( $log, $r['log'] );
		if ( 'hata' === $r['durum'] ) {
			$log[] = sprintf( 'Sürüm %d HATA: %s', $r['surum'], $r['mesaj'] );
		}
	}

	return $log;
}

/**
 * İlk yönetici ziyaretinde bekleyen güncellemeleri çalıştırır.
 */
function servo_guncelle_admin() {
	if ( wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( (int) get_option( 'servo_db_surum', 0 ) >= SERVO_DB_SURUM ) {
		return;
	}

	servo_guncelle_sonuclari();
}
add_action( 'admin_init', 'servo_guncelle_admin' );

/**
 * Araçlar > Veritabanı güncellemeleri sayfasını ekler.
 */
function servo_guncelle_menu() {
	add_management_page(
		__( 'Veritabanı güncellemeleri', 'servomotion' ),
		__( 'Veritabanı güncellemeleri', 'servomotion' ),
		'manage_options',
		'servo-db-guncellemeleri',
		'servo_guncelle_sayfa'
	);
}
add_action( 'admin_menu', 'servo_guncelle_menu' );

/**
 * Durum kodunun görünen adı.
 *
 * @param string $durum Durum kodu.
 * @return string
 */
function servo_guncelle_durum_adi( $durum ) {
	$adlar = array(
		'uygulandi' => 'Uygulandı',
		'atlandi'   => 'Atlandı',
		'hata'      => 'Hata',
		'bekliyor'  => 'Bekliyor',
	);

	return isset( $adlar[ $durum ] ) ? $adlar[ $durum ] : $durum;
}

/**
 * Sonuç tablosunu basar.
 *
 * @param array $sonuc Sürüm => sonuç.
 */
function servo_guncelle_tablo( $sonuc ) {
	echo '<table class="widefat striped" style="max-width:900px"><thead><tr><th>Sürüm</th><th>Sonuç</th><th>Ayrıntı</th></tr></thead><tbody>';
	foreach ( $sonuc as $r ) {
		$renk = array(
			'uygulandi' => '#00a32a',
			'hata'      => '#d63638',
			'bekliyor'  => '#996800',
		);
		$stil = isset( $renk[ $r['durum'] ] ) ? ' style="color:' . esc_attr( $renk[ $r['durum'] ] ) . ';font-weight:600"' : '';
		$ayr  = $r['log'] ? implode( '<br>', array_map( 'esc_html', $r['log'] ) ) : esc_html( $r['mesaj'] );
		echo '<tr><td>' . (int) $r['surum'] . '</td><td' . $stil . '>' . esc_html( servo_guncelle_durum_adi( $r['durum'] ) ) . '</td><td>' . $ayr . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</tbody></table>';
}

/**
 * Araçlar > Veritabanı güncellemeleri sayfası.
 */
function servo_guncelle_sayfa() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'servomotion' ) );
	}

	$yeni = null;

	if ( isset( $_POST['servo_guncelle'] ) && check_admin_referer( 'servo_guncelle_calistir' ) ) {
		if ( ! empty( $_POST['kilidi_kaldir'] ) ) {
			delete_transient( 'servo_guncelleme_kilit' );
		}
		$yeni = servo_guncelle_sonuclari( ! empty( $_POST['zorla'] ) );
	}

	$mevcut  = (int) get_option( 'servo_db_surum', 0 );
	$bekleyen = array_filter(
		array_keys( servo_guncelleme_adimlari() ),
		static function ( $s ) use ( $mevcut ) {
			return $s > $mevcut;
		}
	);

	echo '<div class="wrap"><h1>Veritabanı güncellemeleri</h1>';
	echo '<p>Uygulanan sürüm: <strong>' . (int) $mevcut . '</strong> / son sürüm: <strong>' . (int) SERVO_DB_SURUM . '</strong>';
	echo $bekleyen ? ' &mdash; bekleyen: ' . esc_html( implode( ', ', $bekleyen ) ) : ' &mdash; bekleyen güncelleme yok'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</p>';

	if ( get_transient( 'servo_guncelleme_kilit' ) ) {
		echo '<div class="notice notice-warning inline"><p>Bir güncelleme kilidi açık (başka bir istek çalışıyor ya da yarım kalmış). Kilit en geç 5 dakikada kalkar; hemen kaldırmak için aşağıdaki kutuyu işaretleyin.</p></div>';
	}

	echo '<form method="post">';
	wp_nonce_field( 'servo_guncelle_calistir' );
	echo '<p><label><input type="checkbox" name="zorla" value="1"> Uygulanmış sürümleri de yeniden çalıştır (adımlar tekrar güvenlidir, kopya üretmez)</label><br>';
	echo '<label><input type="checkbox" name="kilidi_kaldir" value="1"> Açık kilidi kaldır</label></p>';
	echo '<p><button type="submit" name="servo_guncelle" value="1" class="button button-primary">Bekleyen güncellemeleri çalıştır</button></p></form>';

	if ( null !== $yeni ) {
		echo '<h2>Bu çalıştırmanın sonucu</h2>';
		servo_guncelle_tablo( $yeni );
	} else {
		$son = get_option( 'servo_db_son_sonuc' );
		if ( is_array( $son ) && ! empty( $son['sonuc'] ) ) {
			echo '<h2>Son çalıştırma (' . esc_html( wp_date( 'd.m.Y H:i', (int) $son['zaman'] ) ) . ')</h2>';
			servo_guncelle_tablo( $son['sonuc'] );
		}
	}

	echo '</div>';
}

/**
 * Metni (iç içe dizilerde de) değiştirir; serileştirilmiş veriyi bozmaz.
 *
 * @param mixed  $deger Değer.
 * @param string $ara   Aranan metin.
 * @param string $yeni  Yeni metin.
 * @return mixed
 */
function servo_guncelle_degistir( $deger, $ara, $yeni ) {
	if ( is_string( $deger ) ) {
		return str_replace( $ara, $yeni, $deger );
	}

	if ( is_array( $deger ) ) {
		foreach ( $deger as $anahtar => $alt ) {
			$deger[ $anahtar ] = servo_guncelle_degistir( $alt, $ara, $yeni );
		}
	}

	return $deger;
}

/**
 * Bir yazı/sayfanın meta alanlarında metin değiştirir.
 *
 * @param int      $id        Yazı ID'si.
 * @param string   $anahtar   SQL LIKE kalıbı (meta anahtarı).
 * @param string[] $ara_yeni  Aranan => yeni metin.
 * @return int Değişen alan sayısı.
 */
function servo_guncelle_meta_metin( $id, $anahtar, $ara_yeni ) {
	global $wpdb;

	$satirlar = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s", $id, $anahtar )
	);
	$adet = 0;

	foreach ( $satirlar as $satir ) {
		$eski = maybe_unserialize( $satir->meta_value );
		$yeni = $eski;
		foreach ( $ara_yeni as $ara => $degisen ) {
			$yeni = servo_guncelle_degistir( $yeni, $ara, $degisen );
		}
		if ( $yeni !== $eski ) {
			update_post_meta( $id, $satir->meta_key, wp_slash( $yeni ) );
			++$adet;
		}
	}

	return $adet;
}

/**
 * Sürüm 1.
 *
 * @return string[]
 */
function servo_guncelle_1() {
	global $wpdb;

	$log   = array();
	$dokun = array();

	// MAJ-02: iletişim sayfasındaki veri akışı cümlesi (KVKK sayfasına dokunulmaz).
	$cumleler = array(
		'tr' => array(
			'slug' => 'iletisim',
			'ara'  => 'Hiçbir bilgi tarayıcınızdan çıkmaz; gönderme kararını siz verirsiniz.',
			'yeni' => 'Özet, siz “Gönder”e basana kadar tarayıcınızda kalır; gönderdiğinizde e-postayla ekibimize iletilir. Ayrıntılar KVKK ve Çerezler sayfasındadır.',
		),
		'en' => array(
			'slug' => 'contact',
			'ara'  => 'Nothing leaves your browser; you decide when to send it.',
			'yeni' => 'The summary stays in your browser until you press “Send”; sending forwards it to our team by email. Details are on the Privacy and Cookies page.',
		),
	);
	foreach ( $cumleler as $dil => $c ) {
		$sayfa = servo_kayit_bul( $c['slug'], 'page', $dil );
		if ( $sayfa ) {
			$adet = servo_guncelle_meta_metin( $sayfa->ID, 'bloklar\_%\_aciklama', array( $c['ara'] => $c['yeni'] ) );
			$log[] = "MAJ-02 {$dil}: {$adet} alan";
			$dokun[ $sayfa->ID ] = true;
		}
	}

	// MAJ-05: EN Core ve Shuttle teknik tablo altı koşul notları (referans paketin EN dosyalarından).
	$notlar = array(
		'core-precisor'    => array( 'field_servo_core_teknik_not', 'Values apply to example configurations. Final performance and connectivity are evaluated together with motion load, working distance and mounting conditions.' ),
		'shuttle-precisor' => array( 'field_servo_shuttle_teknik_not', 'Values apply to example configurations; carriage count and rail assembly length are determined during the site survey.' ),
	);
	foreach ( $notlar as $slug => $n ) {
		$sayfa = servo_kayit_bul( $slug, 'page', 'en' );
		if ( $sayfa && '' === trim( (string) get_post_meta( $sayfa->ID, 'teknik_not', true ) ) ) {
			update_post_meta( $sayfa->ID, 'teknik_not', $n[1] );
			update_post_meta( $sayfa->ID, '_teknik_not', $n[0] );
			$log[]               = "MAJ-05 {$slug}: not eklendi";
			$dokun[ $sayfa->ID ] = true;
		}
	}

	// MIN-02: EN "sistem seçimi" rehberinde ana sayfa çapası /en/#sistemler → /en/home/#systems.
	$yazi = servo_kayit_bul( 'system-selection', 'post', 'en' );
	if ( $yazi ) {
		$icerik = preg_replace( '#href="([^"]*?)/en/\#sistemler"#', 'href="$1/en/home/#systems"', $yazi->post_content, -1, $say );
		if ( $say ) {
			wp_update_post(
				array(
					'ID'           => $yazi->ID,
					'post_content' => wp_slash( $icerik ),
				)
			);
			$log[] = 'MIN-02: çapa düzeltildi';
		}
	}

	// MIN-04: EN sertifika bağlantısı talep sayfasına gider; metin "Request", download özniteliği kalkar.
	$anahtar = 'options_en_guvenlik_maddeler_3_metin';
	$metin   = get_option( $anahtar );
	if ( is_string( $metin ) && false !== strpos( $metin, 'Download the certificate (PDF)' ) ) {
		$metin = preg_replace( '#(<a\s[^>]*?)\sdownload(?=[\s>])#', '$1', $metin );
		$metin = str_replace( 'Download the certificate (PDF)', 'Request the certificate', $metin );
		update_option( $anahtar, $metin );
		$log[] = 'MIN-04: sertifika bağlantısı';
	}

	// MIN-05: ana sayfa "veri sayfalarını indirin" bağlantıları talep akışını anlatır.
	$basliklar = array(
		'Veri sayfalarını indirin' => 'Veri sayfası talep edin',
		'Download the data sheets' => 'Request data sheets',
	);
	foreach ( array( array( 'ana-sayfa', 'tr' ), array( 'home', 'en' ) ) as $s ) {
		$sayfa = servo_kayit_bul( $s[0], 'page', $s[1] );
		if ( $sayfa ) {
			$adet = servo_guncelle_meta_metin( $sayfa->ID, 'bloklar\_%\_baglantilar\_%\_link', $basliklar );
			$log[] = "MIN-05 {$s[1]}: {$adet} alan";
			$dokun[ $sayfa->ID ] = true;
		}
	}

	// MIN-07: "çalışma strok'u" → "çalışma stroku" (yazılar ve meta alanları).
	$strok = array(
		"strok'u" => 'stroku',
		'strok’u' => 'stroku',
	);
	foreach ( array( "strok'u", 'strok’u' ) as $kalip ) {
		$like  = '%' . $wpdb->esc_like( $kalip ) . '%';
		$yazilar = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content, post_title, post_excerpt FROM {$wpdb->posts} WHERE post_type <> 'revision' AND ( post_content LIKE %s OR post_title LIKE %s OR post_excerpt LIKE %s )", $like, $like, $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( $yazilar as $y ) {
			wp_update_post(
				array(
					'ID'           => $y->ID,
					'post_content' => wp_slash( servo_guncelle_degistir( $y->post_content, $kalip, 'stroku' ) ),
					'post_title'   => wp_slash( servo_guncelle_degistir( $y->post_title, $kalip, 'stroku' ) ),
					'post_excerpt' => wp_slash( servo_guncelle_degistir( $y->post_excerpt, $kalip, 'stroku' ) ),
				)
			);
			$log[] = "MIN-07: yazı {$y->ID}";
		}

		$idler = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE %s AND meta_key <> %s", $like, '_servo_arama_metni' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( $idler as $id ) {
			$adet = servo_guncelle_meta_metin( (int) $id, '%', $strok );
			$log[] = "MIN-07: meta {$id} ({$adet})";
			$dokun[ (int) $id ] = true;
		}
	}

	// MIN-07: EN arayüzde Türkçe marka alt yazısı kalmasın (logo alt yazısı).
	if ( '' === trim( (string) get_option( 'options_en_logo_alt_yazi', '' ) ) ) {
		update_option( 'options_en_logo_alt_yazi', 'Laser Positioning' );
		update_option( '_options_en_logo_alt_yazi', 'field_servo_logo_alt_yazi' );
		$log[] = 'MIN-07: EN logo alt yazısı';
	}

	// Arama dizinini tazele (düzenlenen sayfalar).
	if ( $dokun && function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 2: EN Shuttle ve Core bölümlerinde boş/etiketle aynı kalan alanlar.
 *
 * @return string[]
 */
function servo_guncelle_2() {
	$log   = array();
	$dokun = false;

	$shuttle = servo_kayit_bul( 'shuttle-precisor', 'page', 'en' );
	if ( $shuttle ) {
		$id = $shuttle->ID;

		$aciklama = trim( (string) get_post_meta( $id, 'mimari_aciklama', true ) );
		if ( '' === $aciklama || 'ARCHITECTURE' === $aciklama ) {
			update_post_meta( $id, 'mimari_aciklama', 'Power transmission, carriage guidance and motion drive sit in one rail assembly. One servo axis performs all movement instead of a motor, drive and cable per laser: fewer components and a simpler maintenance routine. The number of servo axes remains unchanged as laser carriages are added, helping limit system complexity as the application scales.' );
			update_post_meta( $id, '_mimari_aciklama', 'field_servo_shuttle_mimari_aciklama' );
			$log[]  = 'EN Shuttle: mimari açıklaması';
			$dokun = true;
		}

		// Kartlar bir sıra kaymıştı (0. kart boş, alt ray yok); üç kart baştan yazılır.
		if ( '' === trim( (string) get_post_meta( $id, 'mimari_kartlar_0_baslik', true ) ) ) {
			$kartlar = array(
				array( 'UPPER RAIL', 'Power supply', 'Lasers are powered continuously at any position along the rail, so moving cables and energy chains are not required.' ),
				array( 'MIDDLE RAIL', 'Laser carriages', 'Each laser travels in its own carriage. When it reaches its position it <b>locks on the rail</b> and stays there independently of the shuttle.' ),
				array( 'LOWER RAIL', 'Servo shuttle', 'The only moving axis of the system. It carries the carriages to their recipe positions in sequence and withdraws when finished.' ),
			);
			foreach ( $kartlar as $i => $k ) {
				update_post_meta( $id, "mimari_kartlar_{$i}_numara", $k[0] );
				update_post_meta( $id, "mimari_kartlar_{$i}_baslik", $k[1] );
				update_post_meta( $id, "mimari_kartlar_{$i}_metin", $k[2] );
				update_post_meta( $id, "_mimari_kartlar_{$i}_numara", 'field_servo_shuttle__mimari_kartlar_numara' );
				update_post_meta( $id, "_mimari_kartlar_{$i}_baslik", 'field_servo_shuttle__mimari_kartlar_baslik' );
				update_post_meta( $id, "_mimari_kartlar_{$i}_metin", 'field_servo_shuttle__mimari_kartlar_metin' );
			}
			update_post_meta( $id, 'mimari_kartlar', 3 );
			update_post_meta( $id, '_mimari_kartlar', 'field_servo_shuttle_mimari_kartlar' );
			$log[]  = 'EN Shuttle: mimari kartları';
			$dokun = true;
		}
	}

	$core = servo_kayit_bul( 'core-precisor', 'page', 'en' );
	if ( $core ) {
		$aciklama = trim( (string) get_post_meta( $core->ID, 'calisma_aciklama', true ) );
		if ( '' === $aciklama || 'WORKING PRINCIPLE' === $aciklama ) {
			update_post_meta( $core->ID, 'calisma_aciklama', 'PLC-controlled servo axes carry the laser modules to the defined targets. The projected laser reference lines guide material placement or machine setting, while the operator carries out the physical alignment. The position is not a one-off setting made at installation — it is recipe data.' );
			update_post_meta( $core->ID, '_calisma_aciklama', 'field_servo_core_calisma_aciklama' );
			$log[]  = 'EN Core: çalışma açıklaması';
			$dokun = true;
		}
	}

	if ( $dokun && function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 3: EN ana sayfa hero'su TR ana sayfadaki `ana_hero` bloğuna çevrilir.
 *
 * @return string[]
 */
function servo_guncelle_3() {
	global $wpdb;

	$sayfa = servo_kayit_bul( 'home', 'page', 'en' );
	$bloklar = $sayfa ? get_post_meta( $sayfa->ID, 'bloklar', true ) : array();

	if ( ! $sayfa || ! is_array( $bloklar ) || ! $bloklar ) {
		throw new \RuntimeException( 'EN ana sayfa (home) ya da blok listesi bulunamadı.' );
	}

	if ( 'ana_hero' === $bloklar[0] ) {
		return array();
	}

	$id     = $sayfa->ID;
	$gorsel = (int) get_post_meta( $id, 'bloklar_0_gorsel', true );
	$iletis = get_post_meta( $id, 'bloklar_0_butonlar_1_link', true );

	// Eski sayfa_hero alanlarını sil.
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND ( meta_key LIKE %s OR meta_key LIKE %s )", $id, $wpdb->esc_like( 'bloklar_0_' ) . '%', $wpdb->esc_like( '_bloklar_0_' ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	wp_cache_delete( $id, 'post_meta' ); // Doğrudan SQL sonrası meta önbelleği (aynı değerli alanlar silinmiş kalmasın).

	$a = 'field_servo_blk_ana_hero';
	$m = array(
		'etiket'        => array( 'SERVO-CONTROLLED LASER POSITIONING', "{$a}_etiket" ),
		'baslik'        => array( 'ACCURATE POSITIONING.', "{$a}_baslik" ),
		'vurgu'         => array( 'FASTER CHANGEOVERS.', "{$a}_vurgu" ),
		'kapanis'       => array( "Steer your production with ServoMotion\nlaser positioning systems.", "{$a}_kapanis" ),
		'gorsel'        => array( $gorsel ? $gorsel : 78, "{$a}_gorsel" ),
		'gorsel_baslik' => array( 'CORE PRECISOR', "{$a}_gorsel_baslik" ),
		'gorsel_metin'  => array( 'Compact housing · Servo-controlled motion', "{$a}_gorsel_metin" ),
		'capa'          => array( '', "{$a}_capa" ),
	);
	foreach ( $m as $ad => $d ) {
		update_post_meta( $id, "bloklar_0_{$ad}", $d[0] );
		update_post_meta( $id, "_bloklar_0_{$ad}", $d[1] );
	}

	$mesaj = array(
		array( 'Precise alignment.', 0 ),
		array( 'Fast product changeovers.', 0 ),
		array( 'Efficient production.', 1 ),
	);
	foreach ( $mesaj as $i => $r ) {
		update_post_meta( $id, "bloklar_0_mesaj_{$i}_metin", $r[0] );
		update_post_meta( $id, "_bloklar_0_mesaj_{$i}_metin", "{$a}__mesaj_metin" );
		update_post_meta( $id, "bloklar_0_mesaj_{$i}_vurgulu", $r[1] );
		update_post_meta( $id, "_bloklar_0_mesaj_{$i}_vurgulu", "{$a}__mesaj_vurgulu" );
	}
	update_post_meta( $id, 'bloklar_0_mesaj', 3 );
	update_post_meta( $id, '_bloklar_0_mesaj', "{$a}_mesaj" );

	$butonlar = array(
		array( array( 'url' => '#systems', 'title' => 'COMPARE THE SYSTEMS', 'target' => '' ), 1 ),
		array( is_array( $iletis ) && ! empty( $iletis['url'] ) ? $iletis : array( 'url' => home_url( '/en/contact/' ), 'title' => 'REQUEST A QUOTE', 'target' => '' ), 0 ),
	);
	foreach ( $butonlar as $i => $b ) {
		update_post_meta( $id, "bloklar_0_butonlar_{$i}_link", $b[0] );
		update_post_meta( $id, "_bloklar_0_butonlar_{$i}_link", "{$a}__butonlar_link" );
		update_post_meta( $id, "bloklar_0_butonlar_{$i}_birincil", $b[1] );
		update_post_meta( $id, "_bloklar_0_butonlar_{$i}_birincil", "{$a}__butonlar_birincil" );
	}
	update_post_meta( $id, 'bloklar_0_butonlar', 2 );
	update_post_meta( $id, '_bloklar_0_butonlar', "{$a}_butonlar" );

	$serit = array(
		array( 'PLC-BASED CONTROL', 'Recipe and motion management', 'PROFINET / EtherNet/IP communication' ),
		array( 'SERVO MOTION ARCHITECTURE', "CORE: independent and symmetrical motion\nSHUTTLE: sequential placement with a single shuttle", 'Position control with encoder feedback' ),
		array( 'RED / GREEN LASER', 'Adjustable focus · Line reference', '12–24 VDC supply · Standard M12 connector' ),
		array( 'APPLICATION-SPECIFIC MECHANICS', "CORE: compact, enclosed housing\nSHUTTLE: three-rail, locking carriage design", 'Sized to the working area' ),
	);
	foreach ( $serit as $i => $r ) {
		foreach ( array( 'baslik', 'metin', 'alt_metin' ) as $j => $f ) {
			update_post_meta( $id, "bloklar_0_serit_{$i}_{$f}", $r[ $j ] );
			update_post_meta( $id, "_bloklar_0_serit_{$i}_{$f}", "{$a}__serit_{$f}" );
		}
	}
	update_post_meta( $id, 'bloklar_0_serit', 4 );
	update_post_meta( $id, '_bloklar_0_serit', "{$a}_serit" );

	$bloklar[0] = 'ana_hero';
	update_post_meta( $id, 'bloklar', $bloklar );

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return array( 'EN ana sayfa hero: ana_hero bloğuna çevrildi.' );
}
