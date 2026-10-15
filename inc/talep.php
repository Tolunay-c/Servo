<?php
/**
 * Talep formları: ortak uç nokta, korumalı doküman dizini ve süreli indirme bağlantıları.
 *
 * - POST servomotion/v1/talep : doküman talebi ve servis formları (destek, iade,
 *   kılavuz). Nonce, honeypot ve IP başına rate-limit (inc/formlar.php) ile korunur.
 *   Talep her zaman ekibe bildirilir. Doküman talebinde, Site Ayarları > Formlar'daki
 *   "Doküman talebi: gönderim" seçeneği "otomatik" ise ziyaretçiye 7 gün geçerli
 *   indirme bağlantıları e-postayla gider.
 * - "Talep formuyla" erişimli dokümanlar uploads/servo-korumali/ dizinine alınır;
 *   dizin doğrudan erişime kapalıdır (.htaccess) ve dosyalar yalnızca imzalı,
 *   süreli bağlantıyla (?servo-indir=...) verilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Korumalı dizinin uploads altındaki adı.
 */
const SERVO_KORUMALI_DIZIN = 'servo-korumali';

/**
 * İndirme bağlantısının geçerlilik süresi (saniye): 7 gün.
 *
 * @return int
 */
function servo_talep_gecerlilik() {
	return (int) apply_filters( 'servo_talep_gecerlilik', 7 * DAY_IN_SECONDS );
}

/**
 * Doküman talebi otomatik mi gönderilecek?
 *
 * @return bool
 */
function servo_talep_otomatik() {
	return 'ekip' !== servo_option( 'talep_gonderim', 'otomatik' );
}

/**
 * Formların altındaki varsayılan aydınlatma notu.
 */
const SERVO_KVKK_NOTU = 'Bu formda paylaştığınız bilgiler yalnızca talebinizi yanıtlamak için kullanılır ve e-posta ile ekibimize iletilir. Ayrıntılar için <a href="{kvkk_adresi}">KVKK aydınlatma metnimizi</a> inceleyin.';

const SERVO_KVKK_NOTU_EN = 'The details you share in this form are used only to answer your request and are sent to our team by email. See our <a href="{kvkk_adresi}">privacy notice</a> for details.';

/**
 * Formların altına basılan aydınlatma notunun güvenli HTML'i.
 *
 * Metin Site Ayarları > Formlar'dan düzenlenir; {kvkk_adresi} KVKK sayfasının adresiyle değişir.
 *
 * @return string
 */
function servo_kvkk_notu() {
	if ( servo_en() ) {
		// EN'de TR metne düşülmez: yalnızca EN Site Ayarları'ndaki not ya da EN varsayılan.
		$not = (string) get_option( 'options_en_form_kvkk_notu', '' );
		$not = '' === trim( $not ) ? SERVO_KVKK_NOTU_EN : $not;
	} else {
		$not = (string) servo_option( 'form_kvkk_notu', SERVO_KVKK_NOTU );

		if ( '' === trim( $not ) ) {
			$not = SERVO_KVKK_NOTU;
		}
	}

	$sayfa = servo_kayit_bul( servo_en() ? 'privacy' : 'kvkk' );
	$url   = $sayfa ? get_permalink( $sayfa ) : home_url( servo_en() ? '/en/privacy/' : '/kvkk/' );

	return servo_text( str_replace( '{kvkk_adresi}', esc_url( $url ), $not ) );
}

/* ------------------------------------------------------------------
 * Korumalı dizin
 * ---------------------------------------------------------------- */

/**
 * Korumalı dizinin sunucu yolu (sonunda eğik çizgi).
 *
 * @return string
 */
function servo_korumali_yol() {
	$u = wp_upload_dir();

	return trailingslashit( $u['basedir'] ) . SERVO_KORUMALI_DIZIN . '/';
}

/**
 * Korumalı dizini ve engelleme dosyalarını oluşturur.
 *
 * @return bool Dizin hazırsa true.
 */
