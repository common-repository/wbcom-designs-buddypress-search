<?php
/**
 * BuddyPress search types.
 *
 * @todo add description
 *
 * @package BuddyPress_Search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_Type' ) ) :

	/**
	 * BuddyPress Global Search Type - Parent class
	 */
	abstract class BuddyPress_Search_Type {

		/**
		 * Search term might be used later for caching purposes
		 *
		 * @var string
		 */
		protected $search_term = '';

		/**
		 * The variable to hold search results.
		 *
		 * @var array
		 */
		protected $search_results = array(
			'total_match_count' => false,
			'items'             => array(),
			'items_title'       => array(),
			'html_generated'    => false,
		);

		/**
		 * A dummy magic method to prevent this class from being cloned.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'buddypress-search' ), '1.7' );
		}

		/**
		 * A dummy magic method to prevent this class being unserialized.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin\' huh?', 'buddypress-search' ), '1.7' );
		}

		/**
		 * Returns the sql query to be used in performing an 'all' items search.
		 *
		 * @param string $search_term
		 * @return string sql query
		 * @since 1.0.0
		 */
		public function union_sql( $search_term ) {
			$this->search_term = $search_term;// save it for future reference may be.

			return $this->sql( $search_term );
		}

		/**
		 * Add search item.
		 *
		 * @param int $item_id Item ID.
		 */
		public function add_search_item( $item_id ) {
			if ( ! in_array( $item_id, $this->search_results['items'] ) ) {
				$this->search_results['items'][ $item_id ] = '';
			}
		}

		/**
		 * Get the title.
		 *
		 * @param int $item_id Item ID.
		 */
		public function get_title( $item_id ) {
			if ( ! $this->search_results['html_generated'] ) {
				$this->generate_html();
				$this->search_results['html_generated'] = true;// do once only.
			}

			return isset( $this->search_results['items'][ $item_id ]['title'] ) ? $this->search_results['items'][ $item_id ]['title'] : $this->search_term;
		}

		/**
		 * Get the total matches.
		 *
		 * @param string $search_term Search Term.
		 */
		public function get_total_match_count( $search_term ) {
			$this->search_term = $search_term;// save it for future reference may be.

			global $wpdb;
			$sql = $this->sql( $search_term, true );
			return $wpdb->get_var( $sql );
		}

		/**
		 * This function must be overriden by inheritingn classes
		 *
		 * @param string  $search_term Search Term.
		 * @param boolean $only_totalrow_count Total Row Count.
		 */
		abstract public function sql( $search_term, $only_totalrow_count = false );

		/**
		 * Get the html for given search result.
		 *
		 * @param int    $itemid Item ID.
		 * @param string $template_type Optional.
		 * @return string
		 */
		public function get_html( $itemid, $template_type = '' ) {
			if ( ! $this->search_results['html_generated'] ) {
				$this->generate_html( $template_type );
				$this->search_results['html_generated'] = true;// do once only.
			}

			return ( isset( $this->search_results['items'][ $itemid ] ) && isset( $this->search_results['items'][ $itemid ]['html'] ) )? @$this->search_results['items'][ $itemid ]['html'] : '';
		}
	}

	// End class BuddyPress_Search_Type.

endif;

