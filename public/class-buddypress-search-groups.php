<?php
/**
 * BuddyPress search groups.
 *
 * @package BuddyPress_Search
 * @since   1.0.0
 * @todo    add description
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_Groups' ) ) :

	/**
	 * BuddyPress Global Search  - search groups class
	 */
	class BuddyPress_Search_Groups extends BuddyPress_Search_Type {

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var type Search Type.
		 */
		private $type = 'groups';

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return object BuddyPress_Search_Groups
		 */
		public static function instance() {
			// Store the instance locally to avoid private static replication.
			static $instance = null;

			// Only run these methods if they haven't been run previously.
			if ( null === $instance ) {
				$instance = new BuddyPress_Search_Groups();
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
			$bpsearch_bp_search = get_option( 'bpsearch_bp_search' );
			/*
			an example UNION query :-
			-----------------------------------------------------
			(
				SELECT
					DISTINCT g.id, 'groups' as type, g.name LIKE '%ho%' AS relevance, gm2.meta_value as entry_date
				FROM
					wp_bp_groups_groupmeta gm1, wp_bp_groups_groupmeta gm2, wp_bp_groups g
				WHERE
					1=1
					AND g.id = gm1.group_id
					AND g.id = gm2.group_id
					AND gm2.meta_key = 'last_activity'
					AND gm1.meta_key = 'total_member_count'
					AND ( g.name LIKE '%ho%' OR g.description LIKE '%ho%' )
			)
			----------------------------------------------------
			*/
			global $wpdb, $bp;
			$query_placeholder = array();

			$sql['select'] = 'SELECT';

			if ( $only_totalrow_count ) {
				$sql['select'] .= ' COUNT( DISTINCT g.id ) ';
			} else {
				$sql['select'] .= $wpdb->prepare( " DISTINCT g.id, 'groups' as type, g.name LIKE %s AS relevance, gm2.meta_value as entry_date  ", '%' . $wpdb->esc_like( $search_term ) . '%' );
			}

			$sql['from'] = "FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name} g";

			/**
			 * Filter the MySQL JOIN clause for the group Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param string $join_sql JOIN clause.
			 */
			$sql['from'] = apply_filters( 'bp_group_search_join_sql', $sql['from'] );

			$where_conditions                 = array( '1=1' );
			$where_conditions['search_query'] = "g.id = gm1.group_id 
						AND g.id = gm2.group_id 
						AND gm2.meta_key = 'last_activity' 
						AND gm1.meta_key = 'total_member_count' 
						AND ( g.name LIKE %s OR g.description LIKE %s )
				";

			$query_placeholder[] = '%' . $wpdb->esc_like( $search_term ) . '%';
			$query_placeholder[] = '%' . $wpdb->esc_like( $search_term ) . '%';

			/** LOCATION AUTOCOMPLETE SEARCH */

			if ( function_exists( 'bp_bpla' ) && 'yes' == bp_bpla()->option( 'enable-for-groups' ) ) {

				$split_search_term = explode( ' ', $search_term );

				$where_conditions['search_query'] .= "OR g.id IN ( SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'bbgs_group_search_string' ";

				foreach ( $split_search_term as $k => $sterm ) {

					if ( $k == 0 ) {
						$where_conditions['search_query'] .= 'AND meta_value LIKE %s';
						$query_placeholder[]               = '%' . $wpdb->esc_like( $sterm ) . '%';
					} else {
						$where_conditions['search_query'] .= 'AND meta_value LIKE %s';
						$query_placeholder[]               = '%' . $wpdb->esc_like( $sterm ) . '%';
					}
				}
				$where_conditions['search_query'] .= ' ) ';

			}

			/**
			 * Properly handle hidden groups.
			 * For guest users - exclude all hidden groups.
			 * For members - include only those hidden groups where current user is a member.
			 * For admins - include all hidden groups ( do nothing extra ).
			 *
			 * @since 1.1.0
			 */
			$group_status = array( 'public' );
			if ( isset( $bpsearch_bp_search['bp_search_groups_hidden'] ) ) {
				$group_status[] = 'hidden';
			}
			if ( isset( $bpsearch_bp_search['bp_search_groups_private'] ) ) {
				$group_status[] = 'private';
			}

			if ( is_user_logged_in() ) {
				if ( ! current_user_can( 'level_10' ) ) {
					// get all hidden groups where i am a member of.
					$grp_status        = implode( "','", $group_status );
					$hidden_groups_ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT gm.group_id FROM %s gm JOIN %s g ON gm.group_id = g.id WHERE gm.user_id = %d AND gm.is_confirmed = 1 AND gm.is_banned = 0 AND g.status in ('%s') ", $bp->groups->table_name_members, $bp->groups->table_name, bp_loggedin_user_id(), $grp_status ) );
					if ( empty( $hidden_groups_ids ) ) {
						$hidden_groups_ids = array( 99999999 );// arbitrarily large number.
					}

					$hidden_groups_ids_csv = implode( ',', $hidden_groups_ids );

					// either gruops which are not hidden,
					// or if hidden, only those where i am a member.
					$where_conditions['search_query'] .= " AND ( g.status in ('" . implode( "','", $group_status ) . "') OR g.id IN ( {$hidden_groups_ids_csv} ) ) ";
				}
			} else {
				$where_conditions['search_query'] .= "AND g.status in ('" . implode( "','", $group_status ) . "') ";
			}
			$where_conditions['search_query'] .= "AND g.status in ('" . implode( "','", $group_status ) . "') ";
			/**
			 * Filters the MySQL WHERE conditions for the group Search query.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $where_conditions Current conditions for MySQL WHERE statement.
			 * @param string $search_term      Search Term.
			 */
			$where_conditions = apply_filters( 'bp_group_search_where_conditions', $where_conditions, $search_term );

			// Join the where conditions together.
			$sql['where'] = 'WHERE ' . join( ' AND ', $where_conditions );

			$sql = "{$sql['select']} {$sql['from']} {$sql['where']}";

			$sql = $wpdb->prepare( $sql, $query_placeholder );

			return apply_filters(
				'buddypress_search_groups_sql',
				$sql,
				array(
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Generate BuddyPress Groups template HtML
		 *
		 * @param  mixed $template_type template Type.
		 * @return void
		 */
		protected function generate_html( $template_type = '' ) {
			$group_ids = array();
			foreach ( $this->search_results['items'] as $item_id => $item_html ) {
				$group_ids[] = $item_id;
			}

			// now we have all the posts.
			// lets do a groups loop.
			$args = array(
				'include'      => $group_ids,
				'per_page'     => count( $group_ids ),
				'search_terms' => false,
				'user_id'      => '',
			);
			if ( is_user_logged_in() ) {
				$args['show_hidden'] = true;
			}

			if ( function_exists( 'bp_bpla' ) ) {
				$args['search_terms'] = ' ';
			}

			do_action( 'bp_before_search_groups_html' );

			if ( bp_has_groups( $args ) ) {
				while ( bp_groups() ) {
					bp_the_group();

					$result = array(
						'id'    => bp_get_group_id(),
						'type'  => $this->type,
						'title' => bp_get_group_name(),
						'html'  => buddypress_search_template_part( 'loop/group', $template_type, false ),
					);

					$this->search_results['items'][ bp_get_group_id() ] = $result;
				}
			}

			do_action( 'bp_after_search_groups_html' );
		}
	}

	// End class BuddyPress_Search_Groups.

endif;

