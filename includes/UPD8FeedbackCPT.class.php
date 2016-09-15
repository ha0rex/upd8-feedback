<?php
// don't load directly
if (!defined('ABSPATH')) die('-1');

class UPD8FeedbackCPT {
	public function __construct() {
		return $this->init();
	}
	
	private function init() {
		$this->plugin_version = UPD8_FEEDBACK_PLUGIN_VER;
		
		// Register upd8_feedback custom post type
		add_action( 'init', array( $this, 'registerPostType') );
		
		// Set up meta boxes
		$this->metaBoxesSetup();
		
		return $this;
	}
	
	public function getPluginVersion() {
		return $this->pluginVersion;
	}

	public function registerPostType() {
		register_post_type( 'upd8_feedback',
		  array(
			'labels' => array(
			  'name' => __( 'Feedback', 'upd8-feedback' ),
			  'singular_name' => __( 'Feedback form', 'upd8-feedback' ),
			  'add_new' => __( 'Add new', 'upd8-feedback' ),
			  'add_new_item' => __( 'Add new Feedback form', 'upd8-feedback' ),
			  'edit_item' => __( 'Edit Feedback form', 'upd8-feedback' ),
			  'new_item' => __( 'New Feedback form', 'upd8-feedback' ),
			  'view_item' => __( 'View Feedback form', 'upd8-feedback' ),
			  'not_found' => __( 'No Feedback forms found', 'upd8-feedback' ),
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array('slug' => 'feedback'),
			'menu_icon' => 'dashicons-portfolio',
			'taxonomies' => array('post_tag'),
			'supports' => array('title', 'slug', 'author')
		  )
		);
		
		return $this;
	}
	
	private function metaBoxesSetup() {
		add_action( 'load-post.php', array( $this, 'addMetaBoxes') );
		add_action( 'load-post-new.php', array( $this, 'addMetaBoxes') );
	
		/* Add meta boxes on the 'add_meta_boxes' hook. */
	  	add_action( 'add_meta_boxes', array($this, 'addMetaBoxes') );
  
	  	/* Save post meta on the 'save_post' hook. */
	  	add_action( 'save_post', array($this, 'savePostMeta'), 10, 2 );
	  	
	  	return $this;
	}
	
	public function addMetaBoxes() {
		add_meta_box(
			'upd8_feedback_meta_boxes',      // Unique ID
			esc_html__( 'Feedback', 'Mimox' ),    // Title
		  	array( $this, 'addMetaBoxesCallback'),   // Callback function
		  	'upd8_feedback',         // Admin page (or post type)
		  	'normal',         // Context
		  	'default'         // Priority
		);
		
		return $this;
	}
	
	public function savePostMeta( $post_id, $post ) {
		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['upd8_feedback_nonce'] ) || !wp_verify_nonce( $_POST['upd8_feedback_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;
			
		foreach( $_POST['questions'] as $i => $question ) {
			if( $question && $question != '' ) {
				$questions[] = $question;
			}
		}


		/* Save fields */
		update_post_meta( $post_id, 'display-skip-btn', $_POST['display-skip-btn'] );
		update_post_meta( $post_id, 'slide-to-next-auto', $_POST['slide-to-next-auto'] );
		
		update_post_meta( $post_id, 'prevent-duplicates-cookie', $_POST['prevent-duplicates-cookie'] );
		update_post_meta( $post_id, 'prevent-duplicates-ip', $_POST['prevent-duplicates-ip'] );
		
		update_post_meta( $post_id, 'stars-color', $_POST['stars-color'] );
		update_post_meta( $post_id, 'stars-color-hover', $_POST['stars-color-hover'] );
		update_post_meta( $post_id, 'stars-color-active', $_POST['stars-color-active'] );
		
		update_post_meta( $post_id, 'prologue', $_POST['prologue'] );
		update_post_meta( $post_id, 'conclusion', $_POST['conclusion'] );	
		update_post_meta( $post_id, 'questions', $questions );		
		
		return $this;
	}
	
	public function addMetaBoxesCallback( $object, $box ) { 
		/* getting value of display-skip-btn */
		$display_skip_btn = get_post_meta( $object->ID, 'display-skip-btn', true );	
		$display_skip_btn = $display_skip_btn ? 'checked' : '';
		
		/* getting value of slide-to-next-auto */
		$slide_to_next_auto = get_post_meta( $object->ID, 'slide-to-next-auto', true );	
		$slide_to_next_auto = $slide_to_next_auto ? 'checked' : '';
		
		/* getting value of prevent-duplicates-cookie */
		$prevent_duplicates_cookie = get_post_meta( $object->ID, 'prevent-duplicates-cookie', true );	
		$prevent_duplicates_cookie = $prevent_duplicates_cookie ? 'checked' : '';
		
		/* getting value of prevent-duplicates-ip */
		$prevent_duplicates_ip = get_post_meta( $object->ID, 'prevent-duplicates-ip', true );	
		$prevent_duplicates_ip = $prevent_duplicates_ip ? 'checked' : '';
		
		/* getting stars color */
		$stars_color = get_post_meta( $object->ID, 'stars-color', true );	
		$stars_color = $stars_color ? $stars_color : '#CCCCCC';
		
		/* getting stars color (hover) */
		$stars_color_hover = get_post_meta( $object->ID, 'stars-color-hover', true );	
		$stars_color_hover = $stars_color_hover ? $stars_color_hover : '#787878';
		
		/* getting stars color (active) */
		$stars_color_active = get_post_meta( $object->ID, 'stars-color-active', true );	
		$stars_color_active = $stars_color_active ? $stars_color_active : '#FFFC00';
		
		/* getting prologue */
		$prologue = get_post_meta( $object->ID, 'prologue', true );	
		$prologue = $prologue ? $prologue : '';
		
		/* getting questions */
		$questions = get_post_meta( $object->ID, 'questions', true );	
		$questions = $questions ? $questions : array('');
		
		/* getting conclusion */
		$conclusion = get_post_meta( $object->ID, 'conclusion', true );	
		$conclusion = $conclusion ? $conclusion : '';
		
		/* generating nonce field */
		wp_nonce_field( basename( __FILE__ ), 'upd8_feedback_nonce' );
		
		$this->enqueueAdminJS();
		$this->enqueueAdminCSS();
		
		$formfills = $this->getFormFills( $object->ID );
		
		?>
		<div class="upd8-feedback-form-editor">
			<h3><?php _e('Feedback', 'upd8-feedback') ?></h3>
			<?php if($object->ID): ?>
				<div class="row">
					<label for="shortcode"><?php _e('Shortcode', 'upd8-feedback') ?></label>
					<input type="text" class="shortcode" disabled value="[upd8-feedback id=&quot;<?php echo $object->ID ?>&quot;]" />
				</div>
			<?php endif; ?>
			<div class="row">
				<label for="display-skip-btn"><?php _e('Display skip button', 'upd8-feedback') ?></label>
				<input name="display-skip-btn" type="checkbox" <?php echo $display_skip_btn ?> />
			</div>
			<div class="row">
				<label for="slide-to-next-auto"><?php _e('Slide to next automatically', 'upd8-feedback') ?></label>
				<input name="slide-to-next-auto" type="checkbox" <?php echo $slide_to_next_auto ?> />
			</div>	
			<div class="row">
				<label for="prevent-duplicates-cookie"><?php _e('Prevent duplicated form fills (Cookie)', 'upd8-feedback') ?></label>
				<input name="prevent-duplicates-cookie" type="checkbox" <?php echo $prevent_duplicates_cookie ?> />
			</div>	
			<div class="row">
				<label for="prevent-duplicates-ip"><?php _e('Prevent duplicated form fills (IP)', 'upd8-feedback') ?></label>
				<input name="prevent-duplicates-ip" type="checkbox" <?php echo $prevent_duplicates_ip ?> />
			</div>		
			<div class="row">
				<label for="stars-color"><?php _e('Stars color', 'upd8-feedback') ?></label>
				<input class="color-picker" type="text" name="stars-color" value="<?php esc_attr_e($stars_color); ?>"/>
			</div>	
			<div class="row">
				<label for="stars-color-hover"><?php _e('Stars color (hover)', 'upd8-feedback') ?></label>
				<input class="color-picker" type="text" name="stars-color-hover" value="<?php esc_attr_e($stars_color_hover); ?>"/>
			</div>	
			<div class="row">
				<label for="stars-color-active"><?php _e('Stars color (active)', 'upd8-feedback') ?></label>
				<input class="color-picker" type="text" name="stars-color-active" value="<?php esc_attr_e($stars_color_active); ?>"/>
			</div>			
			<div class="row">
				<label for="prologue"><?php _e('Prologue', 'upd8-feedback') ?></label>
				<?php
				wp_editor( $prologue, 'prologue', array(
					'wpautop'       => true,
					'media_buttons' => false,
					'textarea_name' => 'prologue',
					'textarea_rows' => 10,
					'teeny'         => true
				) );
				?>
			</div>
			<div class="row">
				<label for="conclusion"><?php _e('Conclusion', 'upd8-feedback') ?></label>
				<?php
				wp_editor( $conclusion, 'conclusion', array(
					'wpautop'       => true,
					'media_buttons' => false,
					'textarea_name' => 'conclusion',
					'textarea_rows' => 10,
					'teeny'         => true
				) );
				?>
			</div>
			<div class="questions">
				<h4><?php _e('Questions', 'upd8-feedback') ?></h4>
				<?php if($questions[0] != ''): ?>
					<h5 class="msg msg-warning"><?php _e('Warning! Reordering or modifying questions on an existing form can cause confusion while reading stats. If you still want to do that, it\'s recommended to create a new form istead.', 'upd8-feedback') ?></h5>
				<?php endif; ?>
				<?php foreach( $questions as $i => $question ): ?>
					<div class="row">
						<label for="questions"><?php _e('Question', 'upd8-feedback') ?> #<span class="nr"><?php echo $i+1 ?></span></label>		
						<textarea name="questions[]"><?php echo $question ?></textarea>		
					</div>
				<?php endforeach; ?>
			</div>
		</div>	
		<?php if($formfills): ?>
		<?php $ip_addresses = array(); ?>
		<div class="upd8-feedback-form-fills">
			<h4><?php _e('Form Fills', 'upd8-feedback') ?></h4>
			<table class="form-fills">
				<tr class="heading">
					<th><?php _e('Date', 'upd8-feedback') ?></th>
					<th><?php _e('IP', 'upd8-feedback') ?></th>
					<th><?php _e('Hostname', 'upd8-feedback') ?></th>
					
					<?php foreach( $questions as $i => $question ): ?>
						<th><?php echo ($i+1).'. '.$question ?></th>
					<?php endforeach; ?>
				</tr>
				<?php foreach( $formfills as $fill ):  ?>
					<?php if( $fill->answers ): ?>
						<tr class="<?php echo isset($ip_addresses[$fill->ip]) ? 'duplicate' : '' ?>">
							<td><?php echo $fill->time ?></td>
							<td><?php echo $fill->ip ?><?php echo isset($ip_addresses[$fill->ip]) ? ' (DUP!)' : '' ?></td>
							<td><?php echo gethostbyaddr($fill->ip) ?></td>
							
							<?php $ip_addresses[$fill->ip]++; ?>
						
							<?php $fill->answers = json_decode($fill->answers); ?>
							<?php if( is_array($fill->answers) ): ?>
								<?php foreach( $fill->answers as $i=>$answer ): ?>
									<td><?php echo '<span class="stars">'.str_repeat('★', $answer).'</span> ('.$answer.')' ?></td>
									<?php $average[$i]+=$answer; ?>
									<?php $average_count[$i] = $answer == 0 ? $average_count[$i] : $average_count[$i]+1; ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				<tr class="average">
					<th><?php _e('AVG', 'upd8-feedback') ?></th>
					<th colspan="2"><?php printf( __("%s individual IPs", 'upd8-feedback'), count($ip_addresses) ) ?></th>
					<?php foreach( $questions as $i => $question ): ?>
						<th><?php echo $average[$i]/$average_count[$i] ?></th>
					<?php endforeach; ?>
				</tr>				
			</table>
		</div>
		<?php endif; ?>	
		<?php
		
		return $this;
	}
	
	private function getFormFills($id) {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'upd8_feedback_form_fills';
		
		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE form_id = ".$id, OBJECT );		
		
		return $results;		
	}
	
	private function enqueueAdminJS() {
		wp_enqueue_script( 'upd8-feedback-admin', plugin_dir_url(__FILE__).'../js/admin-functions.js', false, $this->getPluginVersion(), true );
		wp_enqueue_script( 'wp-color-picker');
		
		return $this;
	}
	
	private function enqueueAdminCSS() {
		wp_enqueue_style( 'upd8-feedback-admin', plugin_dir_url(__FILE__).'../css/admin.css', array(), $this->getPluginVersion(), 'all' );
       	wp_enqueue_style( 'wp-color-picker');
		
		return $this;
	}
}
?>