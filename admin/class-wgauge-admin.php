<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.jacobanderson.co.uk
 * @since      1.0.0
 *
 * @package    Wgauge
 * @subpackage Wgauge/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wgauge
 * @subpackage Wgauge/admin
 * @author     Jacob Anderson <hey@jacobanderson.co.uk>
 */
class Wgauge_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wgauge-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'daterangepicker-css', plugin_dir_url( __FILE__ ) . 'css/daterangepicker.css', array(), $this->version, 'all' );
		//wp_enqueue_style( 'wg-bootstrap-css', '//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wgauge-admin.js', array( 'jquery'), $this->version, true );
		wp_enqueue_script( 'chartjs', plugin_dir_url( __FILE__ ) . 'js/chart.js', array(), $this->version, false );
		wp_enqueue_script( 'momentjs', plugin_dir_url( __FILE__ ) . 'js/moment.js', array(), $this->version, true );
		wp_enqueue_script( 'daterangepicker', plugin_dir_url( __FILE__ ) . 'js/daterangepicker.js', array(), $this->version, true );
		wp_localize_script( $this->plugin_name, 'wgAjax', array( 'ajax_url' => admin_url( 'admin-ajax.php')));
		wp_enqueue_media();
		
	}

	/** 
	 * 
	 * Display the admin page
	 * 
	 */

	public function display_admin_page() {
		add_menu_page(
			'wGauge Console', // Page title
			'wGauge', // Menu title
			'manage_options', // Capability
			'wgauge-console', // Menu slug
			array($this,'displayAdminPage'), // Function
			'', // Icon url
			'3.0' //Position from the top
		);
	}

	public function displayAdminPage() {
    	require(plugin_dir_path( __FILE__ ) . 'partials/wgauge-admin-display.php' );
	}

	/**
	 * 
	 * Add post meta-box
	 * 
	 */

	public function wg_meta_box_cb($post)
	{	
		global $post;
		$value = get_post_custom($post->ID);
		//echo $value['wg-active-toggle'][0];
		$check = isset($value['wg-active-toggle']) ? esc_attr($value['wg-active-toggle'][0]) : '';
		wp_nonce_field( 'wg_meta_box_nonce', 'meta_box_nonce' );
		?>
		<p>
			<label for="wg-active-toggle">Toggle wGauge</label>
			<input type="checkbox" name="wg-active-toggle" id="wg-active-toggle" <?php checked($check, 'on'); ?>/>
		</p>
		<?php
	}
	
	public function add_wg_meta_box()
	{
		$types = array('post', 'page');
		foreach ($types as $type) {
			add_meta_box("wg-page-active", // ID
			"wGauge", // Title
			array($this, 'wg_meta_box_cb'), // Callback 
			$type, // Page type
			"side", 
			"high", // Priority
			null);
		}
		
	}

	public function wg_meta_box_save($post_id) {
		// Don't save if autosaving
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

		// Verify nonce
		if(!isset($_POST['meta_box_nonce']) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'wg_meta_box_nonce' )) return;

		// If the user can't edit, don't allow them to save
		if(!current_user_can('edit_post')) return;

		// Save the state
		$chk = isset($_POST['wg-active-toggle']) ? 'on' : 'off';
		update_post_meta($post_id, 'wg-active-toggle', $chk);
		$score_array = get_post_meta($post_id, 'wg_score_array', true);
		if ($score_array == null) {
			update_post_meta($post_id, 'wg_score_array', array(0));
		}
		$score_array = get_post_meta($post_id, 'wg_score_array', true);
		$us_score_array = unserialize($score_array);
	}

	/** BACKEND FUNCTIONS */

	public function wgGetImage($img){
		return(plugins_url('img/' . $img ,__FILE__ ));
	}

	public function wgGetPages() {
		$pages = get_posts(array(
			'post_type' => 'any',
			'meta_key' => 'wg-active-toggle', 
			'meta_value' => 'on', 
			'numberposts' => -1,
			'orderby' => 'ID',
			'order' => 'ASC'
		)); 
		$i = 0;
		foreach ( $pages as $page ) {
			$gauges = floor(get_post_meta($page->ID, 'wg_gauges', true));
			$comments = floor(get_post_meta($page->ID, 'wg_comments_count', true));
			$score = floor(get_post_meta($page->ID, 'wg_score_avg', true));
			$grade = unserialize(get_post_meta($page->ID, 'wg_grade', true));

			if ($page->post_title == '') {
				$title = 'Unnamed Post';
			} else {
				$title = $page->post_title;
			}

			if ($i == 0) {
			  $listItem =  "<ul class='wg-list--row wg-list--card wg-page-tile wg-page-tile--active' data-id='$page->ID' data-title='$page->post_title'>";
			  $i++;
			} else {
				$listItem =  "<ul class='wg-list--row wg-list--card wg-page-tile' data-id='$page->ID' data-title='$page->post_title'>";
			}

			if ($grade['letter'] != '') {
				$gradeLetter = "<div class='wg-rating-badge " .  $grade['classname'] . "'>" . $grade['letter'] . "</div>";
			} else {
				$gradeLetter = "<div style='padding: 18px'></div>";
			}

			$classname = $grade['classname'];
		  	$listItem .= "<li>$title</li>";
		  	$listItem .= "<li>$gauges</li>";
		  	$listItem .= "<li>$comments</li>";
		  	$listItem .= "<li>$score/100</li>";
		  	$listItem .= "<li>$gradeLetter</li>";
		  	$listItem .= "</ul>";
		  	echo $listItem;
		}
	}

	public function wgGetDate(){
		$date = date('Y-m-d');
		return $date;
	}

	public function wg_query_data(){
		global $wpdb;
		// Rating 1-100
		$start = sanitize_text_field($_POST['start'] . ' 00:00:00');
		// Feedback left (optional)
		$end = sanitize_text_field($_POST['end'] . ' 23:59:59');

		$table = $wpdb->prefix . 'wg_feedback';
		$query = "SELECT ";
		$query .= "DATE(`$table`.`timestamp`) AS `date`,";
		$query .= "SUM(CASE WHEN `rating` >= 0 AND `rating` <= 20 THEN 1 ELSE 0 END) AS `F`,";
		$query .= "SUM(CASE WHEN `rating` >= 21 AND `rating` <= 30 THEN 1 ELSE 0 END) AS `D`,";
		$query .= "SUM(CASE WHEN `rating` >= 31 AND `rating` <= 50 THEN 1 ELSE 0 END) AS `C`,";
		$query .= "SUM(CASE WHEN `rating` >= 51 AND `rating` <= 80 THEN 1 ELSE 0 END) AS `B`,";
		$query .= "SUM(CASE WHEN `rating` >= 81 AND `rating` <= 100 THEN 1 ELSE 0 END) AS `A`,";
		$query .= "AVG(rating) AS 'score'";
		$query .= "FROM `$table`";
		$query .= "WHERE (`$table`.`timestamp` >= '$start' AND `$table`.`timestamp` <= '$end')";
		$query .= "GROUP BY `date` ";
		$query .= "ORDER BY `date`";
		try {
			
			$results = $wpdb->get_results($query);

		} catch (Exception $e) {
			echo $e->getMessage();
			echo 'Feedback failed to send';
			error_log('He is dead Jim');
			die();
		}
		//echo json_encode($results);
		//$results['sql'] = $wpdb->last_query;
		wp_send_json($results);
		die();

	}

	public function wg_page_data(){
		global $wpdb;
		// Rating 1-100
		$start = sanitize_text_field($_POST['start'] . ' 00:00:00');
		// Feedback left (optional)
		$end = sanitize_text_field($_POST['end'] . ' 23:59:59');
		$page = sanitize_text_field($_POST['page']);
		$table = $wpdb->prefix . 'wg_feedback';
		$query = "SELECT ";
		$query .= "DATE(`$table`.`timestamp`) AS `date`, feedback, rating,";
		$query .= "SUM(CASE WHEN `rating` >= 0 AND `rating` <= 20 THEN 1 ELSE 0 END) AS `F`,";
		$query .= "SUM(CASE WHEN `rating` >= 21 AND `rating` <= 30 THEN 1 ELSE 0 END) AS `D`,";
		$query .= "SUM(CASE WHEN `rating` >= 31 AND `rating` <= 50 THEN 1 ELSE 0 END) AS `C`,";
		$query .= "SUM(CASE WHEN `rating` >= 51 AND `rating` <= 80 THEN 1 ELSE 0 END) AS `B`,";
		$query .= "SUM(CASE WHEN `rating` >= 81 AND `rating` <= 100 THEN 1 ELSE 0 END) AS `A`,";
		$query .= "AVG(rating) AS 'score'";
		$query .= "FROM `$table`";
		$query .= "WHERE (`$table`.`timestamp` >= '$start' AND `$table`.`timestamp` <= '$end') AND `$table`.`postid` = '$page' ";
		//$query .= "WHERE (`wp_wg_feedback`.`timestamp` BETWEEN '$start' AND '$end') AND `wp_wg_feedback`.`postid` = '$page' ";
		$query .= "GROUP BY `date`";
		$query .= "ORDER BY `date`";
		try {
			
			$results = $wpdb->get_results($query);

		} catch (Exception $e) {
			echo $e->getMessage();
			echo 'Feedback failed to send';
			error_log('He is dead Jim');
			die();
		}
		//$results['sql'] = $wpdb->last_query;
		wp_send_json($results);
		//echo $wpdb->last_query;
		die();

	}

	public function wg_page_comments(){
		global $wpdb;
		// Rating 1-100
		$start = sanitize_text_field($_POST['start']) . ' 00:00:00';
		// Feedback left (optional)
		$end = sanitize_text_field($_POST['end']) . ' 23:59:59';
		$page = sanitize_text_field($_POST['page']);
		$table = $wpdb->prefix . 'wg_feedback';
		$query = "SELECT ";
		$query .= "DATE(`$table`.`timestamp`) AS `date`, feedback, rating ";
		$query .= "FROM `$table`";
		$query .= "WHERE (`$table`.`timestamp` >= '$start' AND `$table`.`timestamp` <= '$end') AND `$table`.`postid` = '$page' ";
		$query .= "ORDER BY `date`";
		try {
			
			$results = $wpdb->get_results($query);

		} catch (Exception $e) {
			echo $e->getMessage();
			echo 'Feedback failed to send';
			error_log('He is dead Jim');
			die();
		}
		//echo var_dump($results);
		//$results['sql'] = $wpdb->last_query;
		wp_send_json($results);
		//echo $wpdb->last_query;
		die();

	}
	
}
