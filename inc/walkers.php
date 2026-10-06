<?php
/**
 * Menü walker'ları.
 *
 * Tasarımın menü markup'ı <ul>/<li> içermez; bağlantılar doğrudan <nav>
 * (standart) veya <div class="tabs"> (ürün sayfaları) içinde durur. Alt menü,
 * tasarımda CSS/JS'i hazır olan navdrop yapısıyla basılır.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ana menü walker'ı.
 *
 * wp_nav_menu() argümanlarında 'servo_variant' => 'tab' verilirse üst seviye
 * bağlantılar class="tab" alır (ürün sayfası header'ı).
 *
 * Alt menü markup'ı:
 * <div class="navdrop">
 *   <button class="navtop" type="button" aria-expanded="false">ETİKET <span class="car">▾</span></button>
 *   <div class="navsub"> <a>…</a> <div class="sep"></div> … </div>
 * </div>
 *
 * Alt öğelerde CSS class'ı "sep" olan öğe ayırıcı çizgi olarak basılır.
 */
class Servo_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Alt menü <ul> yerine hiçbir şey basmaz; sarmalayıcı start_el içinde açılır.
	 *
	 * @param string   $output Çıktı.
	 * @param int      $depth  Derinlik.
	 * @param stdClass $args   wp_nav_menu argümanları.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Alt menü kapanışı start_el/end_el içinde yapılır.
	 *
	 * @param string   $output Çıktı.
	 * @param int      $depth  Derinlik.
	 * @param stdClass $args   wp_nav_menu argümanları.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Tek bir menü öğesini basar.
	 *
	 * @param string   $output            Çıktı.
	 * @param WP_Post  $data_object       Menü öğesi.
	 * @param int      $depth             Derinlik.
	 * @param stdClass $args              wp_nav_menu argümanları.
	 * @param int      $current_object_id Geçerli nesne ID'si.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item    = $data_object;
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$title   = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

		if ( 0 === $depth && $this->has_children ) {
			$output .= '<div class="navdrop"><button class="navtop" type="button" aria-expanded="false">'
				. esc_html( $title ) . ' <span class="car" aria-hidden="true">▾</span></button><div class="navsub">';

			// Üst öğenin kendi bağlantısı varsa alt menünün ilk öğesi olur.
			if ( ! empty( $item->url ) && '#' !== $item->url ) {
				$output .= $this->link( $item, $title, '' );
			}

			return;
		}

		if ( $depth > 0 && in_array( 'sep', $classes, true ) ) {
			$output .= '<div class="sep" role="separator"></div>';
			return;
		}

		$class   = ( 0 === $depth && isset( $args->servo_variant ) && 'tab' === $args->servo_variant ) ? 'tab' : '';
		$output .= $this->link( $item, $title, $class );
	}

	/**
	 * Alt menülü üst öğenin sarmalayıcısını kapatır.
	 *
	 * @param string   $output      Çıktı.
	 * @param WP_Post  $data_object Menü öğesi.
	 * @param int      $depth       Derinlik.
	 * @param stdClass $args        wp_nav_menu argümanları.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( 0 === $depth && in_array( 'menu-item-has-children', (array) $data_object->classes, true ) ) {
			$output .= '</div></div>';
		}
	}

	/**
	 * Bir <a> etiketi üretir.
	 *
	 * @param WP_Post $item  Menü öğesi.
	 * @param string  $title Etiket.
	 * @param string  $class class değeri (boş olabilir).
	 * @return string
	 */
	private function link( $item, $title, $class ) {
		$atts = '';

		if ( '' !== $class ) {
			$atts .= ' class="' . esc_attr( $class ) . '"';
		}

		if ( ! empty( $item->target ) ) {
			$atts .= ' target="' . esc_attr( $item->target ) . '"';
		}

		if ( ! empty( $item->xfn ) ) {
			$atts .= ' rel="' . esc_attr( $item->xfn ) . '"';
		} elseif ( '_blank' === $item->target ) {
			$atts .= ' rel="noopener"';
		}

		if ( servo_menu_item_is_current( $item ) ) {
			$atts .= ' aria-current="page"';
		}

		return '<a href="' . esc_url( $item->url ) . '"' . $atts . '>' . esc_html( $title ) . '</a>';
	}
}

/**
 * Footer menüsü walker'ı.
 *
 * Üst seviye öğeler kolon başlığıdır (<b>), alt öğeler kolon bağlantılarıdır:
 * <div class="fcol"><b>BAŞLIK</b><a>…</a><a>…</a></div>
 */
class Servo_Footer_Walker extends Walker_Nav_Menu {

	/**
	 * <ul> basılmaz.
	 *
	 * @param string   $output Çıktı.
	 * @param int      $depth  Derinlik.
	 * @param stdClass $args   wp_nav_menu argümanları.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * </ul> basılmaz.
	 *
	 * @param string   $output Çıktı.
	 * @param int      $depth  Derinlik.
	 * @param stdClass $args   wp_nav_menu argümanları.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * Kolon başlığını veya bağlantıyı basar.
	 *
	 * @param string   $output            Çıktı.
	 * @param WP_Post  $data_object       Menü öğesi.
	 * @param int      $depth             Derinlik.
	 * @param stdClass $args              wp_nav_menu argümanları.
	 * @param int      $current_object_id Geçerli nesne ID'si.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item  = $data_object;
		$title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

		if ( 0 === $depth ) {
			$output .= '<div class="fcol"><b>' . esc_html( $title ) . '</b>';
			return;
		}

		$atts = '';

		if ( ! empty( $item->target ) ) {
			$atts .= ' target="' . esc_attr( $item->target ) . '" rel="noopener"';
		}

		$output .= '<a href="' . esc_url( $item->url ) . '"' . $atts . '>' . esc_html( $title ) . '</a>';
	}

	/**
	 * Kolonu kapatır.
	 *
	 * @param string   $output      Çıktı.
	 * @param WP_Post  $data_object Menü öğesi.
	 * @param int      $depth       Derinlik.
	 * @param stdClass $args        wp_nav_menu argümanları.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '</div>';
		}
	}
}

/**
 * Menü öğesinin geçerli sayfayı gösterip göstermediğini döndürür.
 *
 * Rehber yazılarında ve rehber arşivlerinde "Rehber" (yazılar sayfası) öğesi de
 * etkin sayılır; tasarımda rehber yazılarında REHBER aria-current alır.
 *
 * @param WP_Post $item Menü öğesi.
 * @return bool
 */
function servo_menu_item_is_current( $item ) {
	if ( ! empty( $item->current ) ) {
		return true;
	}

	$posts_page = (int) get_option( 'page_for_posts' );

	return $posts_page
		&& 'post_type' === $item->type
		&& $posts_page === (int) $item->object_id
		&& 'rehber' === servo_asset_group();
}
