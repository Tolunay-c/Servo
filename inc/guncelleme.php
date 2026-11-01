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
 * Sürüm 4 (müşteri revizyonu 1, görseller): müşteri resimleri Medya Kitaplığı'na alınır (zipteki geçici
 * `yukle-revizyon1/` klasöründen; yerelde `musteri-gorseller/`), 6 uygulamanın 2'şer resim alanı ve film-folyo,
 * tekstil, etiket vakalarının saha fotoğrafı doldurulur (TR ve EN); lastik, nonwoven ve ambalaj vakalarındaki yer tutucu fotoğraflar da değişir. Geçici klasör sonunda silinir.
 *
 * Sürüm 5: EN ana sayfa teknik karşılaştırma tablosu TR'deki satırlarla birebir aynı olur.
 *
 * Sürüm 6: bülten abone tablosu oluşturulur (inc/bulten.php).
 *
 * Sürüm 7: uygulama notu PDF'leri korumalı dizine alınır; servis kılavuzları sayfasından kurulum notları ve
 * kontrol listesi çıkar, uygulama notları e-posta ile talep akışına bağlanır; uygulama sayfalarındaki "Uygulama
 * notunu indirin" düğmesi talep formuna gider.
 *
 * Sürüm 8 (uygulama sayfası tasarımı): 6 uygulama sayfasında (TR ve EN) "sistem önerisi" bölümü koyu zemine alınır;
 * böylece hero (koyu), resimler (koyu), açık bölümler ve koyu bölüm dönüşümlü olur. Bölüm içeriğine dokunulmaz.
 *
 * Sürüm 9 (ana menü): TR ve EN ana menüye "UYGULAMALAR" / "APPLICATIONS" öğesi eklenir (Başarı hikayelerinden önce,
 * altında 6 uygulama sayfası). Üst öğe TR'de ana sayfadaki #uygulama-alanlari bölümüne, EN'de /en/applications/'a gider.
 *
 * Sürüm 10 (servis menüsü ve footer): ana menüde SERVİS / SERVICE açılır menü olur (Destek hattı, İade ve sevkiyat,
 * Kılavuzlar); footer'da bu sayfalar "SERVİS" / "SERVICE" sütununa alınır, başarı hikayeleri ve rehber bağlantıları
 * Kurumsal / Company sütununa taşınır (4 sütun kalır).
 *
 * Sürüm 11 (servis sayfaları): kılavuzlar hero'sunda tekrar eden üst etiket kalkar; servis alt sayfalarının zemin ritmi
 * koyu-açık dönüşümlü olur; "doğrudan indirilebilir" anlamındaki metinler talep usulüne göre düzeltilir (TR ve EN).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

const SERVO_DB_SURUM = 13;

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
		4 => 'servo_guncelle_4',
		5 => 'servo_guncelle_5',
		6 => 'servo_guncelle_6',
		7 => 'servo_guncelle_7',
		8 => 'servo_guncelle_8',
		9 => 'servo_guncelle_9',
		10 => 'servo_guncelle_10',
		11 => 'servo_guncelle_11',
		12 => 'servo_guncelle_12',
		13 => 'servo_guncelle_13',
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


/**
 * Müşteri görsellerinin anahtarı => [Türkçe dosya adı (uzantısız), Türkçe başlık, İngilizce başlık].
 *
 * Anahtar uygulamanın TR adresidir; zip içindeki geçici klasörde dosyalar `uygulama-{anahtar}-{1|2}.jpg` adıyla durur.
 *
 * @return array<string,array{0:string,1:string,2:string}>
 */
function servo_musteri_gorselleri() {
	return array(
		'ambalaj'    => array( 'AMBALAJ VE KAĞIT', 'Ambalaj ve kağıt', 'Packaging and paper' ),
		'nonwoven'   => array( 'HİJYENİK ÜRÜNLER VE NONWOVEN', 'Hijyenik ürünler ve nonwoven', 'Hygiene products and nonwoven' ),
		'lastik'     => array( 'LASTİK VE KOMPOZİT', 'Lastik ve kompozit', 'Rubber and composite' ),
		'film-folyo' => array( 'FİLM VE FOLYO', 'Film ve folyo', 'Film and foil' ),
		'tekstil'    => array( 'TEKSTİL VE TEKNİK KUMAŞLAR', 'Tekstil ve teknik kumaşlar', 'Textiles and technical fabrics' ),
		'etiket'     => array( 'ETİKET VE YAPIŞKANLI BANT', 'Etiket ve yapışkanlı bant', 'Labels and adhesive tape' ),
	);
}

/**
 * Bir müşteri görselinin kaynak dosyasını bulur.
 *
 * Önce zipteki geçici klasöre (ASCII adlar), yoksa yerel `musteri-gorseller/` klasörüne bakar
 * (Türkçe adlar; macOS'ta ad ayrışık Unicode olabileceği için dizin taranıp normalleştirilir).
 *
 * @param string $anahtar Uygulama anahtarı.
 * @param int    $no      1 | 2.
 * @return string Dosya yolu; yoksa boş metin.
 */
function servo_musteri_gorsel_kaynak( $anahtar, $no ) {
	$tema  = get_template_directory();
	$gecici = $tema . '/yukle-revizyon1/uygulama-' . $anahtar . '-' . $no . '.jpg';
	if ( is_readable( $gecici ) ) {
		return $gecici;
	}

	$bilgi = servo_musteri_gorselleri();
	$klasor = $tema . '/musteri-gorseller';
	if ( ! isset( $bilgi[ $anahtar ] ) || ! is_dir( $klasor ) ) {
		return '';
	}

	// Dosya adları ayrışık ya da birleşik Unicode olabilir: aksan işaretleri ve Türkçe harfler sadeleştirilip karşılaştırılır.
	$sade = static function ( $ad ) {
		$ad = strtr( (string) $ad, array( 'İ' => 'I', 'ı' => 'I', 'Ğ' => 'G', 'ğ' => 'G', 'Ş' => 'S', 'ş' => 'S', 'Ü' => 'U', 'ü' => 'U', 'Ö' => 'O', 'ö' => 'O', 'Ç' => 'C', 'ç' => 'C' ) );
		return strtoupper( preg_replace( '/[^\p{L}\p{N}.]+/u', '', preg_replace( '/\p{M}+/u', '', $ad ) ) );
	};
	$ara = $sade( $bilgi[ $anahtar ][0] . ( 2 === $no ? '2' : '' ) . '.jpg' );
	foreach ( (array) scandir( $klasor ) as $ad ) {
		if ( $sade( $ad ) === $ara && is_readable( $klasor . '/' . $ad ) ) {
			return $klasor . '/' . $ad;
		}
	}

	return '';
}

/**
 * Müşteri görselini Medya Kitaplığı'na alır (zaten alındıysa mevcut kaydı döndürür).
 *
 * @param string $anahtar Uygulama anahtarı.
 * @param int    $no      1 | 2.
 * @return int Ek ID'si.
 * @throws \RuntimeException Dosya yoksa ya da alınamazsa.
 */
