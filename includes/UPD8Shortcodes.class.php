<?php
// don't load directly
if (!defined('ABSPATH')) die('-1');

class UPD8Shortcodes {
	public function __construct() {
		return $this->init();
	}
	
	private function init() {
		$this->registerShortcodes();
		
		$this->initSaveResultsAjaxHook();
		
		return $this;
	}
	
	private function registerShortcodes() {
		add_shortcode( 'upd8-feedback', array( $this, 'UPD8FeedbackShortCodeFunc' ) );
	}
	
	private function initSaveResultsAjaxHook() {
		add_action( 'wp_ajax_upd8_form_fill', array( 'UPD8Feedback', 'saveResults' ) );
		add_action( 'wp_ajax_nopriv_upd8_form_fill', array( 'UPD8Feedback', 'saveResults' ) );
	}
	
	private function setUpStyles( $id ) {
		/* getting stars color */
		$stars_color = get_post_meta( $id, 'stars-color', true );	
		$stars_color = $stars_color ? $stars_color : '#CCCCCC';
		
		/* getting stars color (hover) */
		$stars_color_hover = get_post_meta( $id, 'stars-color-hover', true );	
		$stars_color_hover = $stars_color_hover ? $stars_color_hover : '#787878';
		
		/* getting stars color (active) */
		$stars_color_active = get_post_meta( $id, 'stars-color-active', true );	
		$stars_color_active = $stars_color_active ? $stars_color_active : '#FFFC00';
		
		return '<style>
			.upd8-feedback .stars li a {
				color: '.$stars_color.';
			}
			.upd8-feedback .stars li.hover a {
				color: '.$stars_color_hover.';
			}
			.upd8-feedback .stars li.active a {
				color: '.$stars_color_active.';
			}
		</style>';
	}
	
	private function isIPFilledForm($id, $ip) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'upd8_feedback_form_fills';
		
		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE form_id = ".$id." AND ip='".$ip."'", OBJECT );		
		
		return $results ? true : false;		
	}
	
	function UPD8FeedbackShortCodeFunc( $atts ) {		
		$atts = shortcode_atts( array(
			'id' => false,
		), $atts, 'upd8-feedback' );
		
		$feedback = get_post($atts['id']);
		
		$display_form = true;

		/* getting value of display-skip-btn */
		$display_skip_btn = get_post_meta( $atts['id'], 'display-skip-btn', true );	
		$display_skip_btn = $display_skip_btn ? true : false;
		
		/* getting value of slide-to-next-auto */
		$slide_to_next_auto = get_post_meta( $atts['id'], 'slide-to-next-auto', true );	
		$slide_to_next_auto = $slide_to_next_auto ? true : false;
		
		/* getting value of prevent-duplicates-cookie */
		$prevent_duplicates_cookie = get_post_meta( $atts['id'], 'prevent-duplicates-cookie', true );	
		$prevent_duplicates_cookie = $prevent_duplicates_cookie ? 'checked' : '';
		
		/* getting value of prevent-duplicates-ip */
		$prevent_duplicates_ip = get_post_meta( $atts['id'], 'prevent-duplicates-ip', true );	
		$prevent_duplicates_ip = $prevent_duplicates_ip ? 'checked' : '';
			
		$prologue = get_post_meta( $atts['id'], 'prologue', true );	
		$prologue = $prologue ? $prologue : '';
		
		$conclusion = get_post_meta( $atts['id'], 'conclusion', true );	
		$conclusion = $conclusion ? $conclusion : '';
		
		$questions = get_post_meta( $atts['id'], 'questions', true );	
		$questions = $questions ? $questions : '';
		
		$questions_html = '';
		
		foreach( $questions as $i => $question ) {
			$quetions_html .= '<li>
				<p class="question">'.($i+1).'. '.$question.'</p>
				<input type="hidden" class="answer" name="answers[]" value="0" />
				<div class="buttons_container">
					<button class="prev">'.__('Previous', 'upd8-feedback').'</button>
					'.( $display_skip_btn ? 
					'<button class="skip">'.__('Skip', 'upd8-feedback').'</button>' : 
					'' ).'
					'.( !$slide_to_next_auto ? 
					'<button class="next">'.__('Next', 'upd8-feedback').'</button>' : 
					'' ).'
				</div>
			</li>';
		}
		
		$display_form = ( $display_form && !$prevent_duplicates_cookie ) || ( $display_form && $prevent_duplicates_cookie && !isset($_COOKIE['form_fill_'.$atts['id']]) ) ? true : false;
		$display_form = ( $display_form && !$prevent_duplicates_ip ) || ( $display_form && $prevent_duplicates_ip && !$this->isIPFilledForm($atts['id'], $_SERVER['REMOTE_ADDR']) ) ? true : false;

		return '<script type="text/javascript">var ajax_url = \''.admin_url( 'admin-ajax.php' ).'\';</script>
		'.$this->setUpStyles( $atts['id'] ).'
		<div class="upd8-feedback" data-form-id="'.$atts['id'].'" data-slide-to-next-auto="'.( $slide_to_next_auto ? 1 : 0 ).'" data-display-skip-btn="'.( $display_skip_btn ? 1 : 0 ).'">
			<h2>'.$feedback->post_title.'</h2>
			<div class="wrapper">
				<div class="inner-wrapper">
					'.( $display_form ? '
					<div class="prologue">
						'.$prologue.'
						<div class="buttons_container">
							<button class="start-quiz">'.__('Let\'s go!', 'upd8-feedback').'</button>
						</div>
					</div>
		
					<ul class="questions">
						'.$quetions_html.'
					</ul>
		
					<div class="conclusion">
						'.$conclusion.'
						<div class="buttons_container">
							<button class="finish-quiz">'.__('Close', 'upd8-feedback').'</button>
						</div>
					</div>
					' : '<div class="prologue"><p class="form-already-filled">'.__('You have already filled this form.', 'upd8-feedback')) .'</p></div>
				</div>
			</div>
		</div>';
	}	
}
?>