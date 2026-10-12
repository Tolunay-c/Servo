<?php
/**
 * ServoMotion tema fonksiyonları.
 *
 * Bu dosya sadece inc/ altındaki dosyaları yükler.
 *
 * @package ServoMotion
 */

defined( 'ABSPATH' ) || exit;

$servo_inc = get_template_directory() . '/inc/';

require_once $servo_inc . 'helpers.php';
require_once $servo_inc . 'setup.php';
require_once $servo_inc . 'enqueue.php';
require_once $servo_inc . 'walkers.php';
require_once $servo_inc . 'cpt.php';
require_once $servo_inc . 'acf.php';
require_once $servo_inc . 'bloklar.php';
require_once $servo_inc . 'urun.php';
require_once $servo_inc . 'rehber.php';
require_once $servo_inc . 'arama.php';
require_once $servo_inc . 'formlar.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once $servo_inc . 'cli.php';
}

unset( $servo_inc );