function servo_musteri_gorsel_al( $anahtar, $no ) {
	$kod = $anahtar . '-' . $no;
	$var = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_servo_musteri_gorsel', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $kod, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	if ( $var ) {
		return (int) $var[0];
	}

	$kaynak = servo_musteri_gorsel_kaynak( $anahtar, $no );
	if ( '' === $kaynak ) {
		throw new \RuntimeException( "Müşteri görseli bulunamadı: {$kod} (yukle-revizyon1/ klasörü pakette olmalı)." );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$bilgi = servo_musteri_gorselleri();
	$gecici = wp_tempnam( 'uygulama-' . $kod . '.jpg' );
	if ( ! $gecici || ! copy( $kaynak, $gecici ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
		throw new \RuntimeException( "Müşteri görseli kopyalanamadı: {$kod}." );
	}

	$id = media_handle_sideload(
		array(
			'name'     => 'uygulama-' . $anahtar . '-' . $no . '.jpg',
			'tmp_name' => $gecici,
		),
		0,
		$bilgi[ $anahtar ][1] . ' · uygulama resmi ' . $no
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $gecici );
		throw new \RuntimeException( "Müşteri görseli alınamadı ({$kod}): " . $id->get_error_message() );
	}

	update_post_meta( $id, '_servo_musteri_gorsel', $kod );
	update_post_meta( $id, '_wp_attachment_image_alt', $bilgi[ $anahtar ][1] . ' uygulama resmi ' . $no );

	return (int) $id;
}

/**
 * Sürüm 4: müşteri görselleri (uygulama resimleri ve vaka saha fotoğrafları).
 *
 * @return string[]
 */
