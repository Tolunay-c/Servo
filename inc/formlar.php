<?php
/**
 * Formlar: uygulama özeti gönderimi, bülten uç noktası ve geliştirme modu mail kaydı.
 *
 * Uç noktalar (servomotion/v1):
 * - POST /uygulama-ozeti : iletisim-gonder.js'in gönderdiği özeti wp_mail ile
 *   Site Ayarları > Formlar'daki adrese yollar. Nonce, honeypot ve IP başına
 *   rate-limit ile korunur.
 * - POST /bulten         : kayıtlıdır ama henüz bir listeye bağlı değildir,
 *   501 döner. Ön yüz (bulten.js) bu uç noktayı kullanmaz.
 *
 * Tasarımın JS'i değişmez: uç nokta adresi wp_add_inline_script ile
 * window.SERVOMOTION_FORM_ENDPOINT kancasına verilir (inc/enqueue.php).
 * Alıcı adresi yoksa adres verilmez ve tasarımın e-posta/WhatsApp akışı çalışır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST ad alanı.
 */
const SERVO_REST_NS = 'servomotion/v1';

/**
 * Uygulama özetinin gönderileceği adres.
 *
 * Site Ayarları > Formlar boşsa İletişim sekmesindeki e-posta kullanılır.
 *
 * @return string Geçerli adres yoksa boş metin.
 */
function servo_form_alici() {
	$adres = (string) servo_option( 'form_eposta', '' );

	if ( ! is_email( $adres ) ) {
		$adres = (string) servo_option( 'eposta', '' );
	}

	return is_email( $adres ) ? $adres : '';
}

/**
 * Uygulama özeti uç noktasının adresini (nonce ile) döndürür.
 *
 * @return string
 */
function servo_form_adresi() {
	return add_query_arg( '_wpnonce', wp_create_nonce( 'wp_rest' ), rest_url( SERVO_REST_NS . '/uygulama-ozeti' ) );
}

/**
 * Tasarımın SERVOMOTION_FORM_ENDPOINT kancasını dolduran satır içi script.
 *
 * iletisim-gonder.js adresi bir kez okur ve fetch() anında metne çevirir.
 * Adres, honeypot alanının değerini gönderim anında sorguya ekleyen bir
 * nesne olarak verilir; böylece tasarımın JS'i değişmeden honeypot çalışır.
 *
 * @return string Alıcı adresi yoksa boş metin.
 */
function servo_form_script() {
	if ( '' === servo_form_alici() ) {
		return '';
	}

	return 'window.SERVOMOTION_FORM_ENDPOINT={toString:function(){var h=document.getElementById("f-web");'
		. 'return ' . wp_json_encode( servo_form_adresi() ) . '+(h&&h.value?"&web="+encodeURIComponent(h.value):"");}};';
}

/**
 * Uygulama özeti formundaki honeypot alanını basar.
 *
 * Ekran dışındadır; insanlar görmez, form dolduran botlar doldurur.
 * Sadece gönderim açıkken (alıcı adresi varken) basılır.
 */
function servo_form_honeypot() {
	if ( '' === servo_form_alici() ) {
		return;
	}

	echo '<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden">'
		. '<label for="f-web">' . esc_html__( 'Web sitesi', 'servomotion' ) . '</label>'
		. '<input id="f-web" type="text" tabindex="-1" autocomplete="off"></div>';
}

/**
 * Uç noktaları kaydeder.
 */
function servo_rest_kayit() {
	register_rest_route(
		SERVO_REST_NS,
		'/uygulama-ozeti',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'servo_ozet_gonder',
			'permission_callback' => 'servo_form_nonce',
			'args'                => array(
				'ozet'   => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_textarea_field',
				),
				'firma'  => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'kisi'   => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'kaynak' => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
				),
				'web'    => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);

	register_rest_route(
		SERVO_REST_NS,
		'/bulten',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'servo_bulten_kayit',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'servo_rest_kayit' );

/**
 * Nonce'u doğrular.
 *
 * Giriş yapmış kullanıcılarda WordPress _wpnonce'u zaten doğrular; ziyaretçiler
 * için burada doğrulanır. Böylece uç noktaya sadece sayfadan gelen istek ulaşır.
 *
 * @param WP_REST_Request $request İstek.
 * @return true|WP_Error
 */
function servo_form_nonce( $request ) {
	if ( wp_verify_nonce( (string) $request->get_param( '_wpnonce' ), 'wp_rest' ) ) {
		return true;
	}

	return new WP_Error( 'servo_nonce', __( 'Oturum süresi doldu. Sayfayı yenileyip tekrar deneyin.', 'servomotion' ), array( 'status' => 403 ) );
}

/**
 * IP başına gönderim sınırını uygular.
 *
 * Varsayılan: 15 dakikada 5 istek (servo_form_sinir filtresiyle değişir).
 * IP adresi saklanmaz, sadece özeti (hash) geçici anahtar olarak kullanılır.
 *
 * @param string $form Form adı.
 * @return bool Sınır aşıldıysa false.
 */
