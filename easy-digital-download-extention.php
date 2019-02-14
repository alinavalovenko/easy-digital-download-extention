<?php
/*
	Plugin Name: Easy Digital Download Extension
	Description: Scan chosen directory and create download ctp
	Version: 1.0
	Author: Alina Valovenko
	Author URI: http://www.valovenko.pro
	License: GPL2
*/
if ( ! class_exists( 'Easy_Digital_Download_Extension' ) ) {

	class Easy_Digital_Download_Extension {

		public function __construct() {
			register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'edde_activate' ) );
			register_deactivation_hook( plugin_basename( __FILE__ ), array( $this, 'edde_deactivate' ) );
			register_uninstall_hook( plugin_basename( __FILE__ ), array( $this, 'edde_uninstall' ) );
			add_action( 'admin_menu', array( $this, 'edde_add_admin_page' ) );
		}

		/****
		 * Activation hook callback
		 */
		public function edde_activate() {
			return true;
		}

		/****
		 * Deactivation hook callback
		 */
		public function edde_deactivate() {
			return true;
		}

		/****
		 * Uninstall hook callback
		 */
		public function edde_uninstall() {
			return true;
		}

		/***
		 * Create dashboard for plugin
		 */
		public function edde_add_admin_page() {
			add_submenu_page( 'tools.php', 'Easy Digital Download Extension', 'ED Download Extension', 'manage_options', 'edde_options', array(
				$this,
				'edde_admin_page_callback'
			) );
		}

		/***
		 * Dashboard Callback Function
		 */
		function edde_admin_page_callback() {
			if ( isset( $_POST['action'] ) ) {
				//make magic
			}

			include_once 'dashboard.php';

			return true;
		}
	} new Easy_Digital_Download_Extension();
}