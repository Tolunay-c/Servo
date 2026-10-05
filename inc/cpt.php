<?php
/**
 * Custom Post Type ve taxonomy tanımları.
 *
 * Kalıcı bağlantı yapısı /rehber/%postname%/ olduğundan tüm rewrite
 * kuralları with_front => false ile kaydedilir; aksi halde CPT adresleri
 * /rehber/ ile başlar.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * CPT etiketlerini üretir.
 *
 * @param string $singular Tekil ad.
 * @param string $plural   Çoğul ad.
 * @return array
 */
function servo_cpt_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'all_items'          => $plural,
		/* translators: %s: tekil içerik adı */
		'add_new_item'       => sprintf( __( 'Yeni %s ekle', 'servomotion' ), $singular ),
		/* translators: %s: tekil içerik adı */
		'edit_item'          => sprintf( __( '%s düzenle', 'servomotion' ), $singular ),
		/* translators: %s: tekil içerik adı */
		'new_item'           => sprintf( __( 'Yeni %s', 'servomotion' ), $singular ),
		/* translators: %s: tekil içerik adı */
		'view_item'          => sprintf( __( '%s görüntüle', 'servomotion' ), $singular ),
		/* translators: %s: çoğul içerik adı */
		'search_items'       => sprintf( __( '%s içinde ara', 'servomotion' ), $plural ),
		'not_found'          => __( 'Kayıt bulunamadı.', 'servomotion' ),
		'not_found_in_trash' => __( 'Çöp kutusunda kayıt yok.', 'servomotion' ),
	);
}

/**
 * Tekil sayfası olmayan, sadece bir sayfa içinde listelenen CPT'ler için
 * ortak argümanlar.
 *
 * @return array
 */
function servo_cpt_listonly_args() {
	return array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_rest'        => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
	);
}

/**
 * CPT'leri kaydeder.
 */
function servo_register_post_types() {
	// Uygulama alanları: tekil sayfası var (lastik, nonwoven, ambalaj).
	// Film, tekstil, etiket gibi "diğer" alanlar da buraya girilir; detay sayfası
	// olup olmadığı ACF alanıyla belirlenir.
	register_post_type(
		'uygulama',
		array(
			'labels'        => servo_cpt_labels( __( 'Uygulama alanı', 'servomotion' ), __( 'Uygulama alanları', 'servomotion' ) ),
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => false,
			'hierarchical'  => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-admin-generic',
			'supports'      => array( 'title', 'thumbnail', 'page-attributes', 'revisions' ),
			'rewrite'       => array(
				'slug'       => 'uygulama',
				'with_front' => false,
			),
		)
	);

	// Vaka çalışmaları: referanslar sayfasında listelenir (#lastik gibi çapalarla).
	register_post_type(
		'vaka',
		array_merge(
			servo_cpt_listonly_args(),
			array(
				'labels'        => servo_cpt_labels( __( 'Vaka çalışması', 'servomotion' ), __( 'Vaka çalışmaları', 'servomotion' ) ),
				'menu_position' => 22,
				'menu_icon'     => 'dashicons-portfolio',
				'supports'      => array( 'title', 'page-attributes', 'revisions' ),
			)
		)
	);

	// Dokümanlar: dokümanlar sayfasında ve ürün sayfalarında listelenir.
	register_post_type(
		'dokuman',
		array_merge(
			servo_cpt_listonly_args(),
			array(
				'labels'        => servo_cpt_labels( __( 'Doküman', 'servomotion' ), __( 'Dokümanlar', 'servomotion' ) ),
				'menu_position' => 23,
				'menu_icon'     => 'dashicons-media-document',
				'supports'      => array( 'title', 'page-attributes', 'revisions' ),
			)
		)
	);

	// SSS: başlık = soru, içerik = cevap.
	register_post_type(
		'sss',
		array_merge(
			servo_cpt_listonly_args(),
			array(
				'labels'        => servo_cpt_labels( __( 'Soru', 'servomotion' ), __( 'SSS', 'servomotion' ) ),
				'menu_position' => 24,
				'menu_icon'     => 'dashicons-editor-help',
				'supports'      => array( 'title', 'editor', 'page-attributes', 'revisions' ),
			)
		)
	);

	// Sözlük: başlık = terim, içerik = açıklama.
	register_post_type(
		'terim',
		array_merge(
			servo_cpt_listonly_args(),
			array(
				'labels'        => servo_cpt_labels( __( 'Terim', 'servomotion' ), __( 'Sözlük', 'servomotion' ) ),
				'menu_position' => 25,
				'menu_icon'     => 'dashicons-book-alt',
				'supports'      => array( 'title', 'editor', 'page-attributes', 'revisions' ),
			)
		)
	);
}
add_action( 'init', 'servo_register_post_types' );

/**
 * Taxonomy'leri kaydeder.
 *
 * İkisi de ön yüzde arşiv sayfası üretmez: rehber filtresi istemci tarafında
 * çalışır, SSS grupları tek sayfada listelenir.
 */
function servo_register_taxonomies() {
	// Rehber yazılarının ürün alanı (core / shuttle / genel).
	register_taxonomy(
		'urun_alani',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'          => __( 'Ürün alanları', 'servomotion' ),
				'singular_name' => __( 'Ürün alanı', 'servomotion' ),
				'menu_name'     => __( 'Ürün alanları', 'servomotion' ),
				'add_new_item'  => __( 'Yeni ürün alanı ekle', 'servomotion' ),
				'edit_item'     => __( 'Ürün alanını düzenle', 'servomotion' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);

	// SSS grupları (hassasiyet, sistem seçimi, entegrasyon...).
	register_taxonomy(
		'sss_grup',
		array( 'sss' ),
		array(
			'labels'            => array(
				'name'          => __( 'SSS grupları', 'servomotion' ),
				'singular_name' => __( 'SSS grubu', 'servomotion' ),
				'menu_name'     => __( 'Gruplar', 'servomotion' ),
				'add_new_item'  => __( 'Yeni grup ekle', 'servomotion' ),
				'edit_item'     => __( 'Grubu düzenle', 'servomotion' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}
add_action( 'init', 'servo_register_taxonomies' );

/**
 * Tema etkinleştirildiğinde rewrite kurallarını yeniler.
 */
function servo_flush_rewrite_rules() {
	servo_register_post_types();
	servo_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'servo_flush_rewrite_rules' );
