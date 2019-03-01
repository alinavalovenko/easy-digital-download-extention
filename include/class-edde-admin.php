<?php

class EddE_Admin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'edde_add_admin_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'edde_enqueue_scripts' ), 99 );
		add_action( 'wp_ajax_get_folders_list', array( $this, 'get_folders_list_ajax' ) );
		add_action( 'wp_ajax_save_items_as_downloads', array( $this, 'save_items_as_downloads_ajax' ) );
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
		wp_enqueue_style( 'edde-style', EDDE_DIR_URL . 'assets/styles.css' );
		wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.3.1.min.js', '', '1.0.0', true );
		wp_enqueue_script( 'edde-scripts', EDDE_DIR_URL . 'assets/scripts.js', array( 'jquery' ), '1.0.0', true );

		wp_localize_script( 'edde-scripts', 'eddeInfo', array(
			'directorySeparator' => DIRECTORY_SEPARATOR,
			'basePath' => ROOT_UPLOAD_FOLDER . DIRECTORY_SEPARATOR
		) );
	}

	/***
	 * Dashboard Callback Function
	 */
	function edde_admin_page_callback() {
		$this->edde_enqueue_scripts();
		include_once EDDE_DASHBOARD;
	}

	/***
	 * Get list of all available folders by path
	 *
	 * @param null $path
	 *
	 * @return array|false
	 */
	public function get_folders_list( $path = null ) {
		if ( empty( $path ) ) {
			$path = get_home_path();
		}
		$folders = glob( $path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR );

		return $folders;
	}


	/***
	 * Display folder content by path
	 */
	function get_folders_list_ajax() {
		$path              = str_replace( '\\\\', DIRECTORY_SEPARATOR, $_POST['path'] );
		$path              = $path . DIRECTORY_SEPARATOR;
		$list              = $this->get_folders_list( $path );
		$output            = [];
		$output['path']    = $path;
		$output['folders'] = '';
		$output['files']   = '';
		ob_start();
		if ( $list ) {
			$output['folders'] .= '<option>' . NONE_ELEMENT . '</option>';
			foreach ( $list as $item ) {
				$output['folders'] .= '<option>' . substr( $item, strripos( $item, DIRECTORY_SEPARATOR ) + 1 ) . '</option>';
			}
		}
		$available_files = get_all_available_files( $path );
		if ( $available_files ) {
			$output['files'] = edde_dispay_available_files( $available_files );
		}
		ob_end_clean();
		echo json_encode( $output );
		wp_die();
	}

	/***
	 * Convert selected files to downloads post type
	 */
	function save_items_as_downloads_ajax() {
		$path = str_replace( '\\\\', '\\', $_POST['path'] );
		$date = $_POST['selection_date'];
		if ( isset( $_POST['selected_files'] ) ) {
			$files = $this->edde_selected_files_to_array( $_POST['selected_files'] );
		} else {
			$files = false;
		}

		$result = new Import_From_Uploads( $path, $files, $date);
		if ( $result ) {
			echo 'Success!';
		} else {
			echo 'Something went wrong =(';
		}
		wp_die();
	}

	/***
	 * Convert files post data to array
	 *
	 * @param $post_data
	 *
	 * @return array
	 */
	function edde_selected_files_to_array( $post_data ) {
		$files_name = [];
		if (!empty($post_data) ){
			foreach ($post_data as $item){
				array_push($files_name, $item['value']);
			}
		}

		return $files_name;
	}
}