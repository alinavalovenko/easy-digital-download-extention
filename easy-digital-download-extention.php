<?php
/*
	Plugin Name: TBR Simulator Manager
	Description: Create 'download' Custom post type from files were uploaded by ftp
	Version: 1.0
	Author: Alina Valovenko
	Author URI: http://www.valovenko.pro
	License: GPL2
*/
if ( ! class_exists( 'Easy_Digital_Download_Extension' ) ) {
	if ( ! defined( 'NONE_ELEMENT' ) ) {
		define( 'NONE_ELEMENT', '---NONE---' );
	}
	if ( ! defined( 'ROOT_UPLOAD_FOLDER' ) ) {
		define( 'ROOT_UPLOAD_FOLDER', wp_get_upload_dir()['basedir'] );
	}

	if ( ! defined( 'ROOT_UPLOAD_FOLDER_URL' ) ) {
		define( 'ROOT_UPLOAD_FOLDER_URL', wp_get_upload_dir()['baseurl'] );
	}

	if ( ! defined( 'EDDE_DASHBOARD' ) ) {
		define( 'EDDE_DASHBOARD', plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'dashboard.php' );
	}

	if ( ! defined( 'EDDE_DIR_URL' ) ) {
		define( 'EDDE_DIR_URL', plugin_dir_url( __FILE__ ) );
	}

	include_once 'include/controller.php';

	class Easy_Digital_Download_Extension {

		public function __construct() {
			$page = new EddE_Admin();
		}
	}

	new Easy_Digital_Download_Extension();
}