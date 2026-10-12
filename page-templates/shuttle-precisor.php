<?php
/**
 * Template Name: Shuttle Precisor
 *
 * Shuttle Precisor ürün sayfası. Tasarımda bu sayfada <main> yoktur; hero
 * (<header class="hero">) ve bölümler doğrudan nav'dan sonra gelir.
 * Konfigüratör shuttle-konfig.js ile çalışır.
 * İçerik "Shuttle Precisor içeriği" alan grubundan (group_servo_shuttle),
 * güvenlik bölümü Site Ayarları'ndan gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();

servo_urun_bolumleri( 'shuttle' );

get_footer();
