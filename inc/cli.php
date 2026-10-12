<?php
/**
 * WP-CLI komutları.
 *
 * Sadece WP-CLI altında yüklenir (functions.php).
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * ServoMotion tema komutları.
 */
class Servo_CLI {

	/**
	 * Tasarımdaki rehber yazılarını (rehber-*.html) Gutenberg yazısı olarak içeri aktarır.
	 *
	 * Yazılar kaynak dosya adıyla (_servo_kaynak meta) eşleştirilir; komut tekrar
	 * çalıştırıldığında mevcut yazılar güncellenir, kopya oluşmaz. Kategori ve
	 * ürün alanı terimleri yoksa oluşturulur. Site Ayarları > Rehber sekmesindeki
	 * boş alanlar tasarımdaki metinlerle doldurulur (dolu alanlara dokunulmaz).
	 *
	 * ## OPTIONS
	 *
	 * [--kaynak=<dizin>]
	 * : rehber.html ve rehber-*.html dosyalarının bulunduğu dizin. Varsayılan: temanın _design/ klasörü.
	 *
	 * [--dry-run]
	 * : Hiçbir şey yazmadan ne yapılacağını listeler.
	 *
	 * ## EXAMPLES
	 *
	 *     wp servo rehber-aktar
	 *     wp servo rehber-aktar --kaynak=/yol/_design --dry-run
	 *
	 * @subcommand rehber-aktar
	 *
	 * @param array $args       Konum argümanları.
	 * @param array $assoc_args Seçenekler.
	 */
	public function rehber_aktar( $args, $assoc_args ) {
		$dir = rtrim( isset( $assoc_args['kaynak'] ) ? $assoc_args['kaynak'] : get_template_directory() . '/_design', '/' ) . '/';
		$dry = ! empty( $assoc_args['dry-run'] );

		if ( ! is_readable( $dir . 'rehber.html' ) ) {
			WP_CLI::error( 'rehber.html bulunamadı: ' . $dir );
		}

		$liste = $this->dom( $dir . 'rehber.html' );
		$xp    = new DOMXPath( $liste );
		$kart  = $xp->query( '//*[@id="yazilar"]//article[contains(@class,"featured") or contains(@class,"story")]' );

		if ( ! $kart->length ) {
			WP_CLI::error( 'rehber.html içinde yazı kartı bulunamadı.' );
		}

		$yazilar = array();

		foreach ( $kart as $i => $a ) {
			$href  = $xp->query( './/a', $a )->item( 0 )->getAttribute( 'href' );
			$dosya = basename( $href );

			if ( ! is_readable( $dir . $dosya ) ) {
				WP_CLI::warning( 'Yazı dosyası okunamadı, atlandı: ' . $dosya );
				continue;
			}

			$alan_adlari = array();
			foreach ( $xp->query( './/span[contains(@class,"atag")]', $a ) as $s ) {
				$alan_adlari[] = $this->metin( $s );
			}

			$yazilar[] = array(
				'dosya'     => $dosya,
				'slug'      => preg_replace( '/^rehber-|\.html$/', '', $dosya ),
				'sira'      => $i,
				'kategori'  => array( $a->getAttribute( 'data-category' ), $this->metin( $xp->query( './/span[contains(@class,"ctag")]', $a )->item( 0 ) ) ),
				'alanlar'   => array_combine( preg_split( '/\s+/', trim( $a->getAttribute( 'data-areas' ) ) ), $alan_adlari ),
				'baslik'    => $this->metin( $xp->query( './/h2/a | .//h3/a', $a )->item( 0 ) ),
				'ozet'      => $this->metin( $xp->query( './/p[contains(@class,"sum")]', $a )->item( 0 ) ),
				'tarih'     => $this->metin( $xp->query( './/p[contains(@class,"pmeta")]/b', $a )->item( 0 ) ),
			);
		}

		WP_CLI::log( count( $yazilar ) . ' yazı bulundu.' );

		$ilk = null;
		$ids = array();

		foreach ( $yazilar as $y ) {
			$sayfa = $this->dom( $dir . $y['dosya'] );
			$px    = new DOMXPath( $sayfa );
			$ilk   = $ilk ? $ilk : $px;

			$kirinti = $this->metin( $px->query( '//p[contains(@class,"crumb")]' )->item( 0 ) );
			$kirinti = trim( (string) preg_replace( '/^[^·]*·/u', '', $kirinti ) );
			$meta    = $px->query( '//p[contains(@class,"topmeta")]/span' );
			$okuma   = (int) $this->metin( $meta->item( 2 ) );
			$giris   = $this->metin( $px->query( '//section[contains(@class,"pdoc")]//p[contains(@class,"intro")]' )->item( 0 ) );
			$prose   = $px->query( '//div[contains(@class,"prose")]' )->item( 0 );
			$icerik  = $this->bloklar( $prose );
			$ilgili  = array();

			foreach ( $px->query( '//section[contains(@class,"related")]//article' ) as $r ) {
				$ilgili[] = basename( $px->query( './/a', $r )->item( 0 )->getAttribute( 'href' ) );
			}

			$id = $this->bul( $y['dosya'], $y['slug'] );

			WP_CLI::log( sprintf( '%s %s → /rehber/%s/', $id ? 'Güncelle:' : 'Oluştur: ', $y['dosya'], $y['slug'] ) );

			if ( $dry ) {
				continue;
			}

			$veri = array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $y['baslik'],
				'post_name'    => $y['slug'],
				'post_excerpt' => $y['ozet'],
				'post_content' => $icerik,
				// Aynı gündeki yazılar listedeki sırayı korusun diye dakikayla geriye kaydırılır.
				'post_date'    => $this->tarih( $y['tarih'], $y['sira'] ),
			);

			if ( $id ) {
				$veri['ID'] = $id;
				$sonuc      = wp_update_post( wp_slash( $veri ), true );
			} else {
				$sonuc = wp_insert_post( wp_slash( $veri ), true );
			}

			if ( is_wp_error( $sonuc ) ) {
				WP_CLI::warning( $y['dosya'] . ': ' . $sonuc->get_error_message() );
				continue;
			}

			$id                 = (int) $sonuc;
			$ids[ $y['dosya'] ] = array( $id, $ilgili );
			update_post_meta( $id, '_servo_kaynak', $y['dosya'] );

			wp_set_post_categories( $id, array( $this->terim( 'category', $y['kategori'][0], $y['kategori'][1] ) ) );
			$alanlar = array();
			foreach ( $y['alanlar'] as $slug => $ad ) {
				$alanlar[] = $this->terim( 'urun_alani', $slug, $ad );
			}
			wp_set_object_terms( $id, $alanlar, 'urun_alani' );

			$this->alan( 'field_servo_rehber_kirinti', $kirinti, $id );
			$this->alan( 'field_servo_rehber_giris', $giris, $id );
			$this->alan( 'field_servo_rehber_okuma_suresi', $okuma ? $okuma : '', $id );
			$svg = 'rehber-' . $y['slug'];
			$this->alan( 'field_servo_rehber_kart_svg', file_exists( get_template_directory() . '/assets/img/svg/' . $svg . '.svg' ) ? $svg : '', $id );
		}