function servo_guncelle_4() {
	$log   = array();
	$ekler = array();

	foreach ( servo_musteri_gorselleri() as $anahtar => $bilgi ) {
		foreach ( array( 1, 2 ) as $no ) {
			$ekler[ $anahtar ][ $no ] = servo_musteri_gorsel_al( $anahtar, $no );
		}
	}
	$log[] = 'Müşteri görselleri Medya Kitaplığı\'nda: ' . array_sum( array_map( 'count', $ekler ) ) . ' dosya.';

	$uygulama_slug = array(
		'tr' => array( 'ambalaj' => 'ambalaj', 'nonwoven' => 'nonwoven', 'lastik' => 'lastik', 'film-folyo' => 'film-folyo', 'tekstil' => 'tekstil', 'etiket' => 'etiket' ),
		'en' => array( 'ambalaj' => 'packaging', 'nonwoven' => 'nonwoven', 'lastik' => 'rubber', 'film-folyo' => 'film-foil', 'tekstil' => 'textiles', 'etiket' => 'labels' ),
	);
	$adet = 0;

	foreach ( $uygulama_slug as $dil => $liste ) {
		foreach ( $liste as $anahtar => $slug ) {
			$kayit = servo_kayit_bul( $slug, 'uygulama', $dil );
			if ( ! $kayit ) {
				throw new \RuntimeException( "Uygulama kaydı bulunamadı: {$dil}/{$slug}." );
			}
			foreach ( array( 1, 2 ) as $no ) {
				if ( ! (int) get_post_meta( $kayit->ID, "uygulama_gorsel_{$no}", true ) ) {
					update_post_meta( $kayit->ID, "uygulama_gorsel_{$no}", $ekler[ $anahtar ][ $no ] );
					update_post_meta( $kayit->ID, "_uygulama_gorsel_{$no}", "field_servo_uyg_resim_{$no}" );
					++$adet;
				}
			}
		}
	}
	$log[] = "Uygulama resimleri: {$adet} alan.";

	// Altı vakanın saha fotoğrafı ilgili uygulamanın 1. resmi olur; boş olanlar ve tasarımdaki yer tutucular
	// (vaka-*.jpg) değişir. Yer tutucu başka yerde kullanılmıyorsa Medya'dan silinir.
	$vaka  = 0;
	$eski  = array();
	$bilgi = servo_musteri_gorselleri();
	foreach ( array_keys( $bilgi ) as $anahtar ) {
		foreach ( array( 'tr' => 'Saha fotoğrafı · ', 'en' => 'Site photograph · ' ) as $dil => $onek ) {
			$kayit = servo_kayit_bul( $anahtar, 'vaka', $dil );
			if ( ! $kayit ) {
				continue;
			}
			$mevcut = (int) get_post_meta( $kayit->ID, 'foto', true );
			$yer    = $mevcut && 0 === strpos( basename( (string) get_post_meta( $mevcut, '_wp_attached_file', true ) ), 'vaka-' );
			if ( $mevcut && ! $yer ) {
				continue;
			}
			if ( $yer ) {
				$eski[ $mevcut ] = $mevcut;
			}
			update_post_meta( $kayit->ID, 'foto', $ekler[ $anahtar ][1] );
			update_post_meta( $kayit->ID, '_foto', 'field_servo_vaka_foto' );
			update_post_meta( $kayit->ID, 'foto_aciklama', $onek . mb_strtolower( 'tr' === $dil ? $bilgi[ $anahtar ][1] : $bilgi[ $anahtar ][2], 'UTF-8' ) );
			update_post_meta( $kayit->ID, '_foto_aciklama', 'field_servo_vaka_foto_aciklama' );
			++$vaka;
		}
	}

	global $wpdb;
	foreach ( $eski as $ek ) {
		$kullanan = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key NOT LIKE %s AND meta_value = %s", '\\_%', (string) $ek ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		if ( ! $kullanan ) {
			wp_delete_attachment( $ek, true );
			$log[] = "Yer tutucu saha fotoğrafı silindi (#{$ek}).";
		}
	}
	$log[] = "Vaka saha fotoğrafı: {$vaka} kayıt.";

	// Geçici klasör (zip) işini bitirince silinir; yerel musteri-gorseller/ klasörüne dokunulmaz.
	$gecici = get_template_directory() . '/yukle-revizyon1';
	if ( is_dir( $gecici ) ) {
		foreach ( (array) glob( $gecici . '/*' ) as $dosya ) {
			if ( is_file( $dosya ) ) {
				wp_delete_file( $dosya );
			}
		}
		if ( @rmdir( $gecici ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
			$log[] = 'Geçici klasör (yukle-revizyon1) silindi.';
		}
	}

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 5: EN ana sayfa teknik karşılaştırma tablosu TR'deki satırlarla birebir aynı (aynı sıra, İngilizce metin).
 *
 * @return string[]
 */
function servo_guncelle_5() {
	global $wpdb;

	$sayfa   = servo_kayit_bul( 'home', 'page', 'en' );
	$bloklar = $sayfa ? get_post_meta( $sayfa->ID, 'bloklar', true ) : array();
	$i       = is_array( $bloklar ) ? array_search( 'tablo', $bloklar, true ) : false;

	if ( ! $sayfa || false === $i ) {
		throw new \RuntimeException( 'EN ana sayfada karşılaştırma tablosu bloğu bulunamadı.' );
	}

	$id = $sayfa->ID;

	if ( 7 === (int) get_post_meta( $id, "bloklar_{$i}_satirlar", true ) && 4 === (int) get_post_meta( $id, "bloklar_{$i}_altyapi", true )
		&& 'Suitable applications' === get_post_meta( $id, "bloklar_{$i}_satirlar_4_etiket", true ) ) {
		return array();
	}

	// [satır etiketi, [[metin, küçük not, durum], [metin, küçük not, durum]]] — TR tablosuyla aynı sıra.
	$satirlar = array(
		array( 'Architecture', array( array( 'Servo-driven laser alignment system in an enclosed housing', '6 movement configurations', 'yok' ), array( 'Multi-laser alignment system on a three-rail shuttle structure', 'Single servo drive, multiple locked carriages', 'yok' ) ) ),
		array( 'Lines visible at once', array( array( 'As many as the number of lasers in the housing', 'Remaining lines are shown in turn by recipe steps', 'hayir' ), array( 'All cutting lines', 'Simultaneous and continuous once the recipe is set', 'evet' ) ) ),
		array( 'Typical number of lasers', array( array( '1 – 6', '', 'yok' ), array( '5 – 40+', '', 'yok' ) ) ),
		array( 'Number of servo axes', array( array( '1 axis per moving laser', '', 'yok' ), array( '1 shuttle axis in total (single drive)', '', 'yok' ) ) ),
		array( 'Suitable applications', array( array( 'Slitting with few slits, blade alignment, single-reference lines', '', 'yok' ), array( 'Multi-slit slitting, calender and wide-web lines, elastic/ear feeding', '', 'yok' ) ) ),
		array( 'After setup', array( array( 'Lasers move to the next recipe position', '', 'yok' ), array( 'The shuttle returns to its home position, all lines stay in place', '', 'yok' ) ) ),
		array( 'Housing length', array( array( '600 – 4,000 mm', 'Longer lines are designed on request', 'yok' ), array( 'Travel + reserve area + safety margin', 'Scaled to the project · the configurator calculates', 'yok' ) ) ),
	);
	$altyapi  = array(
		array( 'ikon-plc', 'Industrial PLC and servo drive control' ),
		array( 'ikon-veri-yolu', 'Integration into line automation via a standard fieldbus' ),
		array( 'ikon-panel', 'Touchscreen operator panel' ),
		array( 'ikon-lazer', 'Industrial line laser modules' ),
	);

	// Eski satır ve altyapı alanları silinir (doğrudan SQL; ardından meta önbelleği temizlenir).
	foreach ( array( "bloklar_{$i}_satirlar", "_bloklar_{$i}_satirlar", "bloklar_{$i}_altyapi", "_bloklar_{$i}_altyapi" ) as $onek ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND ( meta_key = %s OR meta_key LIKE %s )", $id, $onek, $wpdb->esc_like( $onek . '_' ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}
	wp_cache_delete( $id, 'post_meta' );

	$t = 'field_servo_blk_tablo';
	foreach ( $satirlar as $n => $satir ) {
		update_post_meta( $id, "bloklar_{$i}_satirlar_{$n}_etiket", $satir[0] );
		update_post_meta( $id, "_bloklar_{$i}_satirlar_{$n}_etiket", "{$t}__satirlar_etiket" );
		foreach ( $satir[1] as $h => $hucre ) {
			foreach ( array( 'metin', 'kucuk_not', 'durum' ) as $j => $alan ) {
				update_post_meta( $id, "bloklar_{$i}_satirlar_{$n}_hucreler_{$h}_{$alan}", $hucre[ $j ] );
				update_post_meta( $id, "_bloklar_{$i}_satirlar_{$n}_hucreler_{$h}_{$alan}", "{$t}__satirlar__hucreler_{$alan}" );
			}
		}
		update_post_meta( $id, "bloklar_{$i}_satirlar_{$n}_hucreler", count( $satir[1] ) );
		update_post_meta( $id, "_bloklar_{$i}_satirlar_{$n}_hucreler", "{$t}__satirlar_hucreler" );
	}
	update_post_meta( $id, "bloklar_{$i}_satirlar", count( $satirlar ) );
	update_post_meta( $id, "_bloklar_{$i}_satirlar", "{$t}_satirlar" );

	foreach ( $altyapi as $n => $a ) {
		update_post_meta( $id, "bloklar_{$i}_altyapi_{$n}_ikon", $a[0] );
		update_post_meta( $id, "_bloklar_{$i}_altyapi_{$n}_ikon", "{$t}__altyapi_ikon" );
		update_post_meta( $id, "bloklar_{$i}_altyapi_{$n}_metin", $a[1] );
		update_post_meta( $id, "_bloklar_{$i}_altyapi_{$n}_metin", "{$t}__altyapi_metin" );
	}
	update_post_meta( $id, "bloklar_{$i}_altyapi", count( $altyapi ) );
	update_post_meta( $id, "_bloklar_{$i}_altyapi", "{$t}_altyapi" );
	update_post_meta( $id, "bloklar_{$i}_altyapi_etiket", 'Common infrastructure' );
	update_post_meta( $id, "_bloklar_{$i}_altyapi_etiket", "{$t}_altyapi_etiket" );

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return array( 'EN ana sayfa tablosu: 7 satır + ortak altyapı satırı (TR ile aynı).' );
}

/**
 * Sürüm 6: bülten abone tablosu.
 *
 * @return string[]
 */
function servo_guncelle_6() {
	if ( '1' === (string) get_option( 'servo_bulten_tablo', '' ) ) {
		return array();
	}

	servo_bulten_tablo_hazirla();

	return array( 'Bülten abone tablosu oluşturuldu.' );
}

/**
 * Bir sayfanın blok listesini ACF üzerinden okuyup değiştirir ve geri yazar (indeksler kendiliğinden yeniden kurulur).
 *
 * @param int      $id Yazı ID'si.
 * @param callable $fn Bloklar dizisini (alan anahtarlı) alır, [yeni dizi, log satırı] döndürür.
 * @return string Log satırı (değişiklik yoksa boş).
 */
function servo_guncelle_bloklari( $id, $fn ) {
	$bloklar = get_field( 'bloklar', $id, false );
	if ( ! is_array( $bloklar ) ) {
		throw new \RuntimeException( "Blok listesi okunamadı (#{$id})." );
	}

	list( $yeni, $log ) = call_user_func( $fn, $bloklar );
	if ( $yeni === $bloklar ) {
		return '';
	}

	update_field( 'bloklar', $yeni, $id );
	wp_cache_delete( $id, 'post_meta' );

	return $log;
}

/**
 * Sürüm 7: uygulama notu talebi (PDF'ler korumalı dizinde; indirme yerine e-posta ile talep).
 *
 * @return string[]
 */
function servo_guncelle_7() {
	$log = array();
	$d   = 'field_servo_blk_indirme_listesi';

	// Korumalı dizine alınacak PDF'ler: talep akışına bağlanan uygulama notu dosyaları (ID'ler canlıda farklı olabilir, satırlardan toplanır).
	$koru = array();

	// 2) /servis-kilavuzlar/ ve /en/service-manuals/: kurulum notları ve kontrol listesi çıkar, notlar talep akışına bağlanır.
	$sayfalar = array(
		'tr' => array( 'servis-kilavuzlar', array( 'DOKÜMANLAR', "UYGULAMA NOTLARI\nVE İLGİLİ BAĞLANTILAR", 'Uygulama notlarını PDF olarak talep edebilirsiniz; talep ekibimize iletilir.' ), 'Uygulama notu' ),
		'en' => array( 'service-manuals', array( 'DOCUMENTS', "APPLICATION NOTES\nAND RELATED LINKS", 'Application notes are available as PDF on request; your request is passed to our team.' ), 'Application note' ),
	);
	foreach ( $sayfalar as $dil => $s ) {
		$kayit = servo_kayit_bul( $s[0], 'page', $dil );
		if ( ! $kayit ) {
			throw new \RuntimeException( "Sayfa bulunamadı: {$dil}/{$s[0]}." );
		}
		$satir = servo_guncelle_bloklari(
			$kayit->ID,
			static function ( $bloklar ) use ( $d, $s, &$koru ) {
				foreach ( $bloklar as $n => $blok ) {
					if ( 'indirme_listesi' !== $blok['acf_fc_layout'] ) {
						continue;
					}
					$dosyalar = array();
					foreach ( (array) $blok[ "{$d}_dosyalar" ] as $r ) {
						$baslik = (string) $r[ "{$d}__dosyalar_baslik" ];
						$ek     = (int) $r[ "{$d}__dosyalar_dosya" ];
						// Kurulum notları (PDF) ve kontrol listesi (XLSX) listeden çıkar.
						$dosya_adi = $ek ? basename( (string) get_post_meta( $ek, '_wp_attached_file', true ) ) : '';
						if ( 0 === strpos( $dosya_adi, 'servomotion-kurulum-' ) || 0 === strpos( $dosya_adi, 'servomotion-installation-' ) ) {
							continue;
						}
						if ( 0 === strpos( $baslik, $s[2] ) ) {
							$r[ "{$d}__dosyalar_talep" ] = '1';
							if ( $ek ) {
								$koru[ $ek ] = $ek;
							}
						}
						$dosyalar[] = $r;
					}
					$bloklar[ $n ][ "{$d}_dosyalar" ] = $dosyalar;
					$bloklar[ $n ][ "{$d}_etiket" ]   = $s[1][0];
					$bloklar[ $n ][ "{$d}_baslik" ]   = $s[1][1];
					$bloklar[ $n ][ "{$d}_aciklama" ] = $s[1][2];
					break;
				}
				return array( $bloklar, 'Kılavuzlar sayfası güncellendi.' );
			}
		);
		if ( '' !== $satir ) {
			$log[] = strtoupper( $dil ) . ': ' . $satir;
		}
	}

	// 3) /en/documents/: uygulama notları listesi (doğrudan indirme) talep akışına bağlanır; TR dokümanlar sayfasında doğrudan not bağlantısı yoktur.
	$belgeler = servo_kayit_bul( 'documents', 'page', 'en' );
	if ( $belgeler ) {
		$satir = servo_guncelle_bloklari(
			$belgeler->ID,
			static function ( $bloklar ) use ( $d, &$koru ) {
				foreach ( $bloklar as $n => $blok ) {
					if ( 'indirme_listesi' !== $blok['acf_fc_layout'] ) {
						continue;
					}
					foreach ( (array) $blok[ "{$d}_dosyalar" ] as $k => $r ) {
						$ek = (int) $r[ "{$d}__dosyalar_dosya" ];
						if ( $ek && 0 === strpos( basename( (string) get_post_meta( $ek, '_wp_attached_file', true ) ), 'application-' ) ) {
							$bloklar[ $n ][ "{$d}_dosyalar" ][ $k ][ "{$d}__dosyalar_talep" ] = '1';
							$koru[ $ek ] = $ek;
						}
					}
				}
				return array( $bloklar, 'EN dokümanlar sayfası: uygulama notları talep akışında.' );
			}
		);
		if ( '' !== $satir ) {
			$log[] = $satir;
		}
	}

	// 4) Uygulama sayfaları: hero düğmesi talep formuna gider; forma çapa olan blok CTA'dan önce eklenir.
	$uygulamalar = array(
		'tr' => array( 'lastik', 'nonwoven', 'ambalaj', 'film-folyo', 'tekstil', 'etiket' ),
		'en' => array( 'rubber', 'nonwoven', 'packaging', 'film-foil', 'textiles', 'labels' ),
	);
	$metin       = array(
		'tr' => array( 'UYGULAMA NOTUNU TALEP EDİN', 'UYGULAMA NOTU', "UYGULAMA NOTUNU\nTALEP EDİN", 'Uygulama notu, talep üzerine PDF olarak paylaşılır.', 'GÖNDER' ),
		'en' => array( 'REQUEST APPLICATION NOTE', 'APPLICATION NOTE', "REQUEST THE\nAPPLICATION NOTE", 'The application note is shared as a PDF on request.', 'SEND' ),
	);
	$sayi        = 0;
	foreach ( $uygulamalar as $dil => $slugs ) {
		foreach ( $slugs as $slug ) {
			$kayit = servo_kayit_bul( $slug, 'uygulama', $dil );
			if ( ! $kayit ) {
				throw new \RuntimeException( "Uygulama kaydı bulunamadı: {$dil}/{$slug}." );
			}
			$m    = $metin[ $dil ];
			$satir = servo_guncelle_bloklari(
				$kayit->ID,
				static function ( $bloklar ) use ( $m ) {
					foreach ( $bloklar as $blok ) {
						if ( 'talep_formu' === $blok['acf_fc_layout'] && 'uygulama_notu' === $blok['field_servo_blk_talep_formu_tur'] ) {
							return array( $bloklar, '' );
						}
					}

					// Hero'daki ilk düğme (uygulama notu PDF'i) talep formuna gider.
					if ( 'sayfa_hero' === $bloklar[0]['acf_fc_layout'] && ! empty( $bloklar[0]['field_servo_blk_sayfa_hero_butonlar'][0] ) ) {
						$bloklar[0]['field_servo_blk_sayfa_hero_butonlar'][0]['field_servo_blk_sayfa_hero__butonlar_link']   = array(
							'url'    => '#not-talep',
							'title'  => $m[0],
							'target' => '',
						);
						$bloklar[0]['field_servo_blk_sayfa_hero_butonlar'][0]['field_servo_blk_sayfa_hero__butonlar_rozet'] = '';
						$bloklar[0]['field_servo_blk_sayfa_hero_butonlar'][0]['field_servo_blk_sayfa_hero__butonlar_indir'] = '0';
					}

					$form = array(
						'acf_fc_layout'                          => 'talep_formu',
						'field_servo_blk_talep_formu_baslik_stili' => 'standart',
						'field_servo_blk_talep_formu_etiket'     => $m[1],
						'field_servo_blk_talep_formu_baslik'     => $m[2],
						'field_servo_blk_talep_formu_aciklama'   => $m[3],
						'field_servo_blk_talep_formu_tur'        => 'uygulama_notu',
						'field_servo_blk_talep_formu_buton'      => $m[4],
						'field_servo_blk_talep_formu_not'        => '',
						'field_servo_blk_talep_formu_arka_plan'  => 'beyaz',
						'field_servo_blk_talep_formu_capa'       => 'not-talep',
					);

					// CTA'dan önce (son blok CTA ise); değilse sona.
					$son = count( $bloklar ) - 1;
					if ( 'cta' === $bloklar[ $son ]['acf_fc_layout'] ) {
						array_splice( $bloklar, $son, 0, array( $form ) );
					} else {
						$bloklar[] = $form;
					}

					return array( $bloklar, 'ok' );
				}
			);
			if ( '' !== $satir ) {
				++$sayi;
			}
		}
	}
	if ( $sayi ) {
		$log[] = "Uygulama sayfaları: hero düğmesi ve talep formu ({$sayi} sayfa).";
	}

	// Linkler değiştiği için PDF'ler en son korumalı dizine alınır.
	$tasinan = 0;
	foreach ( $koru as $ek ) {
		if ( 'attachment' === get_post_type( $ek ) && servo_ek_tasi( $ek, true ) ) {
			++$tasinan;
		}
	}
	if ( $tasinan ) {
		$log[] = "Uygulama notu PDF'leri korumalı dizine alındı: {$tasinan}.";
	}

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 8: uygulama sayfalarında "sistem önerisi" bölümünün zemini koyu olur (açık bölümler art arda gelmesin).
 *
 * @return string[]
 */
function servo_guncelle_8() {
	$say         = 0;
	$uygulamalar = array(
		'tr' => array( 'lastik', 'nonwoven', 'ambalaj', 'film-folyo', 'tekstil', 'etiket' ),
		'en' => array( 'rubber', 'nonwoven', 'packaging', 'film-foil', 'textiles', 'labels' ),
	);

	foreach ( $uygulamalar as $dil => $slugs ) {
		foreach ( $slugs as $slug ) {
			$kayit = servo_kayit_bul( $slug, 'uygulama', $dil );
			if ( ! $kayit ) {
				throw new \RuntimeException( "Uygulama kaydı bulunamadı: {$dil}/{$slug}." );
			}
			$bloklar = get_post_meta( $kayit->ID, 'bloklar', true );
			$i       = is_array( $bloklar ) ? array_search( 'sistem_onerisi', $bloklar, true ) : false;
			if ( false !== $i && 'koyu' !== get_post_meta( $kayit->ID, "bloklar_{$i}_arka_plan", true ) ) {
				update_post_meta( $kayit->ID, "bloklar_{$i}_arka_plan", 'koyu' );
				update_post_meta( $kayit->ID, "_bloklar_{$i}_arka_plan", 'field_servo_blk_sistem_onerisi_arka_plan' );
				++$say;
			}
		}
	}

	return $say ? array( "Sistem önerisi bölümü koyu zemine alındı: {$say} sayfa." ) : array();
}

/**
 * Ana menü ID'si (dile göre): önce Polylang menü konumu eşlemesi, yoksa tema konumu / ad.
 *
 * @param string $dil tr | en.
 * @return int 0 ise bulunamadı.
 */
function servo_ana_menu_id( $dil ) {
	$pll = get_option( 'polylang' );
	$id  = isset( $pll['nav_menus'][ get_stylesheet() ]['ana_menu'][ $dil ] ) ? (int) $pll['nav_menus'][ get_stylesheet() ]['ana_menu'][ $dil ] : 0;

	if ( ! $id && 'tr' === $dil ) {
		$konumlar = get_nav_menu_locations();
		$id       = isset( $konumlar['ana_menu'] ) ? (int) $konumlar['ana_menu'] : 0;
	}

	return $id && wp_get_nav_menu_object( $id ) ? $id : 0;
}

/**
 * Sürüm 9: ana menüye "UYGULAMALAR" / "APPLICATIONS" üst öğesi ve altında 6 uygulama sayfası.
 *
 * @return string[]
 */
function servo_guncelle_9() {
	$log  = array();
	$dil  = array(
		'tr' => array(
			'baslik'  => 'UYGULAMALAR',
			'url'     => home_url( '/#uygulama-alanlari' ),
			'once'    => 'BAŞARI HİKAYELERİ',
			'cocuklar' => array(
				'lastik'     => 'Lastik ve kompozit',
				'nonwoven'   => 'Hijyenik ürünler ve nonwoven',
				'ambalaj'    => 'Ambalaj ve kağıt',
				'film-folyo' => 'Film ve folyo',
				'tekstil'    => 'Tekstil ve teknik kumaşlar',
				'etiket'     => 'Etiket ve yapışkanlı bant',
			),
		),
		'en' => array(
			'baslik'  => 'APPLICATIONS',
			'url'     => home_url( '/en/applications/' ),
			'once'    => 'SUCCESS STORIES',
			'cocuklar' => array(
				'rubber'    => 'Rubber and composite',
				'nonwoven'  => 'Hygiene products and nonwoven',
				'packaging' => 'Packaging and paper',
				'film-foil' => 'Film and foil',
				'textiles'  => 'Textiles and technical fabrics',
				'labels'    => 'Labels and adhesive tape',
			),
		),
	);

	foreach ( $dil as $kod => $m ) {
		$menu = servo_ana_menu_id( $kod );
		if ( ! $menu ) {
			throw new \RuntimeException( "Ana menü bulunamadı ({$kod})." );
		}

		$ogeler = wp_get_nav_menu_items( $menu );
		foreach ( (array) $ogeler as $o ) {
			if ( in_array( 'servo-ust-link', (array) $o->classes, true ) ) {
				continue 2; // Zaten kurulu.
			}
		}

		// Çocuk kayıtlar önce doğrulanır (eksik uygulama varsa menü yarım kurulmaz).
		$kayitlar = array();
		foreach ( $m['cocuklar'] as $slug => $etiket ) {
			$kayit = servo_kayit_bul( $slug, 'uygulama', $kod );
			if ( ! $kayit ) {
				throw new \RuntimeException( "Uygulama kaydı bulunamadı: {$kod}/{$slug}." );
			}
			$kayitlar[ $slug ] = $kayit;
		}

		$ust = wp_update_nav_menu_item(
			$menu,
			0,
			array(
				'menu-item-title'   => $m['baslik'],
				'menu-item-url'     => $m['url'],
				'menu-item-type'    => 'custom',
				'menu-item-classes' => 'servo-ust-link',
				'menu-item-status'  => 'publish',
			)
		);
		if ( is_wp_error( $ust ) ) {
			throw new \RuntimeException( 'Menü öğesi eklenemedi: ' . $ust->get_error_message() );
		}

		$cocuklar = array();
		foreach ( $m['cocuklar'] as $slug => $etiket ) {
			$c = wp_update_nav_menu_item(
				$menu,
				0,
				array(
					'menu-item-title'     => $etiket,
					'menu-item-type'      => 'post_type',
					'menu-item-object'    => 'uygulama',
					'menu-item-object-id' => $kayitlar[ $slug ]->ID,
					'menu-item-parent-id' => $ust,
					'menu-item-status'    => 'publish',
				)
			);
			if ( is_wp_error( $c ) ) {
				throw new \RuntimeException( 'Alt menü öğesi eklenemedi: ' . $c->get_error_message() );
			}
			$cocuklar[] = $c;
		}

		// Sıra: üst seviye öğeler olduğu gibi; yeni öğe ve çocukları "önce" öğesinin hemen öncesine girer.
		$sira = array();
		foreach ( (array) $ogeler as $o ) {
			if ( 0 === (int) $o->menu_item_parent && 0 === strcasecmp( $o->title, $m['once'] ) ) {
				$sira[] = $ust;
				$sira   = array_merge( $sira, $cocuklar );
			}
			$sira[] = (int) $o->ID;
		}
		if ( ! in_array( $ust, $sira, true ) ) {
			$sira[] = $ust;
			$sira   = array_merge( $sira, $cocuklar );
		}
		foreach ( $sira as $n => $id ) {
			wp_update_post(
				array(
					'ID'         => $id,
					'menu_order' => $n + 1,
				)
			);
		}

		$log[] = strtoupper( $kod ) . ": {$m['baslik']} menüye eklendi (6 alt öğe).";
	}

	return $log;
}


/**
 * Bir menünün öğelerini "üst öğe, ardından çocukları" düzeninde yeniden sıralar.
 *
 * @param int                $menu     Menü ID'si.
 * @param array<int,int[]>   $cocuklar Üst öğe ID => istenen çocuk ID sırası (belirtilmeyen çocuklar mevcut sırayla sona).
 */
function servo_menu_yeniden_sirala( $menu, $cocuklar = array() ) {
	$ogeler = (array) wp_get_nav_menu_items( $menu );
	$ust    = array();
	$alt    = array();

	foreach ( $ogeler as $o ) {
		if ( 0 === (int) $o->menu_item_parent ) {
			$ust[] = (int) $o->ID;
		} else {
			$alt[ (int) $o->menu_item_parent ][] = (int) $o->ID;
		}
	}

	$n = 0;
	foreach ( $ust as $id ) {
		wp_update_post( array( 'ID' => $id, 'menu_order' => ++$n ) );
		$mevcut = isset( $alt[ $id ] ) ? $alt[ $id ] : array();
		$istenen = isset( $cocuklar[ $id ] ) ? array_values( array_unique( array_merge( $cocuklar[ $id ], $mevcut ) ) ) : $mevcut;
		foreach ( $istenen as $c ) {
			if ( in_array( $c, $mevcut, true ) ) {
				wp_update_post( array( 'ID' => $c, 'menu_order' => ++$n ) );
			}
		}
	}
}

/**
 * Menüde adresin yolu verilen sayfaya giden ilk öğeyi bulur.
 *
 * @param WP_Post[] $ogeler Menü öğeleri.
 * @param string    $yol    Yol (örn. /servis/).
 * @param bool      $ust    Yalnızca üst seviye mi?
 * @return WP_Post|null
 */
function servo_menu_oge_bul( $ogeler, $yol, $ust = true ) {
	foreach ( $ogeler as $o ) {
		if ( ( ! $ust || 0 === (int) $o->menu_item_parent ) && untrailingslashit( (string) wp_parse_url( $o->url, PHP_URL_PATH ) ) === untrailingslashit( $yol ) ) {
			return $o;
		}
	}

	return null;
}

/**
 * Sürüm 10: ana menüde SERVİS açılır menü; footer'da ayrı SERVİS sütunu.
 *
 * @return string[]
 */
function servo_guncelle_10() {
	$log = array();
	$pll = get_option( 'polylang' );
	$tema = get_stylesheet();
	$dil = array(
		'tr' => array(
			'ana'    => 'servis',
			'sayfalar' => array( 'servis-destek-hatti' => 'Destek hattı', 'servis-iade-sevkiyat' => 'İade ve sevkiyat', 'servis-kilavuzlar' => 'Kılavuzlar' ),
			'sutun'  => array( 'BAŞARI HİKAYELERİ', 'SERVİS', 'KURUMSAL' ),
			'tasi'   => array( 'basari-hikayeleri', '/rehber/' ),
			'iade'   => array( 'servis-iade-sevkiyat', 'İade ve sevkiyat', 'destek-hatti' ),
		),
		'en' => array(
			'ana'    => 'service',
			'sayfalar' => array( 'service-helpdesk' => 'Helpdesk', 'service-return-shipment' => 'Return shipment', 'service-manuals' => 'Manuals' ),
			'sutun'  => array( 'SUCCESS STORIES', 'SERVICE', 'COMPANY' ),
			'tasi'   => array( 'success-stories', '/guide/' ),
			'iade'   => array( 'service-return-shipment', 'Return shipment', 'helpdesk' ),
		),
	);

	foreach ( $dil as $kod => $m ) {
		$ana_sayfa = servo_kayit_bul( $m['ana'], 'page', $kod );
		if ( ! $ana_sayfa ) {
			throw new \RuntimeException( "Servis sayfası bulunamadı ({$kod})." );
		}
		$kayitlar = array();
		foreach ( $m['sayfalar'] as $slug => $etiket ) {
			$k = servo_kayit_bul( $slug, 'page', $kod );
			if ( ! $k ) {
				throw new \RuntimeException( "Servis alt sayfası bulunamadı: {$kod}/{$slug}." );
			}
			$kayitlar[ $slug ] = $k;
		}

		// 1) Ana menü: SERVİS açılır menü.
		$menu = servo_ana_menu_id( $kod );
		if ( ! $menu ) {
			throw new \RuntimeException( "Ana menü bulunamadı ({$kod})." );
		}
		$ogeler = (array) wp_get_nav_menu_items( $menu );
		$ust    = servo_menu_oge_bul( $ogeler, (string) wp_parse_url( get_permalink( $ana_sayfa ), PHP_URL_PATH ) );
		if ( ! $ust ) {
			throw new \RuntimeException( "Ana menüde servis öğesi bulunamadı ({$kod})." );
		}

		if ( ! in_array( 'servo-ust-link', (array) $ust->classes, true ) ) {
			update_post_meta( $ust->ID, '_menu_item_classes', array( 'servo-ust-link' ) );
			$yeni = array();
			foreach ( $kayitlar as $slug => $k ) {
				$c = wp_update_nav_menu_item(
					$menu,
					0,
					array(
						'menu-item-title'     => $m['sayfalar'][ $slug ],
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $k->ID,
						'menu-item-parent-id' => $ust->ID,
						'menu-item-status'    => 'publish',
					)
				);
				if ( is_wp_error( $c ) ) {
					throw new \RuntimeException( 'Alt menü öğesi eklenemedi: ' . $c->get_error_message() );
				}
				$yeni[] = $c;
			}
			servo_menu_yeniden_sirala( $menu, array( (int) $ust->ID => $yeni ) );
			$log[] = strtoupper( $kod ) . ': ana menüde ' . $ust->title . ' açılır menü oldu (3 alt öğe).';
		}

		// 2) Footer: SERVİS sütunu.
		$f_id = isset( $pll['nav_menus'][ $tema ]['footer_menu'][ $kod ] ) ? (int) $pll['nav_menus'][ $tema ]['footer_menu'][ $kod ] : 0;
		if ( ! $f_id && 'tr' === $kod ) {
			$konumlar = get_nav_menu_locations();
			$f_id     = isset( $konumlar['footer_menu'] ) ? (int) $konumlar['footer_menu'] : 0;
		}
		if ( ! $f_id || ! wp_get_nav_menu_object( $f_id ) ) {
			throw new \RuntimeException( "Footer menüsü bulunamadı ({$kod})." );
		}

		$fo = (array) wp_get_nav_menu_items( $f_id );
		$a  = null;
		$b  = null;
		$zaten = false;
		foreach ( $fo as $o ) {
			if ( 0 !== (int) $o->menu_item_parent ) {
				continue;
			}
			if ( 0 === strcasecmp( $o->title, $m['sutun'][0] ) ) {
				$a = $o;
			} elseif ( 0 === strcasecmp( $o->title, $m['sutun'][2] ) ) {
				$b = $o;
			} elseif ( 0 === strcasecmp( $o->title, $m['sutun'][1] ) ) {
				$zaten = true;
			}
		}
		if ( $zaten ) {
			continue;
		}
		if ( ! $a || ! $b ) {
			throw new \RuntimeException( "Footer sütunları bulunamadı ({$kod})." );
		}

		wp_update_post( array( 'ID' => $a->ID, 'post_title' => $m['sutun'][1] ) );

		$tasinan = array();
		$kalan   = array();
		$destek  = 0;
		foreach ( $fo as $o ) {
			if ( (int) $o->menu_item_parent !== (int) $a->ID ) {
				continue;
			}
			$yol = (string) wp_parse_url( $o->url, PHP_URL_PATH );
			if ( false !== strpos( $yol, $m['tasi'][0] ) || false !== strpos( $yol, $m['tasi'][1] ) ) {
				update_post_meta( $o->ID, '_menu_item_menu_item_parent', (int) $b->ID );
				$tasinan[] = (int) $o->ID;
			} else {
				$kalan[] = (int) $o->ID;
				if ( false !== strpos( $yol, $m['iade'][2] ) ) {
					$destek = count( $kalan );
				}
			}
		}

		$iade_kayit = $kayitlar[ $m['iade'][0] ];
		$iade       = wp_update_nav_menu_item(
			$f_id,
			0,
			array(
				'menu-item-title'     => $m['iade'][1],
				'menu-item-type'      => 'post_type',
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $iade_kayit->ID,
				'menu-item-parent-id' => $a->ID,
				'menu-item-status'    => 'publish',
			)
		);
		if ( is_wp_error( $iade ) ) {
			throw new \RuntimeException( 'Footer öğesi eklenemedi: ' . $iade->get_error_message() );
		}
		array_splice( $kalan, $destek ? $destek : count( $kalan ), 0, array( (int) $iade ) );

		// Kurumsal sütunu: mevcut çocukların ilkinden sonra taşınanlar.
		$b_cocuk = array();
		foreach ( $fo as $o ) {
			if ( (int) $o->menu_item_parent === (int) $b->ID ) {
				$b_cocuk[] = (int) $o->ID;
			}
		}
		array_splice( $b_cocuk, 1, 0, $tasinan );

		servo_menu_yeniden_sirala( $f_id, array( (int) $a->ID => $kalan, (int) $b->ID => $b_cocuk ) );
		$log[] = strtoupper( $kod ) . ": footer {$m['sutun'][1]} sütunu kuruldu; başarı hikayeleri ve rehber {$m['sutun'][2]} sütununa taşındı.";
	}

	return $log;
}

/**
 * Sürüm 11: servis sayfaları (hero etiketi, zemin ritmi) ve "doğrudan indirilebilir" metinleri.
 *
 * @return string[]
 */
function servo_guncelle_11() {
	$log = array();

	// 1) Servis alt sayfalarında hero üst etiketi, üst yoldaki ("SERVİS · X") ile aynı olduğu için kalkar.
	$servis = array(
		'tr' => array( 'servis-destek-hatti', 'servis-iade-sevkiyat', 'servis-kilavuzlar' ),
		'en' => array( 'service-helpdesk', 'service-return-shipment', 'service-manuals' ),
	);
	// Zemin ritmi: art arda üç açık bölümü bölen koyu bölüm (sayfa => [blok türü]).
	$koyu = array(
		'servis-destek-hatti'     => 'madde_listesi',
		'servis-iade-sevkiyat'    => 'numarali_kartlar',
		'servis-kilavuzlar'       => 'madde_listesi',
		'service-helpdesk'        => 'madde_listesi',
		'service-return-shipment' => 'numarali_kartlar',
		'service-manuals'         => 'madde_listesi',
	);
	$etiket = 0;
	$zemin  = 0;
	foreach ( $servis as $kod => $slugs ) {
		foreach ( $slugs as $slug ) {
			$kayit   = servo_kayit_bul( $slug, 'page', $kod );
			$bloklar = $kayit ? get_post_meta( $kayit->ID, 'bloklar', true ) : array();
			if ( ! $kayit || ! is_array( $bloklar ) ) {
				throw new \RuntimeException( "Servis sayfası bulunamadı: {$kod}/{$slug}." );
			}

			if ( 'sayfa_hero' === $bloklar[0] && '' !== (string) get_post_meta( $kayit->ID, 'bloklar_0_etiket', true ) ) {
				update_post_meta( $kayit->ID, 'bloklar_0_etiket', '' );
				++$etiket;
			}

			$i = array_search( $koyu[ $slug ], $bloklar, true );
			if ( false !== $i && 'koyu' !== get_post_meta( $kayit->ID, "bloklar_{$i}_arka_plan", true ) ) {
				update_post_meta( $kayit->ID, "bloklar_{$i}_arka_plan", 'koyu' );
				update_post_meta( $kayit->ID, "_bloklar_{$i}_arka_plan", "field_servo_blk_{$koyu[ $slug ]}_arka_plan" );
				++$zemin;
			}
		}
	}
	if ( $etiket ) {
		$log[] = "Servis alt sayfaları: hero tekrar eden etiket kaldırıldı ({$etiket} sayfa).";
	}
	if ( $zemin ) {
		$log[] = "Servis alt sayfaları: koyu bölüm eklendi ({$zemin} sayfa).";
	}

	// 2) "Doğrudan indirilebilir" metinleri → talep usulü. [dil, sayfa, meta anahtarı kalıbı, [eski => yeni]]
	$metinler = array(
		array( 'tr', 'dokumanlar', 'bloklar\_0\_giris', array( 'Kurulum notları ve uygulama notları doğrudan indirilebilir; ürün veri sayfaları ve sertifikalar için' => 'Ürün veri sayfaları, sertifikalar ve uygulama notları için' ) ),
		array( 'tr', 'dokumanlar', 'bloklar\_%\_not', array( 'Kurulum notları, kontrol listesi ve uygulama notları serbest erişimdedir — ' => 'Uygulama notlarını ', 'kılavuzlar sayfasından</a> doğrudan indirebilirsiniz.' => 'kılavuzlar sayfasından</a> e-posta adresinizi girerek talep edebilirsiniz.' ) ),
		array( 'tr', 'servis', 'bloklar\_1\_kartlar\_2\_metin', array( 'Kurulum notları ve uygulama notları doğrudan indirilebilir. Kullanım' => 'Uygulama notları e-posta adresinizi girerek talep edilir. Kullanım' ) ),
		array( 'tr', 'servis-kilavuzlar', 'bloklar\_0\_giris', array( 'Herkese açık dokümanları doğrudan indirebilirsiniz.' => 'Uygulama notlarını e-posta adresinizi girerek talep edebilirsiniz.' ) ),
		array( 'en', 'service', 'bloklar\_1\_kartlar\_2\_metin', array( 'Installation notes and application notes can be downloaded directly. The operating' => 'Application notes are requested by entering your email address. The operating' ) ),
		array( 'en', 'service-manuals', 'bloklar\_0\_giris', array( 'Public documents can be downloaded directly.' => 'Application notes can be requested by entering your email address.' ) ),
		array( 'en', 'documents', 'bloklar\_0\_giris', array( 'Installation notes and application notes can be downloaded directly. For product data sheets and certificates,' => 'For product data sheets, certificates and application notes,' ) ),
		array( 'en', 'documents', 'bloklar\_0\_butonlar\_%\_link', array( 'FREE DOWNLOADS' => 'APPLICATION NOTES' ) ),
		array( 'en', 'documents', 'bloklar\_%\_etiket', array( 'FREE ACCESS' => 'ON REQUEST' ) ),
		array( 'en', 'documents', 'bloklar\_%\_aciklama', array( 'No form required.' => 'Enter your email address next to the note you need and we will get back to you.' ) ),
	);
	foreach ( $metinler as $m ) {
		$kayit = servo_kayit_bul( $m[1], 'page', $m[0] );
		if ( ! $kayit ) {
			throw new \RuntimeException( "Sayfa bulunamadı: {$m[0]}/{$m[1]}." );
		}
		$adet = servo_guncelle_meta_metin( $kayit->ID, $m[2], $m[3] );
		if ( $adet ) {
			$log[] = "Metin düzeltildi {$m[0]}/{$m[1]}: {$adet} alan.";
		}
	}

	// Rank Math sosyal açıklaması (TR dokümanlar).
	$dok = servo_kayit_bul( 'dokumanlar', 'page', 'tr' );
	if ( $dok ) {
		$a = (string) get_post_meta( $dok->ID, 'rank_math_facebook_description', true );
		if ( false !== strpos( $a, 'veri sayfalarını indirin' ) ) {
			update_post_meta( $dok->ID, 'rank_math_facebook_description', str_replace( 'veri sayfalarını indirin', 'veri sayfalarını talep edin', $a ) );
			$log[] = 'Metin düzeltildi tr/dokumanlar: sosyal açıklama.';
		}
	}

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 12: dokümanlar sayfalarından "Kurulum ve devreye alma notları" öğesi kalkar
 * (kılavuzlar sayfasındaki kararla tutarlı). Talep formunun iki sütunlu düzeni şablondadır.
 *
 * @return string[]
 */
function servo_guncelle_12() {
	$log = array();

	foreach ( array( 'tr' => 'dokumanlar', 'en' => 'documents' ) as $dil => $slug ) {
		$kayit = servo_kayit_bul( $slug, 'page', $dil );
		if ( ! $kayit ) {
			throw new \RuntimeException( "Sayfa bulunamadı: {$dil}/{$slug}." );
		}

		$adet = 0;
		$tur  = (array) get_post_meta( $kayit->ID, 'bloklar', true );

		foreach ( $tur as $i => $ad ) {
			if ( 'indirme_listesi' !== $ad ) {
				continue;
			}

			$on    = "bloklar_{$i}_dosyalar";
			$satir = (int) get_post_meta( $kayit->ID, $on, true );

			for ( $j = $satir - 1; $j >= 0; $j-- ) {
				if ( ! preg_match( '/installation and commissioning|kurulum ve devreye alma/i', (string) get_post_meta( $kayit->ID, "{$on}_{$j}_baslik", true ) ) ) {
					continue;
				}

				// Sonraki satırları bir yukarı kaydır, son satırı sil.
				$alanlar = array( 'baslik', 'dosya', 'link', 'rozet', 'talep' );
				for ( $k = $j; $k < $satir - 1; $k++ ) {
					foreach ( $alanlar as $alan ) {
						foreach ( array( '', '_' ) as $ek ) {
							$deger = get_post_meta( $kayit->ID, "{$ek}{$on}_" . ( $k + 1 ) . "_{$alan}", true );
							update_post_meta( $kayit->ID, "{$ek}{$on}_{$k}_{$alan}", $deger );
						}
					}
				}
				foreach ( $alanlar as $alan ) {
					delete_post_meta( $kayit->ID, "{$on}_" . ( $satir - 1 ) . "_{$alan}" );
					delete_post_meta( $kayit->ID, "_{$on}_" . ( $satir - 1 ) . "_{$alan}" );
				}
				--$satir;
				update_post_meta( $kayit->ID, $on, $satir );
				++$adet;
			}
		}

		if ( $adet ) {
			wp_cache_delete( $kayit->ID, 'post_meta' );
			$log[] = "Kaldırıldı {$dil}/{$slug}: {$adet} öğe.";
		}

		// Sayfa açıklamasında da geçmesin.
		foreach ( array( 'rank_math_description', 'rank_math_facebook_description' ) as $anahtar ) {
			$a = (string) get_post_meta( $kayit->ID, $anahtar, true );
			if ( false !== stripos( $a, 'conformity, installation notes and' ) ) {
				update_post_meta( $kayit->ID, $anahtar, str_ireplace( 'conformity, installation notes and', 'conformity and', $a ) );
				$log[] = "Açıklama düzeltildi {$dil}/{$slug}: {$anahtar}.";
			}
		}
	}

	if ( function_exists( 'servo_arama_tumunu_dizinle' ) ) {
		servo_arama_tumunu_dizinle();
	}

	return $log;
}

/**
 * Sürüm 13: bülten artık REST ile kaydolduğu için kullanılmayan mailto betiğini (varsa) siler.
 *
 * @return string[]
 */
function servo_guncelle_13() {
	$log   = array();
	$dosya = get_template_directory() . '/assets/js/bulten-mailto.js';

	if ( file_exists( $dosya ) ) {
		if ( ! wp_delete_file_from_directory( $dosya, get_template_directory() . '/assets/js' ) ) {
			throw new \RuntimeException( 'assets/js/bulten-mailto.js silinemedi.' );
		}
		$log[] = 'Silindi: assets/js/bulten-mailto.js.';
	}

	return $log;
}
