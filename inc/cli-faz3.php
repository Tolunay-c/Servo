<?php
/**
 * WP-CLI: canlı site denetimi, FAZ 3 betiği (`wp servo faz3`): Rank Math kurulumu ve sayfa bazlı SEO alanları.
 *
 * Rank Math kurulum sihirbazının yaptığı ayarları yerelde komutla kurar; sayfa
 * başlığı, açıklaması ve paylaşım (OG/Twitter) görsellerini paketteki
 * sayfalardan okuyup her sayfanın Rank Math alanlarına yazar. Her adım tekrar
 * çalıştırılabilir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/cli-faz1.php';

/**
 * FAZ 3 adımları.
 */
class Servo_Faz3 extends Servo_Faz1 {

	/**
	 * Adımlar, çalışma sırasıyla.
	 *
	 * @var string[]
	 */
	const ADIMLAR = array(
		'og',
		'rankmath',
		'seo',
		'tarihler',
	);

	/**
	 * Paketteki paylaşım görselleri (og/*.png) Medya'ya alınır (og-<ad>.png).
	 */
	protected function adim_og() {
		foreach ( (array) glob( $this->dir . 'og/*.png' ) as $dosya ) {
			$ad = basename( $dosya, '.png' );
			$id = $this->medya( 'og/' . $ad . '.png', 'og-' . $ad . '.png' );
			if ( $id && ! $this->dry && '' === (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
				update_post_meta( $id, '_wp_attachment_image_alt', 'ServoMotion paylaşım görseli: ' . $ad );
			}
		}

		// Kurumsal şema logosu (Article yayıncı logosu / bilgi grafiği).
		$this->medya( 'favicon-512.png', 'servomotion-logo-512.png' );
	}

	/**
	 * Rank Math kurulum sihirbazının karşılığı: kayıt atlama, site bilgileri, başlık/şema/site haritası ayarları.
	 */
	protected function adim_rankmath() {
		if ( $this->dry ) {
			WP_CLI::log( 'Rank Math ayarları yazılacak.' );
			return;
		}

		// "Şimdilik atla": Rank Math hesap bağlantısı olmadan ön yüzü açar (sihirbazdaki karşılığı).
		update_option( 'rank_math_registration_skip', 1 );
		update_option( 'rank_math_wizard_completed', true );
		if ( class_exists( '\RankMath\Helper' ) ) {
			\RankMath\Helper::is_configured( true );
		}

		$logo = $this->medya( 'favicon-512.png', 'servomotion-logo-512.png' );
		$og   = $this->medya( 'og/index.png', 'og-index.png' );

		$baslik = (array) get_option( 'rank-math-options-titles', array() );
		$yeni   = array(
			'title_separator'                     => '—',
			'website_name'                        => 'ServoMotion',
			'knowledgegraph_type'                 => 'company',
			'knowledgegraph_name'                 => 'ServoMotion',
			'local_business_type'                 => 'Organization',
			'disable_author_archives'             => 'on',
			'author_slack_enhanced_sharing'       => 'off',
			'pt_post_default_rich_snippet'        => 'article',
			'pt_post_default_article_type'        => 'Article',
			'pt_post_slack_enhanced_sharing'      => 'off',
			'pt_page_default_rich_snippet'        => 'off',
			'pt_page_slack_enhanced_sharing'      => 'off',
			'pt_uygulama_default_rich_snippet'    => 'off',
			'pt_uygulama_slack_enhanced_sharing'  => 'off',
			'twitter_card_type'                   => 'summary_large_image',
			'404_title'                           => 'Sayfa bulunamadı %sep% %sitename%',
		);
		if ( $logo ) {
			$yeni['knowledgegraph_logo']    = wp_get_attachment_url( $logo );
			$yeni['knowledgegraph_logo_id'] = $logo;
		}
		if ( $og ) {
			$yeni['open_graph_image']    = wp_get_attachment_url( $og );
			$yeni['open_graph_image_id'] = $og;
		}
		update_option( 'rank-math-options-titles', array_merge( $baslik, $yeni ) );

		// Site haritası: yazılar, sayfalar, uygulama alanları ve kategoriler (vaka, doküman, SSS, terim sayfasızdır).
		$harita = (array) get_option( 'rank-math-options-sitemap', array() );
		update_option(
			'rank-math-options-sitemap',
			array_merge(
				$harita,
				array(
					'pt_post_sitemap'       => 'on',
					'pt_page_sitemap'       => 'on',
					'pt_uygulama_sitemap'   => 'on',
					'pt_attachment_sitemap' => 'off',
					'pt_product_sitemap'    => 'off',
					'tax_category_sitemap'  => 'on',
					'tax_post_tag_sitemap'  => 'off',
				)
			)
		);

		// Sihirbazın "Sitenizin tanımı" adımı: slogan.
		if ( in_array( get_option( 'blogdescription' ), array( '', 'ServoMotion', 'Just another WordPress site' ), true ) ) {
			update_option( 'blogdescription', 'Endüstriyel Lazer Konumlandırma' );
		}

		WP_CLI::log( 'Rank Math: kayıt atlandı, kurulum tamamlandı olarak işaretlendi; başlık, şema, site haritası ayarları yazıldı.' );
	}

	/**
	 * Sayfa bazlı başlık, açıklama ve paylaşım görseli (OG/Twitter) paketten Rank Math alanlarına yazılır.
	 */
	protected function adim_seo() {
		$yapildi = 0;

		foreach ( $this->seo_hedefleri() as $dosya => $id ) {
			if ( ! $id ) {
				WP_CLI::warning( 'Hedef kayıt bulunamadı: ' . $dosya );
				continue;
			}

			$x     = $this->xp( $dosya );
			$title = $this->metin( $x->query( '//head/title' )->item( 0 ) );
			$aciklama = $x->query( '//meta[@name="description"]' )->item( 0 );
			$aciklama = $aciklama ? trim( $aciklama->getAttribute( 'content' ) ) : '';
			$img   = $x->query( '//meta[@property="og:image"]' )->item( 0 );
			$ad    = $img && preg_match( '#/og/([a-z0-9-]+)\.png$#', $img->getAttribute( 'content' ), $m ) ? $m[1] : '';
			$ek    = $ad ? $this->medya( 'og/' . $ad . '.png', 'og-' . $ad . '.png' ) : 0;

			WP_CLI::log( sprintf( '%-34s #%d  %s  [og: %s]', $dosya, $id, mb_substr( $title, 0, 60 ), $ad ? $ad : '-' ) );

			if ( $this->dry ) {
				continue;
			}

			update_post_meta( $id, 'rank_math_title', $title );
			update_post_meta( $id, 'rank_math_description', $aciklama );
			update_post_meta( $id, 'rank_math_facebook_title', $title );
			update_post_meta( $id, 'rank_math_facebook_description', $aciklama );
			update_post_meta( $id, 'rank_math_twitter_use_facebook', 'on' );

			if ( $ek ) {
				update_post_meta( $id, 'rank_math_facebook_image', wp_get_attachment_url( $ek ) );
				update_post_meta( $id, 'rank_math_facebook_image_id', $ek );
			}
			++$yapildi;
		}

		WP_CLI::log( $yapildi . ' kaydın SEO alanları yazıldı.' );
	}

	/**
	 * Rehber yazılarının "güncelleme" tarihi yayın tarihine (23 Eylül 2026) eşitlenir; yapılandırılmış veride dateModified da aynı çıkar.
	 */
	protected function adim_tarihler() {
		global $wpdb;

		$yazilar = get_posts(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		);

		foreach ( $yazilar as $y ) {
			if ( $y->post_modified === $y->post_date ) {
				continue;
			}
			WP_CLI::log( 'Güncelleme tarihi: ' . $y->post_name . ' → ' . $y->post_date );
			if ( ! $this->dry ) {
				$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$wpdb->posts,
					array(
						'post_modified'     => $y->post_date,
						'post_modified_gmt' => $y->post_date_gmt,
					),
					array( 'ID' => $y->ID )
				);
				clean_post_cache( $y->ID );
			}
		}
	}

