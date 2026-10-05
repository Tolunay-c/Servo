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
require_once $servo_inc . 'cpt.php';
require_once $servo_inc . 'acf.php';

unset( $servo_inc );
