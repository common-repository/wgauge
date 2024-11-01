<?php

/**
 * Fired during plugin activation
 *
 * @link       www.jacobanderson.co.uk
 * @since      1.0.0
 *
 * @package    Wgauge
 * @subpackage Wgauge/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wgauge
 * @subpackage Wgauge/includes
 * @author     Jacob Anderson <hey@jacobanderson.co.uk>
 */



class Wgauge_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	public static function activate() {
		global $table_prefix, $wpdb;

		add_option('wg_attention_msg', 'Rate This Page');
		
		$wg_feedback = $wpdb->prefix . 'wg_feedback';
		$charset_collate = $wpdb->get_charset_collate();

		#Check to see if the table exists already, if not, then create it

		if($wpdb->get_var( "SHOW TABLES LIKE '{$wg_feedback}'" ) != $wg_feedback) 
		{
			$sql = "CREATE TABLE $wg_feedback (
				id  INT(12)   NOT NULL auto_increment,
				postid  INT(128)   NOT NULL,
				user  INT(128)   NOT NULL,
				timestamp  TIMESTAMP   NOT NULL,
				ip  VARCHAR(8),
				rating  INT(2)   NOT NULL,
				feedback  VARCHAR(255),
				PRIMARY KEY (id)
			) $charset_collate;";
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
		
	}
		 
}


