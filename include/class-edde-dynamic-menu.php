<?php
if ( ! class_exists( 'EddE_Dynamic_Menu' ) ) {
	class EddE_Dynamic_Menu {

		private $meta_key;

		public function __construct() {
			$this->meta_key = 'edd_download_files';
			add_filter( 'register_post_type_args', array( $this, 'download_custom_post_type_args' ), 20, 2 );
			add_action( 'init', array( $this, 'edde_register_menus' ) );
			add_filter( 'wp_nav_menu_items', array( $this, 'edde_add_dynamic_section' ), 10, 2 );
		}

		/***
		 * Register specific locations for dynamic menu
		 */
		function edde_register_menus() {
			register_nav_menus(
				array(
					'edd-dynamic-menu' => esc_html__( 'EDD Dynamic Menu' ),
				)
			);

			return true;
		}

		/***
		 * Add to menu dynamic content
		 *
		 * @param $items
		 * @param $args
		 *
		 * @return string
		 */
		function edde_add_dynamic_section( $items, $args ) {
			$user_status = $this->get_user_status();
			if ( "edd-dynamic-menu" === $args->theme_location ) {
				switch ( $user_status ) {
					case 'customer':
						$edd_settings     = get_option( 'edd_settings' );

						$items            .= '<li><a href="' . get_post_type_archive_link( "downloads" ) . '">' . esc_html__( 'Downloads' ) . '</a>';
						$items            .= '<div>' . $this->selection_list_render() . '</div>';
						$items            .= '</li>';
						$items            .= '<li id="edde-logout"><a href="' . wp_logout_url() . '">' . esc_html__( 'Logout' ) . '</a></li>';
						break;
					case 'guest':
						$items .= '<li id="edde-logout"><a href="' . wp_logout_url() . '">' . esc_html__( 'Logout' ) . '</a></li>';
						break;
					case 'unknown':
						$items .= '<li id="edde-login"><a href="' . wp_login_url() . '">' . esc_html__( 'Login' ) . '</a> / <a href="' . wp_registration_url() . '"> ' . esc_html__( 'Registration' ) . '</a></li>';
						break;
				}
			} else {
				return $items;
			}

			return $items;
		}

		/***
		 * define current users status
		 *
		 * @return string
		 */
		function get_user_status() {
			$status         = 'unknown';
			$user_id        = get_current_user_id();
			$args           = array(
				'numberposts' => 1,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => 'download',
				'post_status' => 'publish',
			);
			$last_selection = get_posts( $args, OBJECT );
			if ( $user_id ) {
				$customer           = new EDD_Customer( $user_id, true );
				$payments           = $customer->get_payments();
				$customer_payments  = end( $payments );
				$customer_downloads = $customer_payments->downloads;
				$last_download      = (string) $last_selection[0]->ID;

				foreach ( $customer_downloads as $item ) {
					if ( $last_download == $item['id'] ) {
						$status = 'customer';
					} else {
						$status = 'guest';
					}
				}
			}

			return $status;
		}

		/***
		 * List of links for a daily archive pages
		 *
		 * @return string
		 */
		function weekly_selection_list_render() {
			$args = array(
				'type'            => 'daily',
				'limit'           => 8,
				'format'          => 'custom',
				'show_post_count' => false,
				'echo'            => false,
				'order'           => 'DESC',
				'post_type'       => 'download'
			);

			$links = wp_get_archives( $args );
			preg_match_all( '/<a(.*)<\/a>/U', $links, $output_array );
			$item  = '<span>' . esc_html__( '	Weekly Results Menu' ) . '</span>';
			$item  .= '<ul class="selection sub-nemu">';
			$count = count( $output_array[0] );
			for ( $i = 1; $i < $count; $i ++ ) {
				$item .= '<li>' . $output_array[0][ $i ] . '</li>';
			}
			$item .= '</ul>';

			return $item;
		}

		/***
		 * List of downloads actual for today
		 *
		 * @return string
		 */
		function selection_list_render() {
			$args           = array(
				'numberposts' => 1,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => 'download',
				'post_status' => 'publish',
			);
			$last_selection = get_posts( $args, OBJECT )[0];
			$downloads      = get_post_meta( $last_selection->ID, $this->meta_key, true );
			$item           = '<span>' . esc_html__( 'Daily Selections Menu' ) . '</span>';
			$item           .= '<div>' . $last_selection->post_title . '</div>';
			$item           .= '<ul class="selection sub-nemu">';
			foreach ( $downloads as $d_item ) {
				$item .= '<li><a href="' . $d_item['file'] . '">' . $d_item['name'] . '</a></li>';
			}
			$item .= '</ul>';

			return $item;
		}

		function download_custom_post_type_args( $args, $post_type ) {
			if ( "download" === $post_type ) {
				$args['has_archive'] = true;
				$args['public']      = true;
			}

			return $args;
		}

	}
}
new EddE_Dynamic_Menu();