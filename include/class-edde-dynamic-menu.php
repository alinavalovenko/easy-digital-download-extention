<?php
if ( ! class_exists( 'EddE_Dynamic_Menu' ) ) {
	class EddE_Dynamic_Menu {

		public function __construct() {
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
						$purchase_page_id = $edd_settings['purchase_history_page'];
						$items            .= '<li><a href="' . get_post_type_archive_link("downloads").'">'. esc_html__('Downloads').'</a>';
						$items            .= '<div class="col-6">' . $this->daily_selection_list_render() . '</div>';
						$items            .= '<div class="col-6">' . $this->weekly_selection_list_render(). '</div>';
						$items            .= '</li>';
						$items            .= '<li id="edde-my-purchases"><a href="' . get_the_permalink( $purchase_page_id ) . '">' . get_the_title( $purchase_page_id ) . '</a>' . do_shortcode( "[download_history]" ) . '</li>';
						$items            .= '<li id="edde-logout"><a href="' . wp_logout_url() . '">' . esc_html__( 'Logout' ) . '</a></li>';
						break;
					case 'guest':
						$items            .= '<li><a href="' . get_post_type_archive_link("downloads").'">'. esc_html__('Downloads').'</a>';
						$items            .= '<div class="col-6">' . $this->daily_selection_list_render() . '</div>';
						$items            .= '<div class="col-6">' . $this->weekly_selection_list_render(). '</div>';
						$items            .= '</li>';
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
			$status  = 'unknown';
			$user_id = get_current_user_id();
			if ( $user_id ) {
				$customer = new EDD_Customer( $user_id, true );
				if ( $customer->purchase_count > 0 ) {
					$status = 'customer';
				} else {
					$status = 'guest';
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
		function daily_selection_list_render() {
			$args       = array(
				'type'            => 'daily',
				'limit'           => 1,
				'format'          => 'custom',
				'show_post_count' => false,
				'echo'            => false,
				'order'           => 'DESC',
				'post_type'       => 'download'
			);
			$item       = '<span>' . esc_html__( 'Daily Selections Menu' ) . '</span>';
			$item       .= '<div>' . wp_get_archives( $args ) . '</div>';
			$today      = getdate();
			$selections = get_posts( array(
				'numberposts' => '-1',
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_type'   => 'download',
				'date_query'  => array(
					array(
						'year'  => $today['year'],
						'month' => $today['mon'],
						'day'   => $today['mday'],
					),
				),
			) );
			$item       .= '<ul class="selection sub-nemu">';
			foreach ( $selections as $selection ) {
				$item .= '<li><a href="' . get_the_permalink( $selection->ID ) . '">' . $selection->post_title . '</a></li>';
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