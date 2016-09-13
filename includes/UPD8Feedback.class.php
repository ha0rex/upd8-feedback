<?php
ini_set('display_errors', 1);
// don't load directly
if (!defined('ABSPATH')) die('-1');

class UPD8Feedback {
	protected $plugin_version;
	
	public function __construct($id = false) {
		return $this->init($id);
	}

	private function init($id) {
		$this->id = $id;
				
		$this->plugin_version = UPD8_FEEDBACK_PLUGIN_VER;

		$this->registerShortcodes();
		$this->registerPostType();
		
		$this->loadFrontendCSSAndJS();
		
		if ( defined( 'WPB_VC_VERSION' ) ) {
			/* $this->initVisualComposerAddOn(); */
		}
		
		return $this;
	}
	
	public static function install() {
		global $wpdb;

		$table_name = $wpdb->prefix . "upd8_feedback_form_fills"; 
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  form_id mediumint(9) NOT NULL,
		  answers text NOT NULL,
		  ip varchar(55) DEFAULT '' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );		
	}
	
	public function registerPostType() {
		require_once 'UPD8FeedbackCPT.class.php';
		$this->post_type = new UPD8FeedbackCPT();
		
		return $this;
	} 
	
	public function registerShortcodes() {
		require_once 'UPD8Shortcodes.class.php';
		$this->shortcodes = new UPD8Shortcodes();
		
		return $this;
	} 
	
	public function enqueueFrontEndJS() {
		wp_enqueue_script( 'upd8-feedback-frontend', plugin_dir_url(__FILE__).'../js/upd8-feedback.frontend.js', false, $this->plugin_version, true );	
	}
	
	public function enqueueFrontEndCSS() {
		wp_enqueue_style( 'upd8-feedback-frontend', plugin_dir_url(__FILE__).'../css/style.css', array(), $this->plugin_version, 'all' );
	}
	
	public function loadFrontendCSSAndJS() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontEndJS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontEndCSS' ) );
		
		return $this;
	}
	
	public function saveResults() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'upd8_feedback_form_fills';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ), 
				'form_id' => $_POST['form_id'], 
				'answers' => json_encode($_POST['answers']), 
				'ip' => $_SERVER['HTTP_X_FORWARDED_FOR']
			) 
		);	
		
		return 'hello';
		die;	
	}
}