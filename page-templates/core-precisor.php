<?php
/**
 * Template Name: Core Precisor
 *
 * Core Precisor ürün sayfası. Tasarımda bu sayfada <main> yoktur; hero
 * (<header class="hero">) ve bölümler doğrudan nav'dan sonra gelir.
 * Simülasyon ve gövde merdiveni core-sim.js ve core-govde.js ile çalışır.
 * İçerik "Core Precisor içeriği" alan grubundan (group_servo_core),
 * güvenlik bölümü Site Ayarları'ndan gelir.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

get_header();

servo_urun_bolumleri( 'core' );

get_footer();
