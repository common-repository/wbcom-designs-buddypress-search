<?php
/**
 * BuddyPress search custom post type.
 *
 * @todo add description
 *
 * @package BuddyPress_Search
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyPress_Search_Custom_Post_Type' ) ) :

	/**
	 * BuddyPress Global Search  - search posts class
	 */
	class BuddyPress_Search_Custom_Post_Type extends BuddyPress_Search_Type {


		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @var $cpt_name Custom post typ name.
		 */
		private $cpt_name;

		/**
		 * Insures that only one instance of Class exists in memory at any
		 * one time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 *  @var $cpt_name Search type.
		 */
		private $search_type;

		/**
		 * A real constructor. Since we do want multiple copies of this class.
		 * The idea is to have one object for each searchable custom post type.
		 *
		 * @since 1.0.0
		 *
		 * @param string $cpt_name CPT Name.
		 * @param string $search_type Search type.
		 */
		public function __construct( $cpt_name, $search_type ) {
			$this->cpt_name    = $cpt_name;
			$this->search_type = $search_type;

			add_action( "bp_search_settings_item_{$this->cpt_name}", array( $this, 'print_search_options' ) );
		}

		/**
		 * MY SQL Query.
		 *
		 * @param  mixed  $search_term Search text.
		 * @param  boolen $only_totalrow_count Total Row Count.
		 */
		public function sql( $search_term, $only_totalrow_count = false ) {

			global $wpdb;
			$bpsearch_pages_posts_search = get_option( 'bpsearch_pages_posts_search' );

			$bp_prefix = bp_core_get_table_prefix();

			$query_placeholder = array();

			$sql = ' SELECT ';

			if ( $only_totalrow_count ) {
				$sql .= ' COUNT( DISTINCT id ) ';
			} else {
				$sql                .= " DISTINCT id , %s as type, post_title LIKE %s AS relevance, post_date as entry_date  ";
				$query_placeholder[] = $this->search_type;
				$query_placeholder[] = $search_term;
			}

			$sql .= " FROM {$wpdb->prefix}posts p";

			$tax        = array();
			$taxonomies = get_object_taxonomies( $this->cpt_name );
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $bpsearch_pages_posts_search[ $this->cpt_name ][ $taxonomy ] ) && $bpsearch_pages_posts_search[ $this->cpt_name ][ $taxonomy ] == 1 ) {
					$tax[] = $taxonomy;
				}
			}

			// Tax query left join.
			if ( ! empty( $tax ) ) {
				$sql .= " LEFT JOIN {$wpdb->term_relationships} r ON p.ID = r.object_id ";
			}

			$sql                .= " WHERE 1=1 AND ( p.post_title LIKE %s OR ExtractValue(p.post_content, '//text()') LIKE %s ";
			$query_placeholder[] = '%' . $search_term . '%';
			$query_placeholder[] = '%' . $search_term . '%';

			// Tax query.
			if ( ! empty( $tax ) ) {

				$tax_in_arr = array_map(
					function( $t_name ) {
							return "'" . $t_name . "'";
					},
					$tax
				);

				$tax_in = implode( ', ', $tax_in_arr );

				$sql                .= " OR  r.term_taxonomy_id IN (SELECT tt.term_taxonomy_id FROM {$wpdb->term_taxonomy} tt INNER JOIN {$wpdb->terms} t ON
					  t.term_id = tt.term_id WHERE ( t.slug LIKE %s OR t.name LIKE %s ) AND  tt.taxonomy IN ({$tax_in}) )";
				$query_placeholder[] = '%' . $search_term . '%';
				$query_placeholder[] = '%' . $search_term . '%';
			}

			// If post is not attachment Post should be publish &
			// else attachment should be inherit and that not include media and document as we have separate search for that.
			$sql .= ' ) AND p.post_type = %s';
			if ( 'attachment' === $this->cpt_name ) {
				$sql .= " AND p.post_status = 'inherit' AND p.ID NOT IN ( SELECT post_id FROM {$wpdb->postmeta} pm WHERE pm.`meta_key` IN ( 'bp_media_upload', 'bp_document_upload', 'bp_video_upload' ) )";
			} else {
				$sql .= " AND p.post_status = 'publish'";
			}
			$query_placeholder[] = $this->cpt_name;			
			
			$sql                 = $wpdb->prepare( $sql, $query_placeholder );

			return apply_filters(
				'buddypress_search_custom_post_type_sql',
				$sql,
				array(
					'post_type'           => $this->cpt_name,
					'search_term'         => $search_term,
					'only_totalrow_count' => $only_totalrow_count,
				)
			);
		}

		/**
		 * Generate custom post type template HtML
		 *
		 * @param  mixed $template_type template Type.
		 * @return void
		 */
		protected function generate_html( $template_type = '' ) {
			$post_ids = array();
			foreach ( $this->search_results['items'] as $item_id => $item_html ) {
				$post_ids[] = $item_id;
			}

			// now we have all the posts
			// lets do a wp_query and generate html for all posts.
			$qry      = new WP_Query(
				array(
					'post_type'      => $this->cpt_name,
					'post__in'       => $post_ids,
					'post_status'    => ( 'attachment' === $this->cpt_name ) ? 'inherit' : 'publish',
					'posts_per_page' => 20,
				)
			);
			$template = buddypress_search_locate_template( "loop/{$this->cpt_name}.php" ) ? "loop/{$this->cpt_name}" : 'loop/post';
			if ( $qry->have_posts() ) {
				while ( $qry->have_posts() ) {
					$qry->the_post();
					$result = array(
						'id'    => get_the_ID(),
						'type'  => $this->search_type,
						'title' => get_the_title(),
						'html'  => buddypress_search_template_part( $template, $template_type, false ),
					);

					$this->search_results['items'][ get_the_ID() ] = $result;
				}
			}
			wp_reset_postdata();
		}


		/**
		 * What taxonomy  should be searched on?
		 *
		 * Prints options to search through all registered taxonomies with give
		 * post type e.g $this->cpt_name
		 */
		public function print_search_options( $items_to_search ) {
			$register_taxonomy_custom = "<div class='wp-{$this->cpt_name}-fields' style='margin: 10px 0 10px 30px'>";
			echo wp_kses_post( $register_taxonomy_custom );
			$cpt_taxonomy = get_object_taxonomies( $this->cpt_name );

			foreach ( $cpt_taxonomy as $tax ) {

				$label   = ucwords( str_replace( '_', ' ', $tax ) );
				$value   = $this->search_type . '-tax-' . $tax;
				$checked = ! empty( $items_to_search ) && in_array( $value, $items_to_search ) ? ' checked' : '';

				$cpt_taxonomy_custom = "<label><input type='checkbox' value='{$value}' name='bp_search_plugin_options[items-to-search][]' {$checked}>{$label}</label><br>";
				echo wp_kses_post( $cpt_taxonomy_custom );
			}

			echo '</div><!-- .wp-user-fields -->';
		}

	}

	// End class BuddyPress_Search_Custom_Post_Type.

endif;