function servo_korumali_hazirla() {
	$yol = servo_korumali_yol();

	if ( ! wp_mkdir_p( $yol ) ) {
		return false;
	}

	$htaccess = "# ServoMotion: bu dizindeki dosyalara doğrudan erişim kapalıdır.\n"
		. "# Dosyalar yalnızca imzalı ve süreli bağlantıyla (?servo-indir=) verilir.\n"
		. "Options -Indexes\n"
		. "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n"
		. "<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n"
		. "<IfModule mod_headers.c>\nHeader set X-Robots-Tag \"noindex, nofollow, noarchive\"\n</IfModule>\n";

	if ( ! file_exists( $yol . '.htaccess' ) || file_get_contents( $yol . '.htaccess' ) !== $htaccess ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		file_put_contents( $yol . '.htaccess', $htaccess ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
	}
	if ( ! file_exists( $yol . 'index.php' ) ) {
		file_put_contents( $yol . 'index.php', "<?php\n// Sessizlik altındır.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
	}

	return true;
}

/**
 * Ek korumalı dizinde mi?
 *
 * @param int $ek Ek ID'si.
 * @return bool
 */
function servo_ek_korumali_mi( $ek ) {
	$rel = (string) get_post_meta( $ek, '_wp_attached_file', true );

	return 0 === strpos( $rel, SERVO_KORUMALI_DIZIN . '/' );
}

/**
 * Eki korumalı dizine alır veya normal Medya klasörüne geri verir.
 *
 * Dosya fiziksel olarak taşınır, ek kaydı yeni yolu gösterir; PDF önizleme
 * küçük resimleri (açık dizinde kalmasın diye) silinir.
 *
 * @param int  $ek   Ek ID'si.
 * @param bool $koru Korumalı dizine alınsın mı?
 * @return bool Değişiklik yapıldıysa true.
 */
function servo_ek_tasi( $ek, $koru ) {
	if ( $koru === servo_ek_korumali_mi( $ek ) ) {
		return false;
	}

	$u    = wp_upload_dir();
	$rel  = (string) get_post_meta( $ek, '_wp_attached_file', true );
	$eski = trailingslashit( $u['basedir'] ) . $rel;

	if ( '' === $rel || ! file_exists( $eski ) ) {
		return false;
	}

	if ( $koru ) {
		if ( ! servo_korumali_hazirla() ) {
			return false;
		}
		$hedef_dizin = SERVO_KORUMALI_DIZIN;
	} else {
		$hedef_dizin = ltrim( (string) $u['subdir'], '/' );
		wp_mkdir_p( trailingslashit( $u['basedir'] ) . $hedef_dizin );
	}

	$ad   = wp_unique_filename( trailingslashit( $u['basedir'] ) . $hedef_dizin, basename( $rel ) );
	$yeni = trailingslashit( $hedef_dizin ) . $ad;

	if ( ! rename( $eski, trailingslashit( $u['basedir'] ) . $yeni ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename
		return false;
	}

	// PDF önizlemeleri ve diğer türevler.
	$meta = wp_get_attachment_metadata( $ek );
	if ( is_array( $meta ) && ! empty( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $boyut ) {
			$dosya = trailingslashit( dirname( $eski ) ) . $boyut['file'];
			if ( file_exists( $dosya ) ) {
				wp_delete_file( $dosya );
			}
		}
		unset( $meta['sizes'] );
		wp_update_attachment_metadata( $ek, $meta );
	}

	update_post_meta( $ek, '_wp_attached_file', $yeni );
	wp_update_post(
		array(
			'ID'   => $ek,
			'guid' => trailingslashit( $u['baseurl'] ) . $yeni,
		)
	);

	return true;
}

/**
 * Doküman kaydındaki erişim seçimine göre dosyayı korumalı dizine alır / geri verir.
 *
 * @param int $id Doküman kaydı.
 * @return bool Dosya taşındıysa true.
 */
function servo_dokuman_korumayi_uygula( $id ) {
	$ek = (int) servo_field( 'dosya', $id, 0 );

	if ( ! $ek || 'attachment' !== get_post_type( $ek ) ) {
		return false;
	}

	return servo_ek_tasi( $ek, 'formla' === servo_field( 'erisim', $id, 'acik' ) );
}

/**
 * Panelde doküman kaydedilince koruma uygulanır.
 *
 * @param int|string $post_id Kayıt.
 */
function servo_dokuman_kaydedildi( $post_id ) {
	if ( is_numeric( $post_id ) && 'dokuman' === get_post_type( (int) $post_id ) ) {
		servo_dokuman_korumayi_uygula( (int) $post_id );
	}
}
add_action( 'acf/save_post', 'servo_dokuman_kaydedildi', 20 );

/**
 * Korumalı dizindeki ekleri REST Medya listesinden çıkarır.
 *
 * @param array $args Sorgu argümanları.
 * @return array
 */
function servo_rest_korumali_gizle( $args ) {
	$args['meta_query'][] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'key'     => '_wp_attached_file',
		'value'   => SERVO_KORUMALI_DIZIN . '/',
		'compare' => 'NOT LIKE',
	);

	return $args;
}
add_filter( 'rest_attachment_query', 'servo_rest_korumali_gizle' );

/* ------------------------------------------------------------------
 * Süreli indirme bağlantısı
 * ---------------------------------------------------------------- */

/**
 * İmzalı token üretir.
 *
 * @param int $dokuman Doküman kaydı.
 * @param int $bitis   Geçerlilik bitişi (unix zamanı).
 * @return string
 */
function servo_indir_token( $dokuman, $bitis ) {
	$yuk = (int) $dokuman . '.' . (int) $bitis;
	$imza = substr( hash_hmac( 'sha256', $yuk, wp_salt( 'auth' ) ), 0, 32 );

	return rtrim( strtr( base64_encode( $yuk ), '+/', '-_' ), '=' ) . '.' . $imza; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
}

/**
 * Süreli indirme bağlantısı.
 *
 * @param int      $dokuman Doküman kaydı.
 * @param int|null $bitis   Bitiş zamanı (boşsa şimdi + geçerlilik süresi).
 * @return string
 */
function servo_indir_url( $dokuman, $bitis = null ) {
	$bitis = null === $bitis ? time() + servo_talep_gecerlilik() : (int) $bitis;

	return add_query_arg( 'servo-indir', servo_indir_token( $dokuman, $bitis ), home_url( '/' ) );
}

/**
 * Token'ı doğrular.
 *
 * @param string $token Token.
 * @return array{0:string,1:int,2:int} Durum (ok|imza|sure), doküman ID, bitiş.
 */
function servo_indir_dogrula( $token ) {
	$parca = explode( '.', (string) $token );

	if ( 2 !== count( $parca ) ) {
		return array( 'imza', 0, 0 );
	}

	$yuk = base64_decode( strtr( $parca[0], '-_', '+/' ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

	if ( ! is_string( $yuk ) || ! preg_match( '/^(\d+)\.(\d+)$/', $yuk, $m ) ) {
		return array( 'imza', 0, 0 );
	}

	$beklenen = substr( hash_hmac( 'sha256', $yuk, wp_salt( 'auth' ) ), 0, 32 );

	if ( ! hash_equals( $beklenen, $parca[1] ) ) {
		return array( 'imza', 0, 0 );
	}

	return array( (int) $m[2] < time() ? 'sure' : 'ok', (int) $m[1], (int) $m[2] );
}

/**
 * ?servo-indir=TOKEN isteğini karşılar: imza ve süre doğruysa dosyayı verir.
 */
function servo_indir_istegi() {
	if ( ! isset( $_GET['servo-indir'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$token = sanitize_text_field( wp_unslash( $_GET['servo-indir'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	list( $durum, $id ) = servo_indir_dogrula( $token );

	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow, noarchive' );

	$sayfa = servo_kayit_bul( 'dokumanlar', 'page', 'tr' );
	$geri  = $sayfa ? '<p><a href="' . esc_url( get_permalink( $sayfa ) . '#talep' ) . '">' . esc_html__( 'Dokümanı yeniden talep edin', 'servomotion' ) . '</a></p>' : '';

	if ( 'ok' !== $durum ) {
		$baslik = 'sure' === $durum ? __( 'Bağlantının süresi doldu', 'servomotion' ) : __( 'Geçersiz bağlantı', 'servomotion' );
		$metin  = 'sure' === $durum
			? __( 'Bu indirme bağlantısı yalnızca 7 gün geçerlidir ve süresi dolmuştur.', 'servomotion' )
			: __( 'Bu indirme bağlantısı geçerli değil.', 'servomotion' );

		wp_die( '<p>' . esc_html( $metin ) . '</p>' . $geri, esc_html( $baslik ), array( 'response' => 'sure' === $durum ? 410 : 403 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	$ek = 'dokuman' === get_post_type( $id ) && 'publish' === get_post_status( $id ) && 'formla' === servo_field( 'erisim', $id, 'acik' )
		? (int) servo_field( 'dosya', $id, 0 )
		: 0;

	$dosya = $ek ? get_attached_file( $ek ) : '';

	if ( ! $dosya || ! is_readable( $dosya ) ) {
		wp_die( '<p>' . esc_html__( 'Doküman bulunamadı.', 'servomotion' ) . '</p>' . $geri, esc_html__( 'Doküman bulunamadı', 'servomotion' ), array( 'response' => 404 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	$tur = wp_check_filetype( $dosya );

	header( 'Content-Type: ' . ( $tur['type'] ? $tur['type'] : 'application/octet-stream' ) );
	header( 'Content-Disposition: attachment; filename="' . rawurlencode( basename( $dosya ) ) . '"' );
	header( 'Content-Length: ' . filesize( $dosya ) );
	header( 'X-Content-Type-Options: nosniff' );

	while ( ob_get_level() ) {
		ob_end_clean();
	}
	readfile( $dosya ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	exit;
}
add_action( 'init', 'servo_indir_istegi', 1 );

/* ------------------------------------------------------------------
 * Form şeması (blok şablonu ve uç nokta aynı tanımı kullanır)
 * ---------------------------------------------------------------- */

/**
 * Bir form türünün alanları.
 *
 * Her satır: 'cift' (iki alan yan yana, .two) veya 'tek'. Alan adları (ad)
 * tasarımın mailto biçimiyle aynıdır; e-posta istemcisi yedeği bu adları kullanır.
 *
 * @param string $tur dokuman | destek | iade | kilavuz.
 * @return array{onek:string,satirlar:array,konu:string}|null
 */
function servo_talep_sema( $tur ) {
	$f = function ( $ad, $etiket, $id, $tip = 'text', $zorunlu = false, $ek = array() ) {
		return array_merge(
			array(
				'ad'      => $ad,
				'etiket'  => $etiket,
				'id'      => $id,
				'tip'     => $tip,
				'zorunlu' => $zorunlu,
			),
			$ek
		);
	};

	switch ( $tur ) {
		case 'dokuman':
			return array(
				'onek'     => 'Doküman talebi',
				'satirlar' => array(
					array( 'dokumanlar' ),
					array( 'cift', $f( 'Firma', __( 'Firma', 'servomotion' ), 'v-firma', 'text', true ), $f( 'Ilgili kisi', __( 'İlgili kişi', 'servomotion' ), 'v-kisi', 'text', true ) ),
					array( 'cift', $f( 'E-posta', __( 'E-posta', 'servomotion' ), 'v-mail', 'email', true ), $f( 'Telefon', __( 'Telefon', 'servomotion' ), 'v-tel', 'tel' ) ),
					array( 'tek', $f( 'Uygulama', __( 'Uygulama / hat', 'servomotion' ), 'v-uyg', 'text', false, array( 'placeholder' => __( 'örn. bobin dilimleme, nonwoven kesim', 'servomotion' ) ) ) ),
				),
			);

		case 'destek':
			return array(
				'onek'     => 'Destek talebi',
				'satirlar' => array(
					array( 'cift', $f( 'Firma', __( 'Firma', 'servomotion' ), 'd-firma', 'text', true ), $f( 'Ilgili kisi', __( 'İlgili kişi', 'servomotion' ), 'd-kisi', 'text', true ) ),
					array( 'cift', $f( 'E-posta', __( 'E-posta', 'servomotion' ), 'd-mail', 'email', true ), $f( 'Telefon', __( 'Telefon', 'servomotion' ), 'd-tel', 'tel' ) ),
					array(
						'cift',
						$f( 'Sistem', __( 'Sistem', 'servomotion' ), 'd-sistem', 'select', false, array( 'secenekler' => array( '', 'Core Precisor', 'Shuttle Precisor', __( 'Bilmiyorum', 'servomotion' ) ) ) ),
						$f( 'Seri no', __( 'Seri numarası', 'servomotion' ), 'd-seri', 'text', false, array( 'placeholder' => __( 'gövde etiketinde', 'servomotion' ) ) ),
					),
					array( 'tek', $f( 'Oncelik', __( 'Öncelik', 'servomotion' ), 'd-oncelik', 'select', false, array( 'secenekler' => array( __( 'Hat duruyor', 'servomotion' ), __( 'Hat çalışıyor, performans etkileniyor', 'servomotion' ), __( 'Bilgi / soru', 'servomotion' ) ), 'bos_yok' => true ) ) ),
					array( 'tek', $f( 'Belirti', __( 'Belirti', 'servomotion' ), 'd-konu', 'textarea', true, array( 'placeholder' => __( 'Ne zaman başladı, hangi reçetede görülüyor, panelde ne yazıyor', 'servomotion' ) ) ) ),
				),
			);

		case 'iade':
			return array(
				'onek'     => 'İade talebi',
				'satirlar' => array(
					array( 'cift', $f( 'Firma', __( 'Firma', 'servomotion' ), 'i-firma', 'text', true ), $f( 'Ilgili kisi', __( 'İlgili kişi', 'servomotion' ), 'i-kisi', 'text', true ) ),
					array( 'cift', $f( 'E-posta', __( 'E-posta', 'servomotion' ), 'i-mail', 'email', true ), $f( 'Telefon', __( 'Telefon', 'servomotion' ), 'i-tel', 'tel' ) ),
					array(
						'cift',
						$f( 'Sistem', __( 'Sistem', 'servomotion' ), 'i-sistem', 'select', false, array( 'secenekler' => array( '', 'Core Precisor', 'Shuttle Precisor' ) ) ),
						$f( 'Seri no', __( 'Seri numarası', 'servomotion' ), 'i-seri', 'text' ),
					),
					array( 'tek', $f( 'Bilesen', __( 'Gönderilecek bileşen', 'servomotion' ), 'i-bilesen', 'text', true, array( 'placeholder' => __( 'örn. lazer modülü, araba, tahrik bileşeni', 'servomotion' ) ) ) ),
					array( 'tek', $f( 'Ariza notu', __( 'Arıza notu', 'servomotion' ), 'i-not', 'textarea', true, array( 'placeholder' => __( 'Belirti, ne zaman başladı, hangi reçetede görülüyor', 'servomotion' ) ) ) ),
				),
			);

		case 'kilavuz':
			return array(
				'onek'     => 'Kılavuz talebi',
				'satirlar' => array(
					array( 'cift', $f( 'Firma', __( 'Firma', 'servomotion' ), 'k-firma', 'text', true ), $f( 'Ilgili kisi', __( 'İlgili kişi', 'servomotion' ), 'k-kisi', 'text', true ) ),
					array( 'cift', $f( 'E-posta', __( 'E-posta', 'servomotion' ), 'k-mail', 'email', true ), $f( 'Seri no', __( 'Seri numarası', 'servomotion' ), 'k-seri', 'text', false, array( 'placeholder' => __( 'gövde etiketinde', 'servomotion' ) ) ) ),
					array(
						'tek',
						$f(
							'Dokuman',
							__( 'İhtiyaç duyulan doküman', 'servomotion' ),
							'k-dok',
							'select',
							false,
							array(
								'secenekler' => array( __( 'Kullanım kılavuzu', 'servomotion' ), __( 'Elektrik ve bağlantı şeması', 'servomotion' ), __( 'Montaj çizimi', 'servomotion' ), __( 'Kalibrasyon ve doğrulama kaydı', 'servomotion' ), __( 'Tümü', 'servomotion' ) ),
								'bos_yok'    => true,
							)
						),
					),
					array( 'tek', $f( 'Not', __( 'Not', 'servomotion' ), 'k-not', 'textarea', false, array( 'placeholder' => __( 'Hangi sistem, hangi hat, özel bir ihtiyaç var mı', 'servomotion' ) ) ) ),
				),
			);
	}

	return null;
}

/**
 * Bir türün düz alan listesi.
 *
 * @param string $tur Tür.
 * @return array<string,array> ad => alan.
 */
function servo_talep_alanlari( $tur ) {
	$sema = servo_talep_sema( $tur );
	$out  = array();

	foreach ( $sema ? $sema['satirlar'] : array() as $satir ) {
		foreach ( array_slice( $satir, 1 ) as $alan ) {
			$out[ $alan['ad'] ] = $alan;
		}
	}

	return $out;
}

/**
 * Talep formunda sunulan dokümanlar (Talep formuyla erişilen, dosyası olan).
 *
 * "Her iki dil" (ortak) dokümanlar (ör. sertifika) iki dilin listesinde de çıkar.
 *
 * @param string $dil tr | en.
 * @return array<int,array{id:int,kod:string,etiket:string}>
 */
function servo_talep_dokumanlari( $dil = 'tr' ) {
	$yazilar = get_posts(
		array(
			'post_type'      => 'dokuman',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'ID'         => 'ASC',
			),
			'fields'         => 'ids',
		)
	);
	$out     = array();

	foreach ( $yazilar as $id ) {
		$d = servo_field( 'dil', $id, 'tr' );
		if ( 'formla' !== servo_field( 'erisim', $id, 'acik' ) || ( $dil !== $d && 'ortak' !== $d ) || ! servo_field( 'dosya', $id ) ) {
			continue;
		}
		$kod = (string) servo_field( 'talep_kodu', $id, '' );
		if ( '' === $kod ) {
			continue;
		}
		$etiket = (string) servo_field( 'talep_etiketi', $id, '' );
		if ( 'en' === $dil && 'ortak' === $d && servo_field( 'talep_etiketi_en', $id, '' ) ) {
			$etiket = (string) servo_field( 'talep_etiketi_en', $id, '' );
		}
		$out[]  = array(
			'id'     => (int) $id,
			'kod'    => $kod,
			'etiket' => '' !== $etiket ? $etiket : get_the_title( $id ),
			'ortak'  => 'ortak' === $d,
		);
	}

	// Her iki dilde geçerli dokümanlar (sertifika) listenin sonunda.
	usort(
		$out,
		function ( $a, $b ) {
			return (int) $a['ortak'] <=> (int) $b['ortak'];
		}
	);

	return $out;
}

/**
 * Talep uç noktasının adresi (nonce ile).
 *
 * @return string
 */
function servo_talep_adresi() {
	return add_query_arg( '_wpnonce', wp_create_nonce( 'wp_rest' ), rest_url( SERVO_REST_NS . '/talep' ) );
}

/* ------------------------------------------------------------------
 * REST uç noktası
 * ---------------------------------------------------------------- */

/**
 * Talep uç noktasını kaydeder.
 */
function servo_talep_rest_kayit() {
	register_rest_route(
		SERVO_REST_NS,
		'/talep',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'servo_talep_gonder',
			'permission_callback' => 'servo_form_nonce',
		)
	);
}
add_action( 'rest_api_init', 'servo_talep_rest_kayit' );

/**
 * Talebi işler: ekibe bildirir; doküman talebinde süreli bağlantıları hazırlar ve (ayara göre) ziyaretçiye gönderir.
 *
 * @param WP_REST_Request $request İstek (JSON: tur, alanlar, dokumanlar, ingilizce, web, kaynak).
 * @return WP_REST_Response|WP_Error
 */
function servo_talep_gonder( $request ) {
	if ( ! servo_form_sinir( 'talep' ) ) {
		return new WP_Error( 'servo_sinir', __( 'Çok fazla deneme yapıldı. Lütfen biraz sonra tekrar deneyin.', 'servomotion' ), array( 'status' => 429 ) );
	}

	$p = $request->get_json_params();
	$p = is_array( $p ) ? $p : (array) $request->get_body_params();

	// Formun bulunduğu sayfanın dili: ziyaretçiye giden metinler bu dilde, ekibe giden bildirim Türkçe yazılır.
	$dil     = isset( $p['lang'] ) && 'en' === $p['lang'] ? 'en' : 'tr';
	$locale  = 'en' === $dil ? 'en_GB' : 'tr_TR';
	$degisti = switch_to_locale( $locale );

	$sonuc = servo_talep_isle( $p, $dil );

	if ( $degisti ) {
		restore_previous_locale();
	}

	return $sonuc;
}

/**
 * Talebi işler (servo_talep_gonder'in gövdesi; ziyaretçi diline göre locale açıkken çalışır).
 *
 * @param array  $p   İstek verisi.
 * @param string $dil Form sayfasının dili (tr | en).
 * @return WP_REST_Response|WP_Error
 */
function servo_talep_isle( $p, $dil ) {
	// Honeypot doluysa bot kabul edilir: başarılı görünür, hiçbir şey gönderilmez.
	if ( '' !== trim( (string) ( isset( $p['web'] ) ? $p['web'] : '' ) ) ) {
		return rest_ensure_response( array( 'ok' => true ) );
	}

	$tur    = isset( $p['tur'] ) ? sanitize_key( $p['tur'] ) : '';
	$sema   = servo_talep_sema( $tur );
	$alici  = servo_form_alici();
	$gelen  = isset( $p['alanlar'] ) && is_array( $p['alanlar'] ) ? $p['alanlar'] : array();

	if ( ! $sema ) {
		return new WP_Error( 'servo_tur', __( 'Geçersiz form.', 'servomotion' ), array( 'status' => 400 ) );
	}
	if ( '' === $alici ) {
		return new WP_Error( 'servo_alici', __( 'Form henüz bir adrese bağlı değil.', 'servomotion' ), array( 'status' => 503 ) );
	}

	// Alanlar: sadece şemadaki adlar kabul edilir.
	$deger = array();
	foreach ( servo_talep_alanlari( $tur ) as $ad => $alan ) {
		$v = isset( $gelen[ $ad ] ) ? $gelen[ $ad ] : '';
		$v = 'textarea' === $alan['tip'] ? sanitize_textarea_field( (string) $v ) : sanitize_text_field( (string) $v );
		$v = mb_substr( $v, 0, 'textarea' === $alan['tip'] ? 4000 : 300 );

		if ( 'select' === $alan['tip'] && '' !== $v && ! in_array( $v, $alan['secenekler'], true ) ) {
			$v = '';
		}
		if ( $alan['zorunlu'] && '' === trim( $v ) ) {
			/* translators: %s: alan adı */
			return new WP_Error( 'servo_eksik', sprintf( __( '"%s" alanı gerekli.', 'servomotion' ), $alan['etiket'] ), array( 'status' => 400 ) );
		}
		$deger[ $ad ] = $v;
	}

	$mail = isset( $deger['E-posta'] ) ? $deger['E-posta'] : '';
	if ( ! is_email( $mail ) ) {
		return new WP_Error( 'servo_mail_adres', __( 'Geçerli bir e-posta adresi girin.', 'servomotion' ), array( 'status' => 400 ) );
	}

	$baglantilar = array();
	$istenen     = array();

	if ( 'dokuman' === $tur ) {
		$kodlar = isset( $p['dokumanlar'] ) && is_array( $p['dokumanlar'] ) ? array_map( 'sanitize_key', $p['dokumanlar'] ) : array();
		$diger  = ! empty( $p['diger_dil'] ) || ! empty( $p['ingilizce'] );
		$digeri = 'en' === $dil ? 'tr' : 'en';

		foreach ( array_merge( servo_talep_dokumanlari( $dil ), $diger ? servo_talep_dokumanlari( $digeri ) : array() ) as $d ) {
			if ( ! in_array( $d['kod'], $kodlar, true ) ) {
				continue;
			}
			$dok_dil = servo_field( 'dil', $d['id'], 'tr' );
			$ek      = 'ortak' !== $dok_dil && $dok_dil !== $dil ? ' (' . strtoupper( $dok_dil ) . ')' : '';
			// Aynı kodun ortak sürümü iki listede de çıkabilir; yinelenmesin.
			if ( isset( $baglantilar[ $d['id'] ] ) ) {
				continue;
			}
			$istenen[]                = $d['etiket'] . $ek;
			$baglantilar[ $d['id'] ] = array( $d['etiket'] . $ek, servo_indir_url( $d['id'] ) );
		}
		$baglantilar = array_values( $baglantilar );

		if ( ! $baglantilar ) {
			return new WP_Error( 'servo_dokuman', __( 'En az bir doküman seçin.', 'servomotion' ), array( 'status' => 400 ) );
		}
	}

	$otomatik = 'dokuman' === $tur && servo_talep_otomatik();
	$firma    = isset( $deger['Firma'] ) ? $deger['Firma'] : '';

	// Ekibe bildirim: her zaman Türkçe.
	$tr_degisti = switch_to_locale( 'tr_TR' );

	$satirlar = array();
	foreach ( servo_talep_alanlari( $tur ) as $ad => $alan ) {
		if ( '' !== $deger[ $ad ] ) {
			$satirlar[] = $alan['etiket'] . ': ' . $deger[ $ad ];
		}
	}
	if ( $istenen ) {
		$satirlar[] = __( 'İstenen dokümanlar', 'servomotion' ) . ': ' . implode( ', ', $istenen );
	}

	$metin = implode( "\n", $satirlar ) . "\n";

	if ( 'dokuman' === $tur ) {
		$metin .= "\n" . ( $otomatik
			? __( 'Gönderim: otomatik. Dokümanların 7 gün geçerli indirme bağlantıları ziyaretçinin e-posta adresine gönderildi.', 'servomotion' )
			: __( 'Gönderim: ziyaretçiye otomatik gönderilmedi. Aşağıdaki 7 gün geçerli bağlantıları ziyaretçiye iletebilirsiniz.', 'servomotion' ) ) . "\n";
		foreach ( $baglantilar as $b ) {
			$metin .= '- ' . $b[0] . ': ' . $b[1] . "\n";
		}
	}

	$metin .= "\n----------------------------------------\n"
		. __( 'Gönderilen sayfa', 'servomotion' ) . ': ' . esc_url_raw( isset( $p['kaynak'] ) ? (string) $p['kaynak'] : '' ) . "\n"
		. ( 'en' === $dil ? 'Dil: English' . "\n" : '' )
		. __( 'Tarih', 'servomotion' ) . ': ' . wp_date( 'd.m.Y H:i' ) . "\n";

	$konu = $sema['onek'] . ' — ' . ( '' !== $firma ? $firma : $mail );

	$gitti = wp_mail( $alici, $konu, $metin, array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $mail ) );

	if ( $tr_degisti ) {
		restore_previous_locale();
	}

	if ( ! $gitti ) {
		return new WP_Error( 'servo_mail', __( 'Mail gönderilemedi.', 'servomotion' ), array( 'status' => 500 ) );
	}

	// Ziyaretçiye süreli bağlantılar (ziyaretçinin dilinde).
	if ( $otomatik ) {
		$bitis = time() + servo_talep_gecerlilik();
		$govde = __( 'Merhaba,', 'servomotion' ) . "\n\n"
			. __( 'Talep ettiğiniz dokümanların indirme bağlantıları aşağıdadır:', 'servomotion' ) . "\n\n";
		foreach ( $baglantilar as $b ) {
			$govde .= $b[0] . "\n" . $b[1] . "\n\n";
		}
		/* translators: %s: tarih ve saat */
		$govde .= sprintf( __( 'Bağlantılar %s tarihine kadar geçerlidir. Süre dolduktan sonra dokümanı yeniden talep edebilirsiniz.', 'servomotion' ), wp_date( 'd.m.Y H:i', $bitis ) ) . "\n\n"
			. __( 'Sorularınız için bu e-postayı yanıtlayabilirsiniz.', 'servomotion' ) . "\n\n"
			. wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . "\n";

		wp_mail(
			$mail,
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . ' — ' . __( 'talep ettiğiniz dokümanlar', 'servomotion' ),
			$govde,
			array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $alici )
		);
	}

	if ( 'dokuman' === $tur ) {
		$mesaj = $otomatik
			? __( 'Talebiniz alındı. Dokümanların indirme bağlantıları e-posta adresinize gönderildi; bağlantılar 7 gün geçerlidir.', 'servomotion' )
			: __( 'Talebiniz alındı. İstediğiniz dokümanları en kısa sürede e-posta ile ileteceğiz.', 'servomotion' );
	} else {
		$mesaj = __( 'Talebiniz alındı. En kısa sürede size dönüş yapacağız.', 'servomotion' );
	}

	return rest_ensure_response(
		array(
			'ok'    => true,
			'mesaj' => $mesaj,
		)
	);
}
