<?php
if ( ! class_exists( 'EddE_Dynamic_Menu' ) ) {
	class EddE_Dynamic_Menu {

		private $meta_key;
		private $date_format;
		public $today;

		public function __construct() {
			$this->meta_key    = 'edd_download_files';
			$this->date_format = get_option( 'date_format' );
			$this->today       = date( $this->date_format );
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
		 * @throws Exception
		 */
		function edde_add_dynamic_section( $items, $args ) {
			$user_status = $this->get_user_status();
			if ( "edd-dynamic-menu" === $args->theme_location || 'primary-menu' === $args->theme_location ) {
				switch ( $user_status ) {
					case 'customer':
						$edd_settings = get_option( 'edd_settings' );

						$items .= '<li class="menu-item menu-item-has-children"><a>' . esc_html__( 'Downloads' ) . '</a>';
						$items .= '<ul class="sub-menu">' . $this->selection_list_render();
						$items .= $this->tomorrow_selection_list_render();
						$items .= '</ul>' . '</li>';
						$items .= '<li id="edde-logout"><a href="' . wp_logout_url() . '">' . esc_html__( 'Logout' ) . '</a></li>';
						break;
					case 'guest':
						$items .= '<li id="edde-logout"><a href="' . wp_logout_url() . '">' . esc_html__( 'Logout' ) . '</a></li>';
						break;
					case 'unknown':
						$items .= '<li id="edde-login"><a href="' . wp_login_url() . '">' . esc_html__( 'Login' ) . '</a></li>';
						$items .= '<li id="edde-login"><a href="' . wp_registration_url() . '"> ' . esc_html__( 'Registration' ) . '</a></li>';
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
			$status          = 'unknown';
			$user_id         = get_current_user_id();
			$today_selection = get_page_by_title( $this->today, OBJECT, 'download' );
			if ( $user_id ) {
				$customer           = new EDD_Customer( $user_id, true );
				$payments           = $customer->get_payments();
				$customer_payments  = end( $payments );
				$customer_downloads = $customer_payments->downloads;
				foreach ( $customer_downloads as $item ) {
					if ( $today_selection && $today_selection->ID == $item['id'] ) {
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
		 * @throws Exception
		 */
		function tomorrow_selection_list_render() {
			$item               = '';
			$tomorrow           = new DateTime( 'tomorrow' );
			$tomorrow           = $tomorrow->format( $this->date_format );
			$tomorrow_selection = get_page_by_title( $tomorrow, OBJECT, 'download' );
			if ( $tomorrow_selection ) {
				$downloads = get_post_meta( $tomorrow_selection->ID, $this->meta_key, true );
				$item      = '<li>' . $tomorrow_selection->post_title;
				$item      .= '<ul class="selection sub-nemu">';
				foreach ( $downloads as $d_item ) {
					$item .= '<li><a href="' . $d_item['file'] . '">' . $d_item['name'] . '</a></li>';
				}
				$item .= '</ul>' . '</li>';
			}

			return $item;
		}

		/***
		 * List of downloads actual for today
		 *
		 * @return string
		 */
		function selection_list_render() {
			$last_selection = get_page_by_title( $this->today, OBJECT, 'download' );
			$downloads      = get_post_meta( $last_selection->ID, $this->meta_key, true );

			$item = '<li>' . $last_selection->post_title;
			$item .= '<ul class="selection sub-menu">';
			foreach ( $downloads as $d_item ) {
				$item .= '<li><a href="' . $d_item['file'] . '">' . $d_item['name'] . '</a></li>';
			}
			$item .= '</ul>';

			return $item . '</li>';
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