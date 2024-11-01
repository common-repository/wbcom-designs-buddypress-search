<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/includes
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Search_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$bpsearch_bp_search = get_option( 'bpsearch_bp_search' );
		if ( empty( $bpsearch_bp_search ) ) {
			$bpsearch_bp_search['bp_search_activity']               = 1;
			$bpsearch_bp_search['bp_search_groups']                 = 1;
			$bpsearch_bp_search['bp_search_groups_public']          = 1;
			$bpsearch_bp_search['bp_search_groups_private']         = 1;
			$bpsearch_bp_search['bp_search_groups_hidden']          = 1;
			$bpsearch_bp_search['bp_search_members']                = 1;
			$bpsearch_bp_search['bp_search_members']                = 1;
			$bpsearch_bp_search['bp_search_member']['user_meta']    = 1;
			$bpsearch_bp_search['bp_search_member']['display_name'] = 1;
			$bpsearch_bp_search['bp_search_member']['user_email']   = 1;
			$bpsearch_bp_search['bp_search_member']['user_login']   = 1;
			update_option( 'bpsearch_bp_search', $bpsearch_bp_search );
		}
		$bpsearch_pages_posts_search = get_option( 'bpsearch_pages_posts_search' );
		if ( empty( $bpsearch_pages_posts_search ) ) {
			$bpsearch_pages_posts_search['bp_post']           = 1;
			$bpsearch_pages_posts_search['post']['category']  = 1;
			$bpsearch_pages_posts_search['post']['post_tag']  = 1;
			$bpsearch_pages_posts_search['post']['meta_data'] = 1;

			$bpsearch_pages_posts_search['bp_page']           = 1;
			$bpsearch_pages_posts_search['page']['meta_data'] = 1;
			update_option( 'bpsearch_pages_posts_search', $bpsearch_pages_posts_search );
		}

		$bpsearch_live_search_settings = get_option( 'bpsearch_pages_posts_search' );
		if ( empty( $bpsearch_live_search_settings ) ) {
			$bpsearch_live_search_settings['enable_live_search'] = 1;
			$bpsearch_live_search_settings['number_of_results']  = 5;
			update_option( 'bpsearch_live_search_settings', $bpsearch_live_search_settings );
		}

	}

}
