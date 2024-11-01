<?php
/**
 * BuddyPress search bbPress forums.
 *
 * @todo add description
 *
 * @package buddypress_search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_bbPress_Forums' ) ) :

	/**
	 * BuddyPress Global Search  - search bbpress forums class
	 */
	class BuddyPress_Search_bbPress_Forums extends BuddyPress_Search_bbPress {

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var $type forum.
		 */
		public $type = 'forum';

		/**
		 * MY SQL Query.
		 *
		 * @param  mixed  $search_term Search text.
		 * @param  boolen $only_totalrow_count Total Row Count.
		 */
		public function sql( $search_term, $only_totalrow_count = false ) {
			global $wpdb;

			$bp_prefix = bp_core_get_table_prefix();

			$query_placeholder = array();

			if ( $only_totalrow_count ) {
				$columns = ' COUNT( DISTINCT p.id ) ';
			} else {
				$columns             = " DISTINCT p.id , '{$this->type}' as type, p.post_title LIKE %s AS relevance, p.post_date as entry_date  ";
				$query_placeholder[] = '%' . $search_term . '%';
			}

			$from = "{$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_bbp_group_ids'";

			/**
			 * Filter the MySQL JOIN clause for the forum Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param string $join_sql JOIN clause.
			 */
			$from = apply_filters( 'bp_forums_search_join_sql', $from );

			$where   = array();
			$where[] = '1=1';
			$where[] = "(post_title LIKE %s OR ExtractValue(post_content, '//text()') LIKE %s)";
			$where[] = "post_type = '{$this->type}'";

			if ( current_user_can( 'read_hidden_forums' ) ) {
				$post_status = array( 'publish', 'private', 'hidden' );
			} elseif ( current_user_can( 'read_private_forums' ) ) {
				$post_status = array( 'publish', 'private' );
			} else {
				$post_status = array( 'publish' );
			}

			$in = '0';

			$where[] = '( post_status IN (\'' . join( '\',\'', $post_status ) . '\') OR pm.meta_value IN (' . trim( $in, ',' ) . ') )';

			$query_placeholder[] = '%' . $search_term . '%';
			$query_placeholder[] = '%' . $search_term . '%';

			/**
			 * Filters the MySQL WHERE conditions for the forum Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $where_conditions Current conditions for MySQL WHERE statement.
			 */
			$where = apply_filters( 'bp_forums_search_where_sql', $where );

			$sql   = 'SELECT ' . $columns . ' FROM ' . $from . ' WHERE ' . implode( ' AND ', $where );
			$query = $wpdb->prepare( $sql, $query_placeholder ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			return apply_filters(
				'bp_search_forums_sql',
				$query,
				array(
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return object Bp_Search_Forums
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication.
			static $instance = null;

			// Only run these methods if they haven't been run previously.
			if ( null === $instance ) {
				$instance = new BuddyPress_Search_bbPress_Forums();
			}

			// Always return the instance.
			return $instance;
		}

		/**
		 * A dummy constructor to prevent this class from being loaded more than once.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			/* Do nothing here */
		}

	}

	// End class Bp_Search_Posts.

endif;

