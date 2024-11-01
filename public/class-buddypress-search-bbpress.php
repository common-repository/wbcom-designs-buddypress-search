<?php
/**
 * BuddyPress search bbPress.
 *
 * @todo add description
 *
 * @package buddypress_search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_bbPress' ) ) :

	/**
	 * BuddyPress Global Search  - search bbPress class
	 */
	abstract class BuddyPress_Search_bbPress extends BuddyPress_Search_Type {

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var $type BBPress forum reply.
		 */
		public $type;

		/**
		 * MY SQL Query.
		 *
		 * @param  mixed  $search_term Search text.
		 * @param  boolen $only_totalrow_count Total Row Count.
		 */
		public function sql( $search_term, $only_totalrow_count = false ) {
			global $wpdb;
			$query_placeholder = array();

			$sql = ' SELECT ';

			if ( $only_totalrow_count ) {
				$sql .= ' COUNT( DISTINCT id ) ';
			} else {
				$sql                .= " DISTINCT id , '{$this->type}' as type, post_title LIKE %s AS relevance, post_date as entry_date  ";
				$query_placeholder[] = '%' . $search_term . '%';
			}

			$sql                .= " FROM 
						{$wpdb->prefix}posts 
					WHERE 
						1=1 
						AND (
								(
										(post_title LIKE %s)
									OR 	(post_content LIKE %s)
								)
							) 
						AND post_type = '{$this->type}'
						AND post_status = 'publish' 
				";
			$query_placeholder[] = '%' . $search_term . '%';
			$query_placeholder[] = '%' . $search_term . '%';
			$sql                 = $wpdb->prepare( $sql, $query_placeholder );

			return apply_filters(
				'bp_search_forums_sql',
				$sql,
				array(
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Generate bbPress template HtML
		 *
		 * @param  mixed $template_type template Type.
		 * @return void
		 */
		protected function generate_html( $template_type = '' ) {
			$post_ids = array();
			foreach ( $this->search_results['items'] as $item_id => $item_html ) {
				$post_ids[] = $item_id;
			}

			remove_action( 'pre_get_posts', 'bbp_pre_get_posts_normalize_forum_visibility', 4 );

			// now we have all the posts
			// lets do a wp_query and generate html for all posts.
			$qry = new WP_Query(
				array(
					'post_type'     => array( 'forum', 'topic', 'reply' ),
					'post__in'      => $post_ids,
					'post_status'   => array( 'publish', 'private', 'hidden', 'closed' ),
					'no_found_rows' => true,
					'nopaging'      => true,
				)
			);

			add_action( 'pre_get_posts', 'bbp_pre_get_posts_normalize_forum_visibility', 4 );

			if ( $qry->have_posts() ) {
				while ( $qry->have_posts() ) {
					$qry->the_post();

					/**
					 * The following will try to load loop/forum.php, loop/topic.php loop/reply.php(if reply is included).
					 */
					$result_item = array(
						'id'    => get_the_ID(),
						'type'  => $this->type,
						'title' => get_the_title(),
						'html'  => buddypress_search_template_part( 'loop/' . $this->type, $template_type, false ),
					);

					$this->search_results['items'][ get_the_ID() ] = $result_item;
				}
			}
			wp_reset_postdata();
		}

	}

	// End class BuddyPress_Search_bbPress.

endif;

