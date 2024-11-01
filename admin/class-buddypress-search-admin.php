<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Search_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-search-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-search-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
		 * Hide all notices from the setting page.
		 *
		 * @return void
		 */
	public function wbcom_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress-search' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}

	}


	/**
	 * Register the admin menu for plugin.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_search_add_menu() {

		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-search' ), esc_html__( 'WB Plugins', 'buddypress-search' ), 'manage_options', 'wbcomplugins', array( $this, 'buddypress_search_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-search' ), esc_html__( 'General', 'buddypress-search' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Search Setting Page', 'buddypress-search' ), esc_html__( 'BuddyPress Search', 'buddypress-search' ), 'manage_options', 'buddypress-search', array( $this, 'buddypress_search_settings_page' ) );

	}

	/**
	 * Search setting page.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_search_settings_page() {
		$current = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
		?>
		<div class="wrap">
		<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
					<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/" target="_blank">
						<img src="<?php echo esc_url( BP_SEARCH_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
					</a>
				</div>
			</div>
		<div class="wbcom-wrap">
		<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">
							<?php esc_html_e( 'BuddyPress Search', 'buddypress-search' ); ?>
							<span><?php printf( esc_html__( 'Version %s', 'buddypress-search' ), esc_html__( BUDDYPRESS_SEARCH_VERSION ) ); ?></span>
						</div>
						<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
		<div class="wbcom-admin-settings-page">
		<?php

		$bpsts_tabs = array(
			'welcome'              => __( 'Welcome', 'buddypress-search' ),
			'bp-search'            => __( 'Search Components', 'buddypress-search' ),
			'pages-posts-search'   => __( 'Enable Post/Pages', 'buddypress-search' ),
			'live-search-settings' => __( 'Live Search Settings', 'buddypress-search' ),
		);

		$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bpsts_tabs as $bpsts_tab => $bpsts_name ) {
			$class     = ( $bpsts_tab == $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li class="' . $bpsts_tab . '"><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress-search&tab=' . $bpsts_tab . '">' . $bpsts_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo wp_kses_post( $tab_html );

		include 'inc/bpsearch-options-page.php';
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
		echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_search_add_setting() {
		register_setting( 'bpsearch_bp_search_section', 'bpsearch_bp_search' );
		register_setting( 'bpsearch_pages_posts_search_section', 'bpsearch_pages_posts_search' );
		register_setting( 'bpsearch_live_search_settings_section', 'bpsearch_live_search_settings' );
		register_setting( 'bpsearch_search_results_settings_section', 'bpsearch_search_results_settings' );

	}
}