	/**
	 * Paket sayfası → WordPress kaydı eşleşmesi.
	 *
	 * @return array<string,int> Paket dosyası => kayıt ID'si.
	 */
	protected function seo_hedefleri() {
		$out = array();

		$sayfa = function ( $slug ) {
			$p = $this->sayfa_bul( $slug );
			return $p ? (int) $p->ID : 0;
		};

		$out['index.html'] = (int) get_option( 'page_on_front' );
		foreach ( array( 'core-precisor', 'shuttle-precisor', 'basari-hikayeleri', 'servis', 'servis-destek-hatti', 'servis-iade-sevkiyat', 'servis-kilavuzlar', 'kurumsal', 'iletisim', 'dokumanlar', 'sss', 'sozluk', 'kvkk' ) as $slug ) {
			$out[ $slug . '.html' ] = $sayfa( $slug );
		}
		$out['rehber.html'] = (int) get_option( 'page_for_posts' ) ? (int) get_option( 'page_for_posts' ) : $sayfa( 'rehber' );

		foreach ( array( 'lastik', 'nonwoven', 'ambalaj', 'film-folyo', 'tekstil', 'etiket' ) as $slug ) {
			$out[ 'uygulama-' . $slug . '.html' ] = $this->uygulama_bul( $slug );
		}

		foreach ( (array) glob( $this->dir . 'rehber-*.html' ) as $dosya ) {
			$ad    = basename( $dosya );
			$yazi  = $this->sayfa_bul( preg_replace( '/^rehber-|\.html$/', '', $ad ), 'post' );
			$out[ $ad ] = $yazi ? (int) $yazi->ID : 0;
		}

		return $out;
	}
}
