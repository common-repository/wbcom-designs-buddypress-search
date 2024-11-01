<?php
/**
 * BuddyPress Search Activities.
 *
 * @todo add description
 *
 * @package BuddyPress_Search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_Activities' ) ) :

	/**
	 * BuddyPress Global Search  - search activities class
	 */
	class BuddyPress_Search_Activities extends BuddyPress_Search_Type {

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var type Search Type.
		 */
		private $type = 'activity';

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return object BuddyPress_Search_Activities
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication.
			static $instance = null;

			// Only run these methods if they haven't been run previously.
			if ( null === $instance ) {
				$instance = new BuddyPress_Search_Activities();
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

		/**
		 * MY SQL Query.
		 *
		 * @param  mixed  $search_term Search text.
		 * @param  boolen $only_totalrow_count Total Row Count.
		 */
		public function sql( $search_term, $only_totalrow_count = false ) {

			/**
			 * SELECT DISTINCT a.id
			 * FROM wp_bp_activity a
			 * WHERE
			 *      a.is_spam = 0
			 *  AND a.content LIKE '%nothing%'
			 *  AND a.hide_sitewide = 0
			 *  AND a.type NOT IN ('activity_comment', 'last_activity')
			 *
			 * ORDER BY a.date_recorded DESC LIMIT 0, 21
			 */
			global $wpdb, $bp, $privacy_field_check;

			$bp_prefix = bp_core_get_table_prefix();

			$privacy_field_check = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$bp->activity->table_name} LIKE 'privacy'" ) );

			$query_placeholder = array();

			$user_groups = array();
			if ( bp_is_active( 'groups' ) ) {

				// Fetch public groups.
				$public_groups = groups_get_groups(
					array(
						'fields'   => 'ids',
						'status'   => 'public',
						'per_page' => - 1,
					)
				);
				if ( ! empty( $public_groups['groups'] ) ) {
					$public_groups = $public_groups['groups'];
				} else {
					$public_groups = array();
				}

				$groups = groups_get_user_groups( bp_loggedin_user_id() );
				if ( ! empty( $groups['groups'] ) ) {
					$user_groups = $groups['groups'];
				} else {
					$user_groups = array();
				}

				$user_groups = array_unique( array_merge( $user_groups, $public_groups ) );
			}

			$friends = array();
			if ( bp_is_active( 'friends' ) ) {

				// Determine friends of user.
				$friends = friends_get_friend_user_ids( bp_loggedin_user_id() );
				if ( empty( $friends ) ) {
					$friends = array( 0 );
				}
				array_push( $friends, bp_loggedin_user_id() );
			}

			$sql['select'] = 'SELECT';

			if ( $only_totalrow_count ) {
				$sql['select'] .= ' COUNT( DISTINCT a.id ) ';
			} else {
				$sql['select'] .= $wpdb->prepare( " DISTINCT a.id , 'activity' as type, a.content LIKE %s AS relevance, a.date_recorded as entry_date  ", '%' . $wpdb->esc_like( $search_term ) . '%' );
			}

			$privacy = array( 'public' );
			if ( is_user_logged_in() ) {
				$privacy[] = 'loggedin';
			}

			$sql['from'] = "FROM {$bp->activity->table_name} a";

			/**
			 * Filter the MySQL JOIN clause for the activity Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param string $join_sql JOIN clause.
			 */
			$sql['from'] = apply_filters( 'bp_activity_search_join_sql', $sql['from'] );

			// searching only activity updates, others don't make sense.
			$where_conditions = array( '1=1' );
			if ( 'privacy' == $privacy_field_check ) {
				$where_conditions[] = "is_spam = 0
						AND ExtractValue(a.content, '//text()') LIKE %s
						AND a.hide_sitewide = 0
						AND a.type = 'activity_update'
						AND
						(
							( a.privacy IN ( '" . implode( "','", $privacy ) . "' ) and a.component != 'groups' ) " .
								( isset( $user_groups ) && ! empty( $user_groups ) ? " OR ( a.item_id IN ( '" . implode( "','", $user_groups ) . "' ) AND a.component = 'groups' )" : '' ) .
								( bp_is_active( 'friends' ) && ! empty( $friends ) ? " OR ( a.user_id IN ( '" . implode( "','", $friends ) . "' ) AND a.privacy = 'friends' )" : '' ) .
								( is_user_logged_in() ? " OR ( a.user_id = '" . bp_loggedin_user_id() . "' AND a.privacy = 'onlyme' )" : '' ) .
								')';
			} else {
				$where_conditions[] = "is_spam = 0
						AND ExtractValue(a.content, '//text()') LIKE %s
						AND a.hide_sitewide = 0
						AND a.type = 'activity_update'";
			}

			/**
			 * Filters the MySQL WHERE conditions for the activity Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $where_conditions Current conditions for MySQL WHERE statement.
			 * @param string $search_term      Search Term.
			 */
			$where_conditions = apply_filters( 'bp_activity_search_where_conditions', $where_conditions, $search_term );

			// Join the where conditions together.
			$sql['where'] = 'WHERE ' . join( ' AND ', $where_conditions );

			$sql = "{$sql['select']} {$sql['from']} {$sql['where']}";

			$query_placeholder[] = '%' . $wpdb->esc_like( $search_term ) . '%';
			$sql                 = $wpdb->prepare( $sql, $query_placeholder );

			return apply_filters(
				'buddypress_search_activities_sql',
				$sql,
				array(
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Generate BuddyPress activity template HtML
		 *
		 * @param  mixed $template_type template Type.
		 * @return void
		 */
		protected function generate_html( $template_type = '' ) {
			$post_ids_arr = array();
			foreach ( $this->search_results['items'] as $item_id => $item_html ) {
				$post_ids_arr[] = $item_id;
			}

			$post_ids = implode( ',', $post_ids_arr );

			do_action( 'bp_before_search_activity_html' );

			if ( bp_has_activities(
				array(
					'include'  => $post_ids,
					'per_page' => count( $post_ids_arr ),
				)
			) ) {
				while ( bp_activities() ) {
					bp_the_activity();

					$result = array(
						'id'    => bp_get_activity_id(),
						'type'  => $this->type,
						'title' => $this->search_term,
						'html'  => buddypress_search_template_part( 'loop/activity', $template_type, false ),
					);
					$this->search_results['items'][ bp_get_activity_id() ] = $result;
				}
			}

			do_action( 'bp_after_search_activity_html' );
		}

	}

	// End class Bp_Search_Posts.

endif;

