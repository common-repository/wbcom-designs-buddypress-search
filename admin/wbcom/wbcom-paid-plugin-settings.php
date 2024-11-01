<?php
/**
 * Class to add reviews shortcode.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wbcom_Paid_Plugin_Settings' ) ) {

	/**
	 * Class to serve AJAX Calls.
	 *
	 * @author   Wbcom Designs
	 * @since    1.0.0
	 */
	class Wbcom_Paid_Plugin_Settings {

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'wbcom_admin_license_page' ), 999 );
			add_action( 'wbcom_add_header_menu', array( $this, 'wbcom_add_header_license_menu' ) );
		}

		public function wbcom_admin_license_page() {
			add_submenu_page(
				'wbcomplugins',
				esc_html__( 'License', 'buddypress-search' ),
				esc_html__( 'License', 'buddypress-search' ),
				'manage_options',
				'wbcom-license-page',
				array( $this, 'wbcom_license_submenu_page_callback' )
			);
		}

	}

	function instantiate_wbcom_manager() {
		new Wbcom_Paid_Plugin_Settings();
	}

	instantiate_wbcom_manager();
}