		if ( $dry ) {
			WP_CLI::success( 'Deneme bitti; hiçbir şey yazılmadı.' );
			return;
		}

		// İlgili yazılar: tüm yazılar oluştuktan sonra bağlanır.
		foreach ( $ids as $dosya => $bilgi ) {
			$ilgili = array();
			foreach ( $bilgi[1] as $hedef ) {
				if ( isset( $ids[ $hedef ] ) ) {
					$ilgili[] = $ids[ $hedef ][0];
				}
			}
			$this->alan( 'field_servo_rehber_ilgili', $ilgili, $bilgi[0] );
		}

		if ( $ilk ) {
			$this->ayarlar( $xp, $ilk );
		}

		WP_CLI::success( count( $ids ) . ' yazı aktarıldı.' );
	}

	/**
	 * HTML dosyasını DOMDocument olarak yükler.
	 *
	 * @param string $file Dosya yolu.
	 * @return DOMDocument
	 */
	private function dom( $file ) {
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( '<?xml encoding="utf-8"?>' . file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		libxml_clear_errors();

		return $dom;
	}

	/**
	 * Bir düğümün düz metnini (boşluklar sadeleşmiş) döndürür.
	 *
	 * @param DOMNode|null $node Düğüm.
	 * @return string
	 */
	private function metin( $node ) {
		return $node ? trim( (string) preg_replace( '/\s+/u', ' ', $node->textContent ) ) : '';
	}

	/**
	 * Bir düğümün iç HTML'ini blok içeriğine uygun hâle getirir.
	 *
	 * Boşluklar sadeleşir, <b> → <strong> (Gutenberg'in kalın biçimi; ön yüzde
	 * yeniden <b> olur), elle yazılmış <bdi>/<span lang="en"> işaretleri
	 * kalkar (tema servo_bdi() ile ekler), tasarım adresleri WordPress
	 * adreslerine çevrilir.
	 *
	 * @param DOMNode $node Düğüm.
	 * @return string
	 */
	private function ic( $node ) {
		$html = '';
		foreach ( $node->childNodes as $c ) {
			$html .= $node->ownerDocument->saveHTML( $c );
		}

		$html = preg_replace( '#<span lang="en">\s*(<bdi lang="en">.*?</bdi>)\s*</span>#s', '$1', $html );
		$html = preg_replace( '#</?bdi[^>]*>#', '', $html );
		$html = preg_replace( '#<(/?)b>#', '<$1strong>', $html );
		$html = preg_replace_callback(
			'#href="([^"]*)"#',
			function ( $m ) {
				return 'href="' . esc_url( $this->adres( html_entity_decode( $m[1] ) ) ) . '"';
			},
			$html
		);

		return trim( (string) preg_replace( '/\s+/u', ' ', $html ) );
	}

	/**
	 * Tasarımdaki bir bağlantıyı (ör. sozluk.html, rehber-x.html, index.html#iletisim)
	 * WordPress adresine çevirir.
	 *
	 * @param string $href Bağlantı.
	 * @return string
	 */
	private function adres( $href ) {
		if ( '' === $href || preg_match( '#^(https?:|mailto:|tel:|\#)#', $href ) ) {
			return $href;
		}

		$parca = explode( '#', $href, 2 );
		$frag  = isset( $parca[1] ) ? '#' . $parca[1] : '';
		$base  = basename( $parca[0], '.html' );

		if ( 'index' === $base ) {
			return home_url( '/' ) . $frag;
		}
		if ( 0 === strpos( $base, 'rehber-' ) ) {
			return servo_post_url( substr( $base, 7 ), 'post' ) . $frag;
		}
		if ( 0 === strpos( $base, 'uygulama-' ) ) {
			return servo_post_url( substr( $base, 9 ), 'uygulama' ) . $frag;
		}
		if ( 'rehber' === $base ) {
			$sayfa = (int) get_option( 'page_for_posts' );
			return ( $sayfa ? get_permalink( $sayfa ) : home_url( '/' ) ) . $frag;
		}

		return servo_post_url( $base ) . $frag;
	}

	/**
	 * Tasarımdaki yazı metnini (div.prose) Gutenberg blok içeriğine çevirir.
	 *
	 * Yazı sonu kutusu (.endbox) şablondan geldiği için aktarılmaz.
	 *
	 * @param DOMElement $prose Metin kapsayıcısı.
	 * @return string
	 */
	private function bloklar( $prose ) {
		$out = array();

		foreach ( $prose->childNodes as $el ) {
			if ( XML_ELEMENT_NODE !== $el->nodeType ) {
				continue;
			}

			$cls = ' ' . $el->getAttribute( 'class' ) . ' ';

			switch ( $el->nodeName ) {
				case 'p':
					$out[] = $this->paragraf( $el );
					break;

				case 'h2':
				case 'h3':
					$seviye = (int) substr( $el->nodeName, 1 );
					$id     = $el->getAttribute( 'id' );
					$out[]  = $this->blok(
						'heading',
						2 === $seviye ? array() : array( 'level' => $seviye ),
						'<h' . $seviye . ' class="wp-block-heading"' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>' . $this->ic( $el ) . '</h' . $seviye . '>'
					);
					break;

				case 'ul':
				case 'ol':
					$out[] = $this->liste( $el );
					break;

				case 'div':
					if ( false !== strpos( $cls, ' endbox ' ) ) {
						break;
					}
					if ( false !== strpos( $cls, ' callout ' ) ) {
						$out[] = $this->kutu( $el );
					} elseif ( false !== strpos( $cls, ' keyval ' ) ) {
						$out[] = $this->degerler( $el );
					} elseif ( false !== strpos( $cls, ' ascroll ' ) ) {
						$out[] = $this->tablo( $el->getElementsByTagName( 'table' )->item( 0 ) );
					} else {
						$out[] = $this->blok( 'html', array(), $prose->ownerDocument->saveHTML( $el ) );
					}
					break;

				default:
					$out[] = $this->blok( 'html', array(), $prose->ownerDocument->saveHTML( $el ) );
			}
		}

		return implode( "\n\n", $out );
	}

	/**
	 * Blok yorum işaretleriyle sarılmış blok metni üretir.
	 *
	 * @param string $ad    Blok adı (core/ öneksiz).
	 * @param array  $attrs Öznitelikler.
	 * @param string $html  Blok HTML'i.
	 * @return string
	 */
	private function blok( $ad, $attrs, $html ) {
		$json = $attrs ? ' ' . wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';

		return '<!-- wp:' . $ad . $json . " -->\n" . $html . "\n<!-- /wp:" . $ad . ' -->';
	}

	/**
	 * Paragrafı paragraph bloğuna çevirir; tasarımdaki yazı boyutu
	 * (style="font-size:…") bloğun yazı tipi boyutu ayarına aktarılır.
	 *
	 * @param DOMElement $el Paragraf.
	 * @return string
	 */
	private function paragraf( $el ) {
		if ( preg_match( '/font-size:\s*([0-9.]+(?:px|rem|em))/', $el->getAttribute( 'style' ), $m ) ) {
			return $this->blok( 'paragraph', array( 'style' => array( 'typography' => array( 'fontSize' => $m[1] ) ) ), '<p style="font-size:' . $m[1] . '">' . $this->ic( $el ) . '</p>' );
		}

		return $this->blok( 'paragraph', array(), '<p>' . $this->ic( $el ) . '</p>' );
	}

	/**
	 * Listeyi (ul, ol, ol.steps, ol.alist) list bloğuna çevirir.
	 *
	 * @param DOMElement $el Liste.
	 * @return string
	 */
	private function liste( $el ) {
		$attrs = array();
		$cls   = array( 'wp-block-list' );

		if ( 'ol' === $el->nodeName ) {
			$attrs['ordered'] = true;
		}
		foreach ( array( 'steps', 'alist' ) as $stil ) {
			if ( false !== strpos( ' ' . $el->getAttribute( 'class' ) . ' ', ' ' . $stil . ' ' ) ) {
				$attrs['className'] = 'is-style-' . $stil;
				$cls[]              = 'is-style-' . $stil;
			}
		}

		$ogeler = array();
		foreach ( $el->childNodes as $li ) {
			if ( 'li' === $li->nodeName ) {
				$ogeler[] = $this->blok( 'list-item', array(), '<li>' . $this->ic( $li ) . '</li>' );
			}
		}

		return $this->blok( 'list', $attrs, '<' . $el->nodeName . ' class="' . implode( ' ', $cls ) . '">' . implode( "\n\n", $ogeler ) . '</' . $el->nodeName . '>' );
	}

	/**
	 * Bilgi kutusunu (div.callout) "Bilgi kutusu" stilli grup bloğuna çevirir.
	 *
	 * @param DOMElement $el Kutu.
	 * @return string
	 */
	private function kutu( $el ) {
		$ic = array();

		foreach ( $el->childNodes as $c ) {
			if ( XML_ELEMENT_NODE !== $c->nodeType ) {
				continue;
			}
			if ( 'div' === $c->nodeName && false !== strpos( ' ' . $c->getAttribute( 'class' ) . ' ', ' t ' ) ) {
				$ic[] = $this->blok( 'paragraph', array( 'className' => 'is-style-kutu-basligi' ), '<p class="is-style-kutu-basligi">' . $this->ic( $c ) . '</p>' );
			} else {
				$ic[] = $this->paragraf( $c );
			}
		}

		return $this->blok( 'group', array( 'className' => 'is-style-callout' ), '<div class="wp-block-group is-style-callout">' . implode( "\n\n", $ic ) . '</div>' );
	}

	/**
	 * Değer kutularını (div.keyval) "Değer kutuları" stilli tablo bloğuna çevirir.
	 *
	 * Her kutu bir satırdır: açıklama | değer | birim.
	 *
	 * @param DOMElement $el Kutular.
	 * @return string
	 */
	private function degerler( $el ) {
		$satirlar = '';

		foreach ( $el->childNodes as $kutu ) {
			if ( 'div' !== $kutu->nodeName ) {
				continue;
			}
			$xp    = new DOMXPath( $el->ownerDocument );
			$k     = $xp->query( './/div[contains(@class,"k")]', $kutu )->item( 0 );
			$v     = $xp->query( './/div[contains(@class,"v")]', $kutu )->item( 0 );
			$birim = $v ? $v->getElementsByTagName( 'small' )->item( 0 ) : null;
			$b     = $birim ? $this->metin( $birim ) : '';
			if ( $birim ) {
				$v->removeChild( $birim );
			}
			$satirlar .= '<tr><td>' . ( $k ? $this->ic( $k ) : '' ) . '</td><td>' . ( $v ? $this->ic( $v ) : '' ) . '</td><td>' . esc_html( $b ) . '</td></tr>';
		}

		return $this->blok(
			'table',
			array(
				'hasFixedLayout' => false,
				'className'      => 'is-style-keyval',
			),
			'<figure class="wp-block-table is-style-keyval"><table><tbody>' . $satirlar . '</tbody></table></figure>'
		);
	}

	/**
	 * Tabloyu (table.atable) tablo bloğuna çevirir; birleşik hücreler
	 * (colspan, rowspan) korunur.
	 *
	 * @param DOMElement $table Tablo.
	 * @return string
	 */
	private function tablo( $table ) {
		$html = '';

		foreach ( array( 'thead', 'tbody' ) as $bolum ) {
			$b = $table->getElementsByTagName( $bolum )->item( 0 );
			if ( ! $b ) {
				continue;
			}
			$html .= '<' . $bolum . '>';
			foreach ( $b->getElementsByTagName( 'tr' ) as $tr ) {
				$html .= '<tr>';
				foreach ( $tr->childNodes as $h ) {
					if ( in_array( $h->nodeName, array( 'td', 'th' ), true ) ) {
						$birlesik = '';
						foreach ( array( 'colspan', 'rowspan' ) as $oz ) {
							if ( $h->getAttribute( $oz ) ) {
								$birlesik .= ' ' . $oz . '="' . (int) $h->getAttribute( $oz ) . '"';
							}
						}
						$html .= '<' . $h->nodeName . $birlesik . '>' . $this->ic( $h ) . '</' . $h->nodeName . '>';
					}
				}
				$html .= '</tr>';
			}
			$html .= '</' . $bolum . '>';
		}

		return $this->blok( 'table', array( 'hasFixedLayout' => false ), '<figure class="wp-block-table"><table>' . $html . '</table></figure>' );
	}

	/**
	 * Mevcut yazıyı kaynak dosya adından, yoksa slug'ından bulur.
	 *
	 * @param string $dosya Kaynak dosya adı.
	 * @param string $slug  Yazı slug'ı.
	 * @return int Bulunamazsa 0.
	 */
	private function bul( $dosya, $slug ) {
		$ids = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_servo_kaynak', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $dosya, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( $ids ) {
			return (int) $ids[0];
		}

		$post = get_page_by_path( $slug, OBJECT, 'post' );

		return $post ? (int) $post->ID : 0;
	}

	/**
	 * Terimi slug'ından bulur, yoksa oluşturur.
	 *
	 * @param string $tax  Taxonomy.
	 * @param string $slug Slug.
	 * @param string $ad   Ad.
	 * @return int Terim ID'si.
	 */
	private function terim( $tax, $slug, $ad ) {
		$t = get_term_by( 'slug', $slug, $tax );

		if ( $t ) {
			return (int) $t->term_id;
		}

		$t = wp_insert_term( $ad, $tax, array( 'slug' => $slug ) );

		return is_wp_error( $t ) ? 0 : (int) $t['term_id'];
	}

	/**
	 * "16 Eylül 2026" biçimindeki tarihi yazı tarihine çevirir.
	 *
	 * @param string $metin Tarih.
	 * @param int    $sira  Listedeki sıra (her sıra 1 dakika geriye; liste sırası korunur).
	 * @return string Y-m-d H:i:s
	 */
	private function tarih( $metin, $sira ) {
		$aylar = array( 'ocak', 'şubat', 'mart', 'nisan', 'mayıs', 'haziran', 'temmuz', 'ağustos', 'eylül', 'ekim', 'kasım', 'aralık' );
		$p     = preg_split( '/\s+/u', mb_strtolower( trim( $metin ), 'UTF-8' ) );
		$ay    = isset( $p[1] ) ? array_search( $p[1], $aylar, true ) : false;

		if ( count( $p ) < 3 || false === $ay ) {
			return current_time( 'mysql' );
		}

		return sprintf( '%04d-%02d-%02d 12:%02d:00', (int) $p[2], $ay + 1, (int) $p[0], max( 0, 59 - $sira ) );
	}

	/**
	 * SCF alanını günceller (eklenti kapalıysa uyarır).
	 *
	 * @param string     $key     Alan anahtarı.
	 * @param mixed      $value   Değer.
	 * @param int|string $post_id Yazı ID'si veya 'option'.
	 */
	private function alan( $key, $value, $post_id ) {
		if ( ! function_exists( 'update_field' ) ) {
			WP_CLI::warning( 'Secure Custom Fields etkin değil; alan yazılamadı: ' . $key );
			return;
		}

		update_field( $key, $value, $post_id );
	}

	/**
	 * Site Ayarları > Rehber sekmesindeki boş alanları tasarımdaki metinlerle doldurur.
	 *
	 * @param DOMXPath $liste Rehber listesi (rehber.html).
	 * @param DOMXPath $yazi  İlk yazı sayfası (ortak kutu ve not için).
	 */
	private function ayarlar( $liste, $yazi ) {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		$gh       = $liste->query( '//section[contains(@class,"gh")]' )->item( 0 );
		$h1       = $gh ? $liste->query( './/h1', $gh )->item( 0 ) : null;
		$son      = $yazi->query( '//div[contains(@class,"endbox")]' )->item( 0 );
		$buton    = $son ? $yazi->query( './/a', $son )->item( 0 ) : null;
		$not      = $yazi->query( '//section[contains(@style,"padding:0")]//p' )->item( 0 );
		$yazar    = $this->metin( $yazi->query( '//p[contains(@class,"topmeta")]/span' )->item( 1 ) );
		$degerler = array(
			'rehber_etiket'     => $gh ? $this->metin( $liste->query( './/p[contains(@class,"label")]', $gh )->item( 0 ) ) : '',
			'rehber_baslik'     => $h1 ? trim( str_replace( '<br>', "\n", $this->ic( $h1 ) ) ) : '',
			'rehber_aciklama'   => $gh ? $this->metin( $liste->query( './/p[not(@class)]', $gh )->item( 0 ) ) : '',
			'rehber_yazar'      => $yazar,
			'rehber_son_baslik' => $son ? $this->metin( $son->getElementsByTagName( 'h3' )->item( 0 ) ) : '',
			'rehber_son_metin'  => $son ? $this->metin( $son->getElementsByTagName( 'p' )->item( 0 ) ) : '',
			'rehber_son_buton'  => $buton ? array(
				'url'    => $this->adres( $buton->getAttribute( 'href' ) ),
				'title'  => $this->metin( $buton ),
				'target' => '',
			) : '',
			'rehber_not'        => $not ? str_replace( array( '<strong>', '</strong>' ), array( '<b>', '</b>' ), $this->ic( $not ) ) : '',
		);

		foreach ( $degerler as $ad => $deger ) {
			// Biçimlenmiş değer alanın varsayılanını da içerir; varsayılanı olan alan dolu sayılır.
			if ( $deger && servo_is_empty( get_field( $ad, servo_options_id() ) ) ) {
				update_field( 'field_servo_' . $ad, $deger, servo_options_id() );
				WP_CLI::log( 'Site Ayarları > Rehber: ' . $ad . ' dolduruldu.' );
			}
		}
	}
}

WP_CLI::add_command( 'servo', 'Servo_CLI' );
