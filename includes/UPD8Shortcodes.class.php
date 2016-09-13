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
	
	function UPD8FeedbackShortCodeFunc( $atts ) {		
		$atts = shortcode_atts( array(
			'id' => false,
		), $atts, 'upd8-feedback' );
		
		$feedback = get_post($atts['id']);
		
		$prologue = get_post_meta( $atts['id'], 'prologue', true );	
		$prologue = $prologue ? $prologue : '';
		
		$conclusion = get_post_meta( $atts['id'], 'conclusion', true );	
		$conclusion = $conclusion ? $conclusion : '';
		
		$questions = get_post_meta( $atts['id'], 'questions', true );	
		$questions = $questions ? $questions : '';
		
		$questions_html = '';
		
		foreach( $questions as $i => $question ) {
			$quetions_html .= '<li><p class="question">'.($i+1).'. '.$question.'</p><input type="hidden" class="answer" name="answers[]" value="0" /><div class="buttons_container"><button class="prev">'.__('Previous', 'upd8-feedback').'</button><button class="next">'.__('Next', 'upd8-feedback').'</button></div></li>';
		}

		return '<script type="text/javascript">var ajax_url = \''.admin_url( 'admin-ajax.php' ).'\';</script>
		<div class="upd8-feedback" data-form-id="'.$atts['id'].'">
			<h2>'.$feedback->post_title.'</h2>
			<div class="wrapper">
				<div class="inner-wrapper">
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
				</div>
			</div>
		</div>';
	}	
}
?>