<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.jacobanderson.co.uk
 * @since      1.0.0
 *
 * @package    Wgauge
 * @subpackage Wgauge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wgauge
 * @subpackage Wgauge/public
 * @author     Jacob Anderson <hey@jacobanderson.co.uk>
 */
class Wgauge_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wgauge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wgauge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wgauge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wgauge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wgauge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wgauge-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wgAjax', array( 'ajax_url' => admin_url( 'admin-ajax.php')));

	}

	function insert_ui() {
		global $post;

		if(is_singular()) {
			$wg_page_active = get_post_meta($post->ID, 'wg-active-toggle', true);

			if (isset($_COOKIE['wgcomplete'])) {
				$complete = sanitize_text_field($_COOKIE['wgcomplete']);
			} else {
				$complete = false;
			}

			if($wg_page_active == 'on' && $complete != '1') {
				include 'partials/wgauge-ui.php';
			}
		}

	}

	public function submit_feedback() {
		console.log('Submitted feedback');
	}

	public function wg_get_attention_msg() {
		$msg = get_option('wg_attention_msg', 'Rate This Page');
		return $msg;
	}

	public function wg_get_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
				   if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
				   return $ip;
				 }
			   }
			}
		 }
	}

	public function wg_submit_data() {
		global $wpdb;
		$has_feedback;
		// Rating 1-100
		$rating = sanitize_text_field($_POST['wg_rating']);
		// Feedback left (optional)
		$feedback = sanitize_text_field($_POST['wg_feedback']);
		// Post ID
		$url     = wp_get_referer();
		$post = url_to_postid( $url ); 
		// User ID
		$user = sanitize_user($_POST['wg_user']);

		$timestamp = current_time( 'mysql' );

		if(strlen($feedback) >= 1) {
			$has_feedback = 1;
		}

		//$result = "Rating: " . $rating . ' Feedback: ' . $feedback . ' Post ID: ' . $post . ' User ID: ' . $user . ' Timestamp: ' . $timestamp . ' IP: ' . $ip;
		// echo $result;
		// die();
		try {
			$data = array(
				'rating' => $rating, //INT
				'feedback' => $feedback, //VARCHAR
				'postid' => $post, //INT
				'user' => $user, //INT
				'timestamp' => $timestamp, //TIMESTAMP
			);
			$table = $wpdb->prefix . 'wg_feedback';
			echo $ip;
			$updated = $wpdb->insert( $table, $data );
			
			if ( ! $updated ) {
				echo $wpdb->print_error();
			} 

		} catch (Exception $e) {
			echo $e->getMessage();
			echo 'Feedback failed to send';
			die();
		}
		error_log('Before wgUpdateMeta Call - - - - - - - - - - ');
		$this->wgUpdateMeta($post, $rating, $has_feedback);
		echo 'Feedback sent succesfully';
		die();
	}

	private function wgUpdateMeta($post_id, $new_score, $new_comment) {
		/** GET ARRAY OF SCORES AND UNSERIALIZE **/
		$score_array = get_post_meta($post_id, 'wg_score_array', true);
		$score_array_us = unserialize($score_array);
	
		/** GET COUNT OF COMMENTS **/
		$comments = get_post_meta($post_id, 'wg_comments_count', true);

		/** SET CURRENT COMMENTS **/
		$wg_comments_cur = $comments + $new_comment;

		/** GET COUNT OF GAUGES */
		//$gauges = get_post_meta($post_id, 'wg_gauges_count', true);
	
		/** ADD NEW SCORE TO UNSERIALIZED ARRAY OF SCORES **/
		$score_array_us[] = $new_score;

		/** CALCULATE NEW GAUGE COUNT */
		$wg_gauge_count_cur = sizeof($score_array_us);
	
		/** CALCULATE NEW AVERAGE SCORE **/
		$wg_score_avg_cur = array_sum($score_array_us) / $wg_gauge_count_cur;
	
		/** CURRENT SCORE ARRAY **/
		$wg_score_cur = serialize($score_array_us);
	
		/** GET GRADE **/
		$wg_grade = serialize($this->wgCalculateGrade($wg_score_avg_cur));
	
		update_post_meta($post_id, 'wg_score_avg', $wg_score_avg_cur);
		update_post_meta($post_id, 'wg_score_array', $wg_score_cur);
		update_post_meta($post_id, 'wg_comments_count', $wg_comments_cur);
		update_post_meta($post_id, 'wg_grade', $wg_grade);
		update_post_meta($post_id, 'wg_gauges', $wg_gauge_count_cur);
		}

	private function wgCalculateGrade($score) {
		error_log('wgCalculateGrade Start - - - - - - - ');
		$grade = array('letter' => '', 'color' => '', 'classname' => '');
		switch (true) {
			case $score <= 20:
				$grade['letter'] = 'F';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_f';
				break;
			case $score <= 30:
				$grade['letter'] = 'D';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_d';
				break;
			case $score <= 40:
				$grade['letter'] = 'D+';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_dp';
				break;
			case $score <= 50:
				$grade['letter'] = 'C';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_c';
				break;
			case $score <= 60:
				$grade['letter'] = 'C+';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_cp';
				break;
			case $score <= 70:
				$grade['letter'] = 'B';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_b';
				break;
			case $score <= 80:
				$grade['letter'] = 'B+';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_bp';
				break;
			case $score <= 90:
				$grade['letter'] = 'A';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_a';
				break;
			case $score <= 100:
				$grade['letter'] = 'A+';
				$grade['color'] = '#000';
				$grade['classname'] = 'wg_grade_ap';
				break;
		}

		return $grade;
	}

	public function getLogo(){
		$logoOption = get_option( 'media_selector_attachment_id' );
		if ($logoOption != ''){
			return $logoOption;
		} else {
			return plugins_url( 'partials/img/logo-sml.png', __FILE__ );
		}
		
	}
	


}
