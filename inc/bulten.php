<?php
/**
 * Bülten aboneleri: kayıt tablosu, abonelikten çıkma bağlantısı ve yönetim ekranı.
 *
 * REST uç noktası `servomotion/v1/bulten` `servo_bulten_kayit()` ile (inc/formlar.php) e-postayı
 * buraya yazar. Mail gönderimi yoktur; her aboneye özel çıkış bağlantısı üretilir ve Araçlar >
 * Bülten aboneleri ekranında (ve CSV'de) görünür. Çıkış bağlantısı kaydı siler.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abone tablosunun adı.
 *
 * @return string
 */
function servo_bulten_tablo() {
	global $wpdb;

	return $wpdb->prefix . 'servo_bulten';
}

/**
 * Abone tablosunu (yoksa) oluşturur; bir kez çalışır.
 */
function servo_bulten_tablo_hazirla() {
	if ( '1' === (string) get_option( 'servo_bulten_tablo', '' ) ) {
		return;
	}

	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta(
		'CREATE TABLE ' . servo_bulten_tablo() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		eposta varchar(190) NOT NULL,
		dil varchar(5) NOT NULL DEFAULT 'tr',
		tarih datetime NOT NULL,
		anahtar char(32) NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY eposta (eposta),
		UNIQUE KEY anahtar (anahtar)
	) " . $wpdb->get_charset_collate() . ';'
	);

	update_option( 'servo_bulten_tablo', '1', true );
}
add_action( 'admin_init', 'servo_bulten_tablo_hazirla' );

/**
 * E-postayı aboneliğe ekler.
 *
 * @param string $eposta Geçerli e-posta.
 * @param string $dil    tr | en.
 * @return string yeni | var | hata.
 */
function servo_bulten_ekle( $eposta, $dil ) {
	global $wpdb;

	servo_bulten_tablo_hazirla();

	$eposta = strtolower( $eposta );
	$tablo  = servo_bulten_tablo();

	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	$var = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tablo} WHERE eposta = %s", $eposta ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $var ) {
		return 'var';
	}

	$eklendi = $wpdb->insert(
		$tablo,
		array(
			'eposta'  => $eposta,
			'dil'     => 'en' === $dil ? 'en' : 'tr',
			'tarih'   => current_time( 'mysql' ),
			'anahtar' => bin2hex( random_bytes( 16 ) ),
		),
		array( '%s', '%s', '%s', '%s' )
	);
	// phpcs:enable WordPress.DB.DirectDatabaseQuery

	if ( false === $eklendi ) {
		// Aynı anda gelen ikinci istek benzersiz anahtara takılır: zaten kayıtlı sayılır.
		return $wpdb->last_error && false !== stripos( $wpdb->last_error, 'duplicate' ) ? 'var' : 'hata';
	}

	return 'yeni';
}

/**
 * Bir abonenin abonelikten çıkma bağlantısı.
 *
 * @param string $anahtar Abone anahtarı.
 * @param string $dil     Abonenin dili.
 * @return string
 */
function servo_bulten_cikis_adresi( $anahtar, $dil ) {
	return add_query_arg( 'servo-bulten-cik', $anahtar, home_url( 'en' === $dil ? '/en/' : '/' ) );
}

/**
 * KVKK sayfasının adresi (sayfa diline göre).
 *
 * @return string
 */
function servo_bulten_kvkk_adresi() {
	$sayfa = servo_kayit_bul( servo_en() ? 'privacy' : 'kvkk' );

	return $sayfa ? get_permalink( $sayfa ) : home_url( servo_en() ? '/en/privacy/' : '/kvkk/' );
}

/**
 * Abonelikten çıkma isteği: GET onay sayfasını, POST silmeyi yapar (e-posta tarayıcıları bağlantıyı açıp aboneliği bitirmesin).
 */
