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
			add_action( 'wp_enqueue_scripts', array( $this, 'edde_enqueue_scripts' ) );
			add_action( 'wp_ajax_get_folders_list', array( $this, 'get_folders_list_ajax' ) );


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
			if ( function_exists( 'is_plugin_active' ) ) {
				if ( ! is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
					return;
				}
				add_submenu_page( 'edit.php?post_type=download', 'Easy Digital Download Extension', 'ED Download Extension', 'manage_options', 'edde_options', array(
					$this,
					'edde_admin_page_callback'
				) );
			}
		}

		/***
		 * Enqueue scripts
		 */
		function edde_enqueue_scripts() {
			wp_enqueue_style( 'edde-style', plugin_dir_url( __FILE__ ) . 'assets/styles.css' );
			wp_enqueue_script( 'edde-jquery', 'https://code.jquery.com/jquery-3.3.1.min.js', '', '1.0.0', true );
			wp_enqueue_script( 'edde-scripts', plugin_dir_url( __FILE__ ) . 'assets/scripts.js', array( 'edde-jquery' ), '1.0.0', true );
		}

		/***
		 * Dashboard Callback Function
		 */
		function edde_admin_page_callback() {
			$this->edde_enqueue_scripts();
			if ( isset( $_FILES ) ) {
				//make magic
			}

			include_once 'dashboard.php';
		}

		public function get_folders_list( $path = null ) {
			if ( empty( $path ) ) {
				$path = get_home_path();
			}
			$folders = glob( $path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR );

			return $folders;
		}

		function get_folders_list_ajax() {
			$path   = $_POST['path'];
			$list   = $this->get_folders_list( $path );
			$output = [];
			ob_start();
			if ( $list ) {
				foreach ( $list as $item ) {
					echo '<option>' . substr( $item, strripos(  $item, DIRECTORY_SEPARATOR ) +1 ) . '</option>';
				}
			}
			$output['path'] = $path;
			$output['folders'] = ob_get_contents();
			ob_end_clean();
			echo json_encode($output);
			wp_die();
		}
	}

	new Easy_Digital_Download_Extension();
}