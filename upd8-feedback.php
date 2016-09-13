<?php
/**
 * Plugin Name: UPD8 Feedback
 * Plugin URI: http://upd8.hu
 * Description: A Fedback plugin for WordPress
 * Version: 0.1.0
 * Author: UPD8
 * Author URI: http://upd8.hu
 * License: GPL2
 * Text Domain: upd8-feedback
 */
 
// don't load directly
if (!defined('ABSPATH')) die('-1');

register_activation_hook( __FILE__, array( 'UPD8Feedback', 'install' ) );

define('UPD8_FEEDBACK_PLUGIN_VER', '1.0.0');

require_once 'includes/UPD8Feedback.class.php';

$UPD8Feedback = new UPD8Feedback();