function servo_bulten_cikis_istegi() {
	if ( ! isset( $_GET['servo-bulten-cik'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	global $wpdb;

	$anahtar = sanitize_text_field( wp_unslash( $_GET['servo-bulten-cik'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$tablo   = servo_bulten_tablo();
	$satir   = preg_match( '/^[a-f0-9]{32}$/', $anahtar )
		? $wpdb->get_row( $wpdb->prepare( "SELECT id, dil FROM {$tablo} WHERE anahtar = %s", $anahtar ) ) // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		: null;

	$locale  = $satir && 'en' === $satir->dil ? 'en_GB' : 'tr_TR';
	$degisti = switch_to_locale( $locale );

	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );

	$baslik = get_bloginfo( 'name' );
	$govde  = '';

	if ( ! $satir ) {
		$govde = '<p>' . esc_html__( 'Bu abonelikten çıkma bağlantısı geçerli değil ya da abonelik zaten sonlandırılmış.', 'servomotion' ) . '</p>';
	} elseif ( 'POST' === ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		$wpdb->delete( $tablo, array( 'id' => (int) $satir->id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$govde = '<p>' . esc_html__( 'Bülten aboneliğiniz sonlandırıldı.', 'servomotion' ) . '</p>';
	} else {
		$govde = '<p>' . esc_html__( 'Bülten aboneliğinizi sonlandırmak istiyor musunuz?', 'servomotion' ) . '</p>'
			. '<form method="post" action="' . esc_url( add_query_arg( 'servo-bulten-cik', $anahtar, home_url( 'en_GB' === $locale ? '/en/' : '/' ) ) ) . '">'
			. '<button type="submit" class="button button-primary">' . esc_html__( 'Abonelikten çık', 'servomotion' ) . '</button></form>';
	}

	if ( $degisti ) {
		restore_previous_locale();
	}

	wp_die( $govde, esc_html( $baslik ), array( 'response' => 200 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $govde yukarıda escape edildi.
}
add_action( 'init', 'servo_bulten_cikis_istegi', 1 );

/* ------------------------------------------------------------------
 * Yönetim ekranı: Araçlar > Bülten aboneleri
 * ---------------------------------------------------------------- */

/**
 * Menüyü ekler.
 */
function servo_bulten_menu() {
	add_management_page(
		__( 'Bülten aboneleri', 'servomotion' ),
		__( 'Bülten aboneleri', 'servomotion' ),
		'manage_options',
		'servo-bulten-aboneleri',
		'servo_bulten_sayfa'
	);
}
add_action( 'admin_menu', 'servo_bulten_menu' );

/**
 * Seçili (veya tek) aboneleri siler.
 */
function servo_bulten_sil_istegi() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'servomotion' ) );
	}
	check_admin_referer( 'servo_bulten_sil' );

	global $wpdb;

	$idler = isset( $_REQUEST['abone'] ) ? array_filter( array_map( 'absint', (array) wp_unslash( $_REQUEST['abone'] ) ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$adet  = 0;

	foreach ( $idler as $id ) {
		$adet += (int) $wpdb->delete( servo_bulten_tablo(), array( 'id' => $id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	wp_safe_redirect( add_query_arg( array( 'page' => 'servo-bulten-aboneleri', 'silindi' => $adet ), admin_url( 'tools.php' ) ) );
	exit;
}
add_action( 'admin_post_servo_bulten_sil', 'servo_bulten_sil_istegi' );

/**
 * Aboneleri CSV olarak indirir (UTF-8, Excel için BOM'lu).
 */
function servo_bulten_csv_istegi() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'servomotion' ) );
	}
	check_admin_referer( 'servo_bulten_csv' );

	global $wpdb;

	servo_bulten_tablo_hazirla();

	$tablo    = servo_bulten_tablo();
	$aboneler = $wpdb->get_results( "SELECT eposta, tarih, dil, anahtar FROM {$tablo} ORDER BY id DESC" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="bulten-aboneleri-' . gmdate( 'Y-m-d' ) . '.csv"' );

	$cikti = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fwrite( $cikti, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
	fputcsv( $cikti, array( 'eposta', 'tarih', 'dil', 'cikis_baglantisi' ) );

	foreach ( $aboneler as $a ) {
		// Formül olarak yorumlanabilecek hücreler kaçışlanır (CSV enjeksiyonu).
		$eposta = preg_match( '/^[=+\-@\t\r]/', $a->eposta ) ? "'" . $a->eposta : $a->eposta;
		fputcsv( $cikti, array( $eposta, $a->tarih, $a->dil, servo_bulten_cikis_adresi( $a->anahtar, $a->dil ) ) );
	}

	fclose( $cikti ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_servo_bulten_csv', 'servo_bulten_csv_istegi' );

/**
 * Araçlar > Bülten aboneleri sayfası.
 */
function servo_bulten_sayfa() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'servomotion' ) );
	}

	global $wpdb;

	servo_bulten_tablo_hazirla();

	$tablo   = servo_bulten_tablo();
	$sayfa   = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ); // phpcs:ignore WordPress.Security.NonceVerification
	$adet    = 50;
	$toplam  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tablo}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$aboneler = $wpdb->get_results( $wpdb->prepare( "SELECT id, eposta, tarih, dil, anahtar FROM {$tablo} ORDER BY id DESC LIMIT %d OFFSET %d", $adet, ( $sayfa - 1 ) * $adet ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	echo '<div class="wrap"><h1>' . esc_html__( 'Bülten aboneleri', 'servomotion' ) . '</h1>';

	if ( isset( $_GET['silindi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		/* translators: %d: silinen abone sayısı */
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( __( '%d abone silindi.', 'servomotion' ), absint( $_GET['silindi'] ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification
	}

	$csv = wp_nonce_url( admin_url( 'admin-post.php?action=servo_bulten_csv' ), 'servo_bulten_csv' );
	/* translators: %d: toplam abone sayısı */
	echo '<p>' . esc_html( sprintf( __( 'Toplam %d abone.', 'servomotion' ), $toplam ) ) . ' <a class="button" href="' . esc_url( $csv ) . '">' . esc_html__( 'CSV olarak dışa aktar', 'servomotion' ) . '</a></p>';
	echo '<p class="description">' . esc_html__( 'Bu ekran yalnızca kayıt tutar; mail gönderilmez. Her abonenin çıkış bağlantısı kendisine özeldir; gönderim yaptığınız e-postaya ekleyebilirsiniz.', 'servomotion' ) . '</p>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="servo_bulten_sil">';
	wp_nonce_field( 'servo_bulten_sil' );
	echo '<table class="widefat striped"><thead><tr><td class="check-column"></td><th>' . esc_html__( 'E-posta', 'servomotion' ) . '</th><th>' . esc_html__( 'Tarih', 'servomotion' ) . '</th><th>' . esc_html__( 'Dil', 'servomotion' ) . '</th><th>' . esc_html__( 'Çıkış bağlantısı', 'servomotion' ) . '</th><th></th></tr></thead><tbody>';

	if ( ! $aboneler ) {
		echo '<tr><td></td><td colspan="5">' . esc_html__( 'Henüz abone yok.', 'servomotion' ) . '</td></tr>';
	}

	foreach ( $aboneler as $a ) {
		$sil = wp_nonce_url( admin_url( 'admin-post.php?action=servo_bulten_sil&abone=' . (int) $a->id ), 'servo_bulten_sil' );
		echo '<tr><th class="check-column"><input type="checkbox" name="abone[]" value="' . (int) $a->id . '"></th>'
			. '<td>' . esc_html( $a->eposta ) . '</td>'
			. '<td>' . esc_html( mysql2date( 'd.m.Y H:i', $a->tarih ) ) . '</td>'
			. '<td>' . esc_html( strtoupper( $a->dil ) ) . '</td>'
			. '<td><input type="text" readonly class="large-text code" value="' . esc_attr( servo_bulten_cikis_adresi( $a->anahtar, $a->dil ) ) . '" onfocus="this.select()"></td>'
			. '<td><a href="' . esc_url( $sil ) . '" onclick="return confirm(\'' . esc_js( __( 'Bu abone silinsin mi?', 'servomotion' ) ) . '\')">' . esc_html__( 'Sil', 'servomotion' ) . '</a></td></tr>';
	}

	echo '</tbody></table>';

	if ( $aboneler ) {
		echo '<p><button type="submit" class="button" onclick="return confirm(\'' . esc_js( __( 'Seçili aboneler silinsin mi?', 'servomotion' ) ) . '\')">' . esc_html__( 'Seçilileri sil', 'servomotion' ) . '</button></p>';
	}
	echo '</form>';

	$sayfalar = (int) ceil( $toplam / $adet );
	if ( $sayfalar > 1 ) {
		echo '<p>' . wp_kses_post(
			paginate_links(
				array(
					'base'    => add_query_arg( 'paged', '%#%' ),
					'format'  => '',
					'current' => $sayfa,
					'total'   => $sayfalar,
				)
			)
		) . '</p>';
	}

	echo '</div>';
}
