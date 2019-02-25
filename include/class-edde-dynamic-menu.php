<?php
if ( ! class_exists( 'EddE_Dynamic_Menu' ) ) {
	class EddE_Dynamic_Menu {

		public function __construct() {
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
						$items .= '<li>' . 'customer' . '</li>';
						break;
					case 'guest':
						$items .= '<li>' . 'guest' . '</li>';
						break;
					case 'unknown':
						$items .= '<li>' . 'User is unknown' . '</li>';
						break;
				}
			} else {
				return $items;
			}

			return $items;
		}


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
	}
}
new EddE_Dynamic_Menu();