function servo_form_sinir( $form ) {
	$sinir = apply_filters(
		'servo_form_sinir',
		array(
			'adet' => 5,
			'sure' => 15 * MINUTE_IN_SECONDS,
		),
		$form
	);

	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'servo_sinir_' . $form . '_' . md5( $ip . wp_salt( 'nonce' ) );
	$kayit = get_transient( $key );
	$simdi = time();

	if ( ! is_array( $kayit ) || $kayit['bitis'] <= $simdi ) {
		$kayit = array(
			'adet'  => 0,
			'bitis' => $simdi + (int) $sinir['sure'],
		);
	}

	++$kayit['adet'];
	set_transient( $key, $kayit, max( 1, $kayit['bitis'] - $simdi ) );

	return $kayit['adet'] <= (int) $sinir['adet'];
}

/**
 * Uygulama özetini mail ile gönderir.
 *
 * Tasarımın JS'i yanıt gövdesini okumaz, sadece başarılı olup olmadığına bakar.
 *
 * @param WP_REST_Request $request İstek.
 * @return WP_REST_Response|WP_Error
 */
function servo_ozet_gonder( $request ) {
	if ( ! servo_form_sinir( 'ozet' ) ) {
		return new WP_Error( 'servo_sinir', __( 'Çok fazla deneme yapıldı. Lütfen biraz sonra tekrar deneyin.', 'servomotion' ), array( 'status' => 429 ) );
	}

	// Honeypot doluysa bot kabul edilir: başarılı görünür ama mail gönderilmez.
	if ( '' !== trim( (string) $request->get_param( 'web' ) ) ) {
		return rest_ensure_response( array( 'ok' => true ) );
	}

	$alici = servo_form_alici();
	$ozet  = trim( mb_substr( (string) $request->get_param( 'ozet' ), 0, 5000 ) );
	$firma = mb_substr( (string) $request->get_param( 'firma' ), 0, 200 );
	$kisi  = mb_substr( (string) $request->get_param( 'kisi' ), 0, 200 );

	if ( '' === $alici ) {
		return new WP_Error( 'servo_alici', __( 'Form henüz bir adrese bağlı değil.', 'servomotion' ), array( 'status' => 503 ) );
	}

	// İletişim bilgisi olmayan özete dönüş yapılamaz.
	if ( '' === $ozet || '' === trim( $kisi ) ) {
		return new WP_Error( 'servo_eksik', __( 'İlgili kişi ve iletişim bilgisi gerekli.', 'servomotion' ), array( 'status' => 400 ) );
	}

	/* translators: %s: Firma adı veya site adı. */
	$konu  = sprintf( __( 'Uygulama özeti — %s', 'servomotion' ), '' !== $firma ? $firma : wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$metin = $ozet . "\n\n----------------------------------------\n"
		. __( 'Gönderilen sayfa', 'servomotion' ) . ': ' . (string) $request->get_param( 'kaynak' ) . "\n"
		. __( 'Tarih', 'servomotion' ) . ': ' . wp_date( 'd.m.Y H:i' ) . "\n";

	$basliklar = array( 'Content-Type: text/plain; charset=UTF-8' );

	// İletişim alanında e-posta varsa "Yanıtla" doğrudan kişiye gider.
	if ( preg_match( '/[^\s<>"]+@[^\s<>"]+\.[a-z]{2,}/i', $kisi, $m ) && is_email( $m[0] ) ) {
		$basliklar[] = 'Reply-To: ' . $m[0];
	}

	if ( ! wp_mail( $alici, $konu, $metin, $basliklar ) ) {
		return new WP_Error( 'servo_mail', __( 'Mail gönderilemedi.', 'servomotion' ), array( 'status' => 500 ) );
	}

	return rest_ensure_response( array( 'ok' => true ) );
}

/**
 * Bülten kaydı: henüz bir listeye bağlı değil.
 *
 * Servis bağlandığında sadece bu fonksiyon değişir.
 *
 * @return WP_Error
 */
function servo_bulten_kayit() {
	return new WP_Error( 'servo_bulten_bagli_degil', __( 'Bülten henüz bir listeye bağlı değil.', 'servomotion' ), array( 'status' => 501 ) );
}

/**
 * Geliştirme modu açık mı?
 *
 * Açıkken wp_mail ile gönderilen tüm site mailleri gönderilmez, veritabanına
 * kaydedilir ve Araçlar > Mail kayıtları ekranında görünür. Varsayılan olarak
 * sadece WP_ENVIRONMENT_TYPE 'local' veya 'development' olduğunda açıktır;
 * canlıda (production) kapalıdır. wp-config.php'de SERVO_MAIL_LOG sabitiyle
 * elle açılıp kapatılabilir.
 *
 * @return bool
 */
function servo_mail_log_acik() {
	if ( defined( 'SERVO_MAIL_LOG' ) ) {
		return (bool) SERVO_MAIL_LOG;
	}

	return in_array( wp_get_environment_type(), array( 'local', 'development' ), true );
}

/**
 * Mail kayıtlarının tutulduğu seçenek. Son 50 mail saklanır.
 */
const SERVO_MAIL_KAYITLARI = 'servo_mail_kayitlari';

/**
 * Geliştirme modunda maili göndermek yerine veritabanına kaydeder.
 *
 * @param null|bool $sonuc Kısa devre sonucu.
 * @param array     $atts  wp_mail argümanları.
 * @return null|bool
 */
function servo_mail_log( $sonuc, $atts ) {
	if ( null !== $sonuc || ! servo_mail_log_acik() ) {
		return $sonuc;
	}

	$liste = function ( $deger ) {
		return is_array( $deger ) ? implode( "\n", $deger ) : trim( (string) $deger );
	};

	$kayitlar   = (array) get_option( SERVO_MAIL_KAYITLARI, array() );
	$kayitlar[] = array(
		'tarih'     => wp_date( 'Y-m-d H:i:s' ),
		'kime'      => $liste( $atts['to'] ),
		'konu'      => (string) $atts['subject'],
		'basliklar' => $liste( $atts['headers'] ),
		'ekler'     => $liste( $atts['attachments'] ),
		'mesaj'     => (string) $atts['message'],
	);

	update_option( SERVO_MAIL_KAYITLARI, array_slice( $kayitlar, -50 ), false );

	return true;
}
add_filter( 'pre_wp_mail', 'servo_mail_log', 10, 2 );

/**
 * Geliştirme modunda Araçlar > Mail kayıtları ekranını ekler.
 */
function servo_mail_log_menu() {
	if ( servo_mail_log_acik() ) {
		add_management_page( __( 'Mail kayıtları', 'servomotion' ), __( 'Mail kayıtları', 'servomotion' ), 'manage_options', 'servo-mail-kayitlari', 'servo_mail_log_ekran' );
	}
}
add_action( 'admin_menu', 'servo_mail_log_menu' );

/**
 * Mail kayıtları ekranı (en yeni üstte).
 */
function servo_mail_log_ekran() {
	$kayitlar = array_reverse( (array) get_option( SERVO_MAIL_KAYITLARI, array() ) );

	echo '<div class="wrap"><h1>' . esc_html__( 'Mail kayıtları', 'servomotion' ) . '</h1>';
	echo '<p>' . esc_html__( 'Geliştirme modu açık: site mailleri gönderilmiyor, son 50 mail burada saklanıyor. Canlı ortamda (WP_ENVIRONMENT_TYPE production) bu mod kapalıdır.', 'servomotion' ) . '</p>';

	if ( ! $kayitlar ) {
		echo '<p><em>' . esc_html__( 'Henüz kayıt yok.', 'servomotion' ) . '</em></p></div>';
		return;
	}

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="servo_mail_log_temizle">';
	wp_nonce_field( 'servo_mail_log_temizle' );
	submit_button( __( 'Kayıtları temizle', 'servomotion' ), 'secondary', 'submit', false );
	echo '</form>';

	foreach ( $kayitlar as $k ) {
		echo '<div class="card" style="max-width:none">';
		echo '<h2 style="margin-top:.5em">' . esc_html( $k['konu'] ) . '</h2>';
		echo '<p><strong>' . esc_html__( 'Tarih', 'servomotion' ) . ':</strong> ' . esc_html( $k['tarih'] ) . '<br>';
		echo '<strong>' . esc_html__( 'Kime', 'servomotion' ) . ':</strong> ' . esc_html( $k['kime'] ) . '</p>';
		if ( '' !== $k['basliklar'] ) {
			echo '<pre style="white-space:pre-wrap;color:#646970">' . esc_html( $k['basliklar'] ) . '</pre>';
		}
		if ( '' !== $k['ekler'] ) {
			echo '<p><strong>' . esc_html__( 'Ekler', 'servomotion' ) . ':</strong> ' . esc_html( $k['ekler'] ) . '</p>';
		}
		echo '<pre style="white-space:pre-wrap;background:#f6f7f7;padding:12px">' . esc_html( $k['mesaj'] ) . '</pre>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Mail kayıtlarını siler.
 */
function servo_mail_log_temizle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bu işlem için yetkiniz yok.', 'servomotion' ) );
	}

	check_admin_referer( 'servo_mail_log_temizle' );
	delete_option( SERVO_MAIL_KAYITLARI );

	wp_safe_redirect( admin_url( 'tools.php?page=servo-mail-kayitlari' ) );
	exit;
}
add_action( 'admin_post_servo_mail_log_temizle', 'servo_mail_log_temizle' );

/**
 * Geliştirme modu açıkken Site Ayarları'nda uyarı gösterir.
 */
function servo_mail_log_uyari() {
	$ekran = get_current_screen();

	if ( ! servo_mail_log_acik() || ! $ekran || false === strpos( (string) $ekran->id, 'servo-site-ayarlari' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Geliştirme modu açık: site mailleri gönderilmiyor, kaydediliyor.', 'servomotion' ),
		esc_url( admin_url( 'tools.php?page=servo-mail-kayitlari' ) ),
		esc_html__( 'Mail kayıtları', 'servomotion' )
	);
}
add_action( 'admin_notices', 'servo_mail_log_uyari' );
