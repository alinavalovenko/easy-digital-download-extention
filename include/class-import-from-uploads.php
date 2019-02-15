<?php
if ( ! class_exists( 'Import_From_Uploads' ) ) {

	class Import_From_Uploads {
		public function __construct( $path = ROOT_UPLOAD_FOLDER ) {
			$this->get_files_list( $path );
		}

		function get_files_list( $path ) {
			$files_list = scandir( $path );
			foreach ( $files_list as $item ) {
				if ( $item != '.' && $item != '..' ) {
					$new_download  = new EDD_Download;
					$download_args = array(
						'post_title'   => sanitize_file_name( $item ),
						'post_content' => '',
						'post_status'  => 'publish',
						'price'        => '10'
					);
					$new_download->create( $download_args );
					$item_id = $new_download->ID;
					if ( ! $item_id ) {
						die( 'Import wasn\'t success' );
					} else {
						$this->edde_ser_price( $item_id );
						$this->edde_set_download_info( $item_id, $item, $path );
					}
				}
			}

			return true;
		}


		/***
		 * Set up price
		 *
		 * @param $id
		 */
		function edde_ser_price( $id ) {
			$meta_key   = 'edd_price';
			$meta_value = floatval( 10 );
			add_post_meta( $id, $meta_key, $meta_value );
		}


		/***
		 * Set up downloads item info
		 *
		 * @param $id
		 * @param $item
		 * @param $path
		 */
		function edde_set_download_info( $id, $item, $path ) {
			$meta_key   = 'edd_download_files';
			$file_name = sanitize_file_name( $item );
			$file_path = $path . $item;
			$wp_filetype = wp_check_filetype( $file_path, null );

			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => $file_name,
				'post_content' => '',
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $item );

			$meta_value = array(
				0 => array(
					'index'          => '0',
					'attachment_id'  =>$attach_id,
					'thumbnail_size' => 'medium',
					'name'           => $file_name,
					'file'           => $file_path,
					'condition'      => 'all',
				)
			);

			add_post_meta( $id, $meta_key, $meta_value );
		}
	}

}