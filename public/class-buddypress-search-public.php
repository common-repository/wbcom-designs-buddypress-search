<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Search_Public {

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
	 * The variable to hold the helper class objects for each type of searches.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $search_helpers = array();

	/**
	 * The variable to hold the helper class objects for each type of searches.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $searchable_items;

	/**
	 * The variable to hold arguments used for search.
	 * It will be used by other methods later on.
	 *
	 * @var array
	 */
	public $search_args = array();

	/**
	 * The variable to hold search results.
	 * The results will be grouped into different types(e.g: posts, members, etc..)
	 *
	 * @var array
	 */
	public $search_results = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_action( 'init', array( $this, 'buddypress_search_load_helpers' ), 100 );
		add_filter( 'the_content', array( $this, 'buddypress_search_search_page_content' ), 9 );
		add_shortcode( 'buddypress-search', array( $this, 'buddypress_search_shortcodes' ) );
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-search-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		/* To show number of listing per page. */
		$nonce = isset( $_GET['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['bp-search-nonce'] ) ) : '';
		if ( isset( $_GET['bp-search-nonce'] ) && ! wp_verify_nonce( $nonce, 'buddypress-seach-nonce' ) ) {
			die( 'Busted!' );
		}
		$bpsearch_live_search_settings = get_option( 'bpsearch_live_search_settings' );
		$per_page                      = '5';
		if ( isset( $bpsearch_live_search_settings['number_of_results'] ) && '' != $bpsearch_live_search_settings['number_of_results'] ) {

			$per_page = $bpsearch_live_search_settings['number_of_results'];
		}
		$per_page = apply_filters( 'buddypress_search_numbero_of_results', $per_page );

		/* To checlk enable live search. */
		$enable_ajax_search = false;
		if ( isset( $bpsearch_live_search_settings['enable_live_search'] ) && $bpsearch_live_search_settings['enable_live_search'] == 1 ) {
			$enable_ajax_search = $bpsearch_live_search_settings['enable_live_search'];
		}
		$enable_ajax_search = apply_filters( 'buddypress_search_is_enable_live_search', $enable_ajax_search );

		$data = array(
			'nonce'                 => wp_create_nonce( 'buddypress_search_ajax' ),
			'action'                => 'buddypress_search_ajax',
			'debug'                 => true, // set it to false on production.
			'ajaxurl'               => admin_url( 'admin-ajax.php', is_ssl() ? 'admin' : 'http' ),
			// 'search_url'    => home_url( '/' ), Now we are using form[role='search'] selector
			'loading_msg'           => __( 'Loading Suggestions', 'buddypress-search' ),
			'enable_ajax_search'    => $enable_ajax_search,
			'per_page'              => $per_page,
			'autocomplete_selector' => "form[role='search'], form.search-form, form.searchform, form#adminbarsearch, .bp-search-form>#search-form, form#searchform,form.buddypress-dir-search-form",
			'form_selector'         => '',
		);

		if ( isset( $_GET['s'] ) ) {
			$data['search_term'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
		}

		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-search-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'budypress_search', apply_filters( 'bbuddypress_search_js_settings', $data ) );

	}

	/**
	 * Buddypress search load healpers.
	 *
	 * @return void
	 */
	public function buddypress_search_load_helpers() {
		// load the helper type parent class.
		require_once BP_SEARCH_DIR . '/public/class-buddypress-search-types.php';

		$bpsearch_bp_search            = get_option( 'bpsearch_bp_search' );
		$bpsearch_pages_posts_search   = get_option( 'bpsearch_pages_posts_search' );
		$bpsearch_live_search_settings = get_option( 'bpsearch_live_search_settings' );

		/* Load Activity search class */
		if ( isset( $bpsearch_bp_search['bp_search_activity'] ) && $bpsearch_bp_search['bp_search_activity'] == 1 && bp_is_active( 'activity' ) ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-activities.php';
			$this->search_helpers['activity'] = BuddyPress_Search_Activities::instance();
			$this->searchable_items[]         = 'activity';
		}

		/* Load Activity search class */
		if ( isset( $bpsearch_bp_search['bp_search_groups'] ) && 1 == $bpsearch_bp_search['bp_search_groups'] && bp_is_active( 'groups' ) ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-groups.php';
			$this->search_helpers['groups'] = BuddyPress_Search_Groups::instance();
			$this->searchable_items[]       = 'groups';
		}

		// Check BuddyPress is active.
		if ( isset( $bpsearch_bp_search['bp_search_members'] ) && 1 == $bpsearch_bp_search['bp_search_members'] && bp_is_active( 'members' ) ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-members.php';
			$this->search_helpers['members'] = BuddyPress_Search_Members::instance();
			$this->searchable_items[]        = 'members';
		}

		// load and associate helpers one by one.
		if ( isset( $bpsearch_pages_posts_search['bp_post'] ) && 1 == $bpsearch_pages_posts_search['bp_post'] ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-posts.php';
			$this->search_helpers['post'] = new BuddyPress_Search_Posts( 'post', 'post' );
			$this->searchable_items[]      = 'post';
		}

		if ( isset( $bpsearch_pages_posts_search['bp_page'] ) && 1 == $bpsearch_pages_posts_search['bp_page'] ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-posts.php';
			$this->search_helpers['page'] = new BuddyPress_Search_Posts( 'page', 'page' );
			$this->searchable_items[]      = 'page';
		}

		require_once BP_SEARCH_DIR . '/public/class-buddypress-search-bbpress.php';
		if ( isset( $bpsearch_pages_posts_search['bp_forum'] ) && 1 == $bpsearch_pages_posts_search['bp_forum'] ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-bbpress-forums.php';
			$this->search_helpers['forum'] = BuddyPress_Search_bbPress_Forums::instance();
			$this->searchable_items[]      = 'forum';
		}

		if ( isset( $bpsearch_pages_posts_search['bp_topic'] ) && 1 == $bpsearch_pages_posts_search['bp_topic'] ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-bbpress-forums-topics.php';
			$this->search_helpers['topic'] = BuddyPress_Search_bbPress_Topics::instance();
			$this->searchable_items[]      = 'topic';
		}

		if ( isset( $bpsearch_pages_posts_search['bp_reply'] ) && 1 == $bpsearch_pages_posts_search['bp_reply'] ) {
			require_once BP_SEARCH_DIR . '/public/class-buddypress-search-bbpress-forums-replies.php';
			$this->search_helpers['reply'] = BuddyPress_Search_bbPress_Replies::instance();
			$this->searchable_items[]      = 'reply';
		}

		$post_types          = get_post_types( array( 'public' => true ) );
		$custom_handler_cpts = array( 'post', 'forum', 'topic', 'reply', 'page' );

		foreach ( $post_types as $post_type ) {
			// if name starts with cpt.
			if ( ! in_array( $post_type, $custom_handler_cpts ) && isset( $bpsearch_pages_posts_search[ 'bp_' . $post_type ] ) && $bpsearch_pages_posts_search[ 'bp_' . $post_type ] == 1 ) {
				$cpt_obj = get_post_type_object( $post_type );
				// $searchable_type = 'cpt-' . $post_type;
				$searchable_type = $cpt_obj->label;

				if ( $cpt_obj && ! is_wp_error( $cpt_obj ) ) {
					require_once BP_SEARCH_DIR . '/public/class-buddypress-search-custom-post-type.php';				
					$this->search_helpers[ $searchable_type ] = new BuddyPress_Search_Custom_Post_Type( $post_type, $searchable_type );
					$this->searchable_items[]                 = $searchable_type;
				}
			}
		}
	}

	/**
	 * Ajax callback function for search.
	 */
	public function buddypress_search_ajax() {
		check_ajax_referer( 'buddypress_search_ajax', 'nonce' );
		/* To show number of listing per page. */
		$bpsearch_live_search_settings = get_option( 'bpsearch_live_search_settings' );
		$per_page                      = '5';
		if ( isset( $bpsearch_live_search_settings['number_of_results'] ) && $bpsearch_live_search_settings['number_of_results'] != '' ) {

			$per_page = $bpsearch_live_search_settings['number_of_results'];
		}
		$per_page = apply_filters( 'buddypress_search_numbero_of_results', $per_page );

		if ( isset( $_POST['view'] ) && $_POST['view'] == 'content' ) {

			$_GET['s'] = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
			if ( ! empty( $_POST['subset'] ) ) {
				$_GET['subset'] = sanitize_text_field( wp_unslash( $_POST['subset'] ) );
			}

			if ( ! empty( $_POST['list'] ) ) {
				$_GET['list'] = sanitize_text_field( wp_unslash( $_POST['list'] ) );
			}

			$content = '';

			$this->prepare_buddypress_search_page();
			$content = buddypress_search_template_part( 'results-page-content', '', false );

			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			die();
		}

		$args = array(
			'search_term'   => isset( $_REQUEST['search_term'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_term'] ) ) : '',
			// How many results should be displyed in autosuggest?
			// @todo: give a settings field for this value.
			'ajax_per_page' => isset( $_REQUEST['per_page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['per_page'] ) ) : '',
			'count_total'   => true,
			'template_type' => 'ajax',
		);

		if ( isset( $_REQUEST['forum_search_term'] ) ) {
			$args['forum_search'] = true;
		}

		$this->do_buddypress_search( $args );

		$search_results = array();

		if ( isset( $this->search_results['all']['items'] ) && ! empty( $this->search_results['all']['items'] ) ) {
			/*
			 * group items of same type together
			 */
			$types = array();
			foreach ( $this->search_results['all']['items'] as $item_id => $item ) {
				$type = $item['type'];
				if ( empty( $types ) || ! in_array( $type, $types ) ) {
					$types[] = $type;
				}
			}

			$new_items = array();
			foreach ( $types as $type ) {
				$first_html_changed = false;
				foreach ( $this->search_results['all']['items'] as $item_id => $item ) {
					if ( $item['type'] != $type ) {
						continue;
					}

					// Filter by default will be false.
					$bp_search_group_or_type_title = apply_filters( 'bbuddypress_search_group_or_type_title', false );
					if ( true === $bp_search_group_or_type_title ) {
						// add group/type title in first one.
						if ( ! $first_html_changed ) {
							// this filter can be used to change display of 'posts' to 'Blog Posts' etc..
							$label              = apply_filters( 'bbuddypress_search_label_search_type', $type );
							$item['html']       = "<div class='results-group results-group-{$type}'><span class='results-group-title'>{$label}</span></div>" . $item['html'];
							$first_html_changed = true;
						}
					}

					$new_items[ $item_id ] = $item;
				}
			}

			$this->search_results['all']['items'] = $new_items;

			$url = $this->search_page_search_url( false );

			if ( true === $this->search_args['forum_search'] ) {
				$url = $url;
			} else {
				$url = esc_url(
					add_query_arg(
						array(
							'view'              => 'content',
							'no_frame'          => '1',
							'buddypress_search' => 1,
						),
						$url
					)
				);
			}

			$type_mem = '';
			foreach ( $this->search_results['all']['items'] as $item_id => $item ) {
				$new_row               = array( 'value' => $item['html'] );
				$type_label            = apply_filters( 'bbuddypress_search_label_search_type', $item['type'] );
				$new_row['type']       = $item['type'];
				$new_row['type_label'] = '';
				$new_row['value']      = $item['html'];
				if ( isset( $item['title'] ) ) {
					$new_row['label'] = $item['title'];
				}

				// Filter by default will be false.
				$bp_search_raw_type = apply_filters( 'bbuddypress_search_raw_type', true );

				if ( true === $bp_search_raw_type ) {
					if ( $type_mem != $new_row['type'] ) {
						$type_mem              = $new_row['type'];
						$cat_row               = $new_row;
						$cat_row['type']       = $item['type'];
						$cat_row['type_label'] = $type_label;
						$category_search_url   = esc_url( add_query_arg( array( 'subset' => $item['type'] ), $url ) );
						$html                  = "<span><a href='" . esc_url( $category_search_url ) . "'>" . $type_label . '</a></span>';
						$cat_row['value']      = apply_filters( 'buddypress_gs_autocomplete_category', $html, $item['type'], $url, $type_label );
						$search_results[]      = $cat_row;
					}
				}

				$search_results[] = $new_row;
			}

			// Show "View All" link.
			if ( absint( $this->search_results['all']['total_match_count'] ) > absint( $per_page ) ) {
				$all_results_row  = array(
					'value'      => "<div class='bp-search-ajax-item allresults'><a href='" . esc_url( $url ) . "'>" . __( 'View all', 'buddypress-search' ) . '</a></div>',
					'type'       => 'view_all_type',
					'type_label' => '',
				);
				$search_results[] = $all_results_row;
			}
		} else {
			// @todo give a settings screen for this field
			$search_results[] = array(
				'value' => '<div class="bp-search-ajax-item ui-state-disabled noresult">' .
					sprintf(
						/* translators: %s: search term */
						__( "Nothing found for '%s'", 'buddypress-search' ),
						stripslashes( $this->search_args['search_term'] )
					) .
				'</div>',
				'label' => $this->search_args['search_term'],
			);
		}

		die( json_encode( $search_results ) );
	}

	/**
	 * BuddyPress Search results.
	 *
	 * @param array $args Arguments.
	 */
	public function do_buddypress_search( $args = '' ) {
		global $wpdb;
		$nonce = isset( $_REQUEST['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['bp-search-nonce'] ) ) : '';
		if ( isset( $_REQUEST['bp-search-nonce'] ) && ! wp_verify_nonce( $nonce, 'buddypress-seach-nonce' ) ) {
			die( 'Busted!' );
		}
		$args = $this->sanitize_args( $args );

		$defaults = array(
			'search_term'   => '', // the search term.
			'search_subset' => 'all',
			'per_page'      => 20,
			'current_page'  => 1,
			'count_total'   => true,
			'template_type' => '',
			'forum_search'  => false,
			'number'        => 3,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( true === $args['forum_search'] ) {
			$this->searchable_items = array( 'forum', 'topic', 'reply' );
		}

		$this->search_args = $args;// save it for using in other methods.

		// bail out if nothing to search for.
		if ( ! $args['search_term'] ) {
			return;
		}

		$total = array();

		if ( 'all' == $args['search_subset'] ) {

			$sql_queries = array();

			if ( empty( $this->searchable_items ) && ! is_array( $this->searchable_items ) ) {
				return;
			}

			foreach ( $this->searchable_items as $search_type ) {
				if ( ! isset( $this->search_helpers[ $search_type ] ) ) {
					continue;
				}

				/**
				 * the following variable will be an object of current search type helper class
				 * e.g: an object of Bp_Search_Groups or Bp_Search_Posts etc.
				 * so we can safely call the public methods defined in those classes.
				 * This also means that all such classes must have a common set of methods.
				 */
				$obj                   = $this->search_helpers[ $search_type ];
				$limit                 = isset( $_REQUEST['view'] ) ? ' LIMIT ' . ( $args['number'] ) : " LIMIT {$args['ajax_per_page']} ";
				$sql_queries[]         = '( ' . $obj->union_sql( $args['search_term'] ) . " ORDER BY relevance DESC, entry_date DESC $limit ) ";
				$total[ $search_type ] = $obj->get_total_match_count( $args['search_term'] );
			}

			if ( empty( $sql_queries ) ) {
				// thigs will get messy if program reaches here!!
				return;
			}

			$pre_search_query = implode( ' UNION ', $sql_queries );

			if ( isset( $args['ajax_per_page'] ) && $args['ajax_per_page'] > 0 ) {
				// $pre_search_query .= " LIMIT {$args['ajax_per_page']} ";
			}
			$results = $wpdb->get_results( $pre_search_query );

			// $results will have a structure like below .

			$results = apply_filters( 'bbuddypress_search_query_results', $results, $this );

			if ( ! empty( $results ) ) {
				$this->search_results['all'] = array(
					'total_match_count' => 0,
					'items'             => array(),
					'items_title'       => array(),
				);
				// segregate items of a type together and pass it to corresponsing search handler, so that an aggregate query can be done
				// e.g one single WordPress loop can be done for all posts.

				foreach ( $results as $item ) {
					$obj = $this->search_helpers[ $item->type ];
					$obj->add_search_item( $item->id );
				}

				// now get html for each item.
				foreach ( $results as $item ) {

					$obj    = $this->search_helpers[ $item->type ];
					$result = array(
						'id'    => $item->id,
						'type'  => $item->type,
						'html'  => $obj->get_html( $item->id, $args['template_type'] ),
						'title' => $obj->get_title( $item->id ),
					);

					$this->search_results['all']['items'][ $item->type . '_' . $item->id ] = $result;
				}
				// now we've html saved for search results.

				if ( ! empty( $this->search_results['all']['items'] ) && $args['template_type'] != 'ajax' ) {
					// group items of same type together.
					// create another copy, of items, this time, items of same type grouped together.
					$ordered_items_group = array();
					foreach ( $this->search_results['all']['items'] as $item_id => $item ) {
						$type = $item['type'];
						if ( ! isset( $ordered_items_group[ $type ] ) ) {
							$ordered_items_group[ $type ] = array();
						}
						$item_id                                  = absint( str_replace( $type . '_', '', $item_id ) );
						$ordered_items_group[ $type ][ $item_id ] = $item;
					}

					$search_items = buddypress_search_items();
					$search_url   = $this->search_page_search_url();

					foreach ( $ordered_items_group as $type => &$items ) {
						// now prepend html (opening tags) to first item of each.
						$category_search_url = esc_url(
							add_query_arg(
								array(
									'subset'            => $type,
									'buddypress_search' => 1,
								),
								$search_url
							)
						);
						$label               = isset( $search_items[ $type ] ) ? trim( $search_items[ $type ] ) : trim( $type );
						$first_item          = reset( $items );
						$total_results       = $total[ $type ];
						$start_html          = "<div class='results-group results-group-{$type} " . apply_filters( 'bbuddypress_search_class_search_wrap', 'bp-search-results-wrap', $label ) . "'>"
									. "<header class='results-group-header clearfix'>"
									. "<h3 class='results-group-title'><span>" . apply_filters( 'bbuddypress_search_label_search_type', $label ) . '</span></h3>'
									/* translators: %d: Total Result */
									. "<span class='total-results'>" . sprintf( _n( '%d result', '%d results', $total_results, 'buddypress-search' ), $total_results ) . '</a>'
									. '</header>'
									. "<ul id='{$type}-stream' class='item-list {$type}-list bp-list " . apply_filters( 'bbuddypress_search_class_search_list', 'bp-search-results-list', $label ) . "'>";

						$group_start_html = apply_filters( 'bbuddypress_search_results_group_start_html', $start_html, $type );

						$first_item['html']         = $group_start_html . $first_item['html'];
						$items[ $first_item['id'] ] = $first_item;

						// and append html (closing tags) to last item of each type.
						$last_item = end( $items );
						$end_html  = '</ul>';

						if ( $total_results > 3 ) {
							$end_html .= "<footer class='results-group-footer'>";
							$end_html .= "<a href='" . $category_search_url . "' class='view-all-link'>" .
							/* translators: %1$s: Total Results - Number*/
										sprintf( esc_html__( 'View (%d) more', 'buddypress-search' ), $total_results - $args['number'] ) .
										'</a>';
							$end_html .= '</footer>';
						}

						$end_html .= '</div>';

						$group_end_html = apply_filters( 'bbuddypress_search_results_group_end_html', $end_html, $type );

						$last_item['html']         = $last_item['html'] . $group_end_html;
						$items[ $last_item['id'] ] = $last_item;
					}

					// replace orginal items with this new, grouped set of items.
					$this->search_results['all']['items'] = array();
					foreach ( $ordered_items_group as $type => $grouped_items ) {

						// Remove last item from list.
						if ( count( $grouped_items ) > 3 ) {
							array_pop( $grouped_items );
						}

						foreach ( $grouped_items as $item_id => $item ) {
							$this->search_results['all']['items'][ $type . '_' . $item_id ] = $item;
						}
					}
				}
			}
		} else {
			// if subset not in searchable items, bail out.
			if ( ! in_array( $args['search_subset'], $this->searchable_items ) ) {
				return;
			}

			if ( ! isset( $this->search_helpers[ $args['search_subset'] ] ) ) {
				return;
			}

			/**
			 * 1. Search top top 20( $args['per_page'] ) item( posts|members|..)
			 * 2. Generate html for each of them
			 */
			// $args['per_page'] = get_option( 'posts_per_page' );
			$obj              = $this->search_helpers[ $args['search_subset'] ];
			$pre_search_query = $obj->union_sql( $args['search_term'] ) . ' ORDER BY relevance DESC, entry_date DESC ';

			if ( $args['per_page'] > 0 ) {
				$offset            = ( $args['current_page'] * $args['per_page'] ) - $args['per_page'];
				$pre_search_query .= " LIMIT {$offset}, {$args['per_page']} ";
			}

			$results = $wpdb->get_results( $pre_search_query );

			// $results will have a structure like below.
			$results = apply_filters( 'bbuddypress_search_query_results', $results, $this );

			if ( ! empty( $results ) ) {
				$obj = $this->search_helpers[ $args['search_subset'] ];
				$this->search_results[ $args['search_subset'] ] = array(
					'total_match_count' => 0,
					'items'             => array(),
				);
				// segregate items of a type together and pass it to corresponsing search handler, so that an aggregate query can be done
				// e.g one single WordPress loop can be done for all posts.
				foreach ( $results as $item ) {
					$obj->add_search_item( $item->id );
				}

				// now get html for each item.
				foreach ( $results as $item ) {
					$html = $obj->get_html( $item->id, $args['template_type'] );

					$result = array(
						'id'    => $item->id,
						'type'  => $args['search_subset'],
						'html'  => $obj->get_html( $item->id, $args['template_type'] ),
						'title' => $obj->get_title( $item->id ),
					);

					$this->search_results[ $args['search_subset'] ]['items'][ $item->id ] = $result;
				}

				// now prepend html (opening tags) to first item of each type.
				$first_item = reset( $this->search_results[ $args['search_subset'] ]['items'] );
				$start_html = "<div class='results-group results-group-{$args['search_subset']} " . apply_filters( 'bbuddypress_search_class_search_wrap', 'bp-search-results-wrap', $args['search_subset'] ) . "'>"
							. "<ul id='{$args['search_subset']}-stream' class='item-list {$args['search_subset']}-list bp-list " . apply_filters( 'bbuddypress_search_class_search_list', 'bp-search-results-list', $args['search_subset'] ) . "'>";

				$group_start_html = apply_filters( 'bbuddypress_search_results_group_start_html', $start_html, $args['search_subset'] );

				$first_item['html'] = $group_start_html . $first_item['html'];
				$this->search_results[ $args['search_subset'] ]['items'][ $first_item['id'] ] = $first_item;

				// and append html (closing tags) to last item of each type.
				$last_item = end( $this->search_results[ $args['search_subset'] ]['items'] );
				$end_html  = '</ul></div>';

				$group_end_html = apply_filters( 'bbuddypress_search_results_group_end_html', $end_html, $args['search_subset'] );

				$last_item['html'] = $last_item['html'] . $group_end_html;
				$this->search_results[ $args['search_subset'] ]['items'][ $last_item['id'] ] = $last_item;
			}
		}

		// html for search results is generated.
		// now, lets calculate the total number of search results, for all different types.
		if ( $args['count_total'] ) {
			$all_items_count = 0;
			foreach ( $this->searchable_items as $search_type ) {
				if ( ! isset( $this->search_helpers[ $search_type ] ) ) {
					continue;
				}

				if ( ! isset( $total[ $search_type ] ) ) {
					$obj               = $this->search_helpers[ $search_type ];
					$total_match_count = $obj->get_total_match_count( $this->search_args['search_term'] );
					$this->search_results[ $search_type ]['total_match_count'] = (int) $total_match_count;
				} else {
					$this->search_results[ $search_type ]['total_match_count'] = (int) $total[ $search_type ];
				}

				$all_items_count += $this->search_results[ $search_type ]['total_match_count'];
			}

			$this->search_results['all']['total_match_count'] = $all_items_count;
		}
	}

	/**
	 * Sanitize user inputs before performing search.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function sanitize_args( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		if ( isset( $args['search_term'] ) ) {
			$args['search_term'] = sanitize_text_field( $args['search_term'] );
		}

		if ( isset( $args['search_subset'] ) ) {
			$args['search_subset'] = sanitize_text_field( $args['search_subset'] );
		}

		if ( isset( $args['per_page'] ) ) {
			$args['per_page'] = absint( $args['per_page'] );
		}

		if ( isset( $args['current_page'] ) ) {
			$args['current_page'] = absint( $args['current_page'] );
		}

		return $args;
	}

	/**
	 * Returns the url of the page which is selected to display search results.
	 *
	 * @since 1.0.0
	 * @param  string $value of the serach results page.
	 * @return string url of the serach results page
	 */
	public function search_page_url( $value = '' ) {
		$bpsearch_search_results = get_option( 'bpsearch_search_results_settings' );

		if ( isset( $bpsearch_search_results['overide_default_search'] ) && $bpsearch_search_results['overide_default_search'] == 1 && isset( $bpsearch_search_results['search_results_page'] ) && $bpsearch_search_results['search_results_page'] != '' ) {
			$url = get_permalink( $bpsearch_search_results['search_results_page'] );
		} else {
			$url = home_url( '/' );
		}

		if ( ! empty( $value ) ) {
			$url = esc_url( add_query_arg( 's', urlencode( $value ), $url ) );
		}

		return $url;
	}

	/**
	 * Search page url.
	 */
	public function search_page_search_url() {
		if ( true == $this->search_args['forum_search'] ) {
			// Full search url for bbpress forum search.
			if ( true === $default ) {
				$base_url = buddypress_search_get_url( false );
			} else {
				$base_url = buddypress_search_get_url( false ) . $this->search_args['search_term'];
			}

			if ( true === $this->search_args['forum_search'] ) {
				$full_url = esc_url( $base_url );
			} else {
				$full_url = esc_url( add_query_arg( 'buddypress_search', urlencode( $this->search_args['search_term'] ), $base_url ) );
			}
		} else {
			$base_url = $this->search_page_url();
			$full_url = esc_url( add_query_arg( 's', urlencode( stripslashes( $this->search_args['search_term'] ) ), $base_url ) );
			// for now we only have one filter in url.
		}

		return $full_url;
	}

	/**
	 * Display all tabs.
	 */
	public function print_tabs() {

		$search_url = $this->search_page_search_url();

		// first print the 'all results' tab.
		$class = 'all' == $this->search_args['search_subset'] ? 'active current selected' : '';
		// this filter can be used to change display of 'all' to 'Everything' etc..
		$all_label = __( 'All Results', 'buddypress-search' );
		$label     = apply_filters( 'bp_search_label_search_type', $all_label );

		if ( $this->search_args['count_total'] && isset( $this->search_results['all'] ) ) {
			$label .= "<span class='count'>" . $this->search_results['all']['total_match_count'] . '</span>';
		}

		$tab_url     = $search_url;
		$tabs_custom = "<li class='{$class}'><a href='" . esc_url( $tab_url ) . "'>{$label}</a></li>";
		echo wp_kses_post( $tabs_custom );
		// then other tabs.
		$search_items = buddypress_search_items();

		if ( empty( $this->searchable_items ) && ! is_array( $this->searchable_items ) ) {
			return;
		}

		foreach ( $this->searchable_items as $item ) {
			$class = $item == $this->search_args['search_subset'] ? 'active current' : '';
			// this filter can be used to change display of 'posts' to 'Blog Posts' etc..

			$label = isset( $search_items[ $item ] ) ? $search_items[ $item ] : $item;

			$label = apply_filters( 'bp_search_label_search_type', $label );

			if ( empty( $this->search_results[ $item ]['total_match_count'] ) ) {
				continue; // skip tab.
			}

			if ( $this->search_args['count_total'] ) {
				$label .= "<span class='count'>" . (int) $this->search_results[ $item ]['total_match_count'] . '</span>';
			}

			$tab_url        = esc_url( add_query_arg( 'subset', $item, $search_url ) );
			$tab_url_custom = "<li class='{$class} {$item}' data-item='{$item}'><a href='" . esc_url( $tab_url ) . "'>{$label}</a></li>";
			echo wp_kses_post( $tab_url_custom );
		}
	}

	/**
	 * Display tab results.
	 */
	public function print_results() {

		if ( $this->has_buddypress_search_results() ) {
			$current_tab = $this->search_args['search_subset'];

			foreach ( $this->search_results[ $current_tab ]['items'] as $item_id => $item ) {
				echo wp_kses_post( $item['html'] );
			}

			if ( $current_tab != 'all' ) {
				$page_slug = untrailingslashit( str_replace( home_url(), '', $this->search_page_url() ) );

				buddypress_search_pagination_page_counts(
					$this->search_results[ $current_tab ]['total_match_count'],
					$this->search_args['per_page'],
					$this->search_args['current_page']
				);

				buddypress_search_pagination(
					$this->search_results[ $current_tab ]['total_match_count'],
					$this->search_args['per_page'],
					$this->search_args['current_page'],
					$page_slug
				);
			}
		} else {
			buddypress_search_template_part( 'no-results' );
		}
	}

	/**
	 * Get the search term.
	 */
	public function get_search_term() {
		return isset( $this->search_args['search_term'] ) ? $this->search_args['search_term'] : '';
	}

	/**
	 * Search results.
	 */
	public function has_buddypress_search_results() {
		$current_tab = isset( $this->search_args['search_subset'] ) ? $this->search_args['search_subset'] : '';
		return isset( $this->search_results[ $current_tab ]['items'] ) && ! empty( $this->search_results[ $current_tab ]['items'] );
	}

	/**
	 * Setup everything before starting to display content for search page.
	 */
	public function prepare_buddypress_search_page() {
		$args  = array();
		$nonce = isset( $_GET['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['bp-search-nonce'] ) ) : '';
		if ( isset( $_GET['bp-search-nonce'] ) && ! wp_verify_nonce( $nonce, 'buddypress-seach-nonce' ) ) {
			die( 'Busted!' );
		}

		if ( isset( $_GET['subset'] ) && ! empty( $_GET['subset'] ) ) {
			$args['search_subset'] = sanitize_text_field( wp_unslash( $_GET['subset'] ) );
		}

		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$args['search_term'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
		}

		if ( isset( $_GET['list'] ) && ! empty( $_GET['list'] ) ) {
			$current_page = (int) $_GET['list'];
			if ( $current_page > 0 ) {
				$args['current_page'] = $current_page;
			}
		}

		$args = apply_filters( 'buddypress_search_search_page_args', $args );
		$this->do_buddypress_search( $args );
	}


	/**
	 * BuddyPress Search page content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Search page content .
	 */
	public function buddypress_search_search_page_content( $content ) {
		/**
		 * Reportedly, on some installations, the remove_filter call below, doesn't work and this filter is called over and over again.
		 * Possibly due to some other plugin/theme.
		 *
		 * Lets add another precautionary measure, a global flag.
		 *
		 * @since 1.0.0
		 */
		$bpsearch_search_results = get_option( 'bpsearch_search_results_settings' );
			$nonce               = isset( $_REQUEST['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['bp-search-nonce'] ) ) : '';
		if ( isset( $_REQUEST['bp-search-nonce'] ) && ! wp_verify_nonce( $nonce, 'buddypress-seach-nonce' ) ) {
			die( 'Busted!' );
		}
		if ( isset( $bpsearch_search_results['overide_default_search'] ) && $bpsearch_search_results['overide_default_search'] == 1 && isset( $bpsearch_search_results['search_results_page'] ) && $bpsearch_search_results['search_results_page'] != '' ) {
			$search_results_page = $bpsearch_search_results['search_results_page'];
		}

		if ( ! is_search() && get_the_ID() == 0 && isset( $_REQUEST['s'] ) && isset( $_REQUEST['buddypress_search'] ) ) {
			remove_filter( 'the_content', array( $this, 'buddypress_search_search_page_content' ), 9 );
			remove_filter( 'the_content', 'wpautop' );
			// setup search resutls and all..
			$this->prepare_buddypress_search_page();
			ob_start();
			buddypress_search_template_part( 'results-page' );
			$content .= ob_get_clean();
		}

		if ( ! is_admin() && is_search() && isset( $_REQUEST['buddypress_search'] ) ) {
			remove_filter( 'the_content', array( $this, 'buddypress_search_search_page_content' ), 9 );
			remove_filter( 'the_content', 'wpautop' );
			// setup search resutls and all..
			$this->prepare_buddypress_search_page();
			ob_start();
			buddypress_search_template_part( 'results-page' );
			$content .= ob_get_clean();
		}

		return $content;
	}

	/**
	 * Adds a Buddypress search shortcode.
	 *
	 * @param array|string $atts User defined attributes for this shortcode instance.
	 * @param string       $content Shorcode output content.
	 */
	public function buddypress_search_shortcodes( $atts, $content = null ) {
		$default_atts = array(
			'title' => '', // title of the section.
		);
		$atts         = shortcode_atts( $default_atts, $atts );
		extract( $atts );
		ob_start();
		if ( $title != '' ) { ?>
			<h3 class="section-title"><span><?php echo esc_html( $title ); ?></span></h3>
			<?php
		}
		buddypress_search_template_part( 'search-form' );

		return ob_get_clean();
	}

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @since 2.7.0
	 * @since 5.5.0 The `$args` parameter was added.
	 *
	 * @param string $form The search form HTML output.
	 * @param array  $args The array of arguments for building the search form.
	 *                     See get_search_form() for information on accepted arguments.
	 */
	public function bp_search_get_search_form( $form, $args ) {
		$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url( home_url( '/' ) ) . '">
				<div>
					<label class="screen-reader-text" for="s">' . _x( 'Search for:', 'label', 'buddypress-search' ) . '</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />'
					. wp_nonce_field( 'buddypress-seach-nonce', 'bp-search-nonce' ) .
				'<input type="submit" id="searchsubmit" value="' . esc_attr_x( 'Search', 'submit button', 'buddypress-search' ) . '" />
				</div>
			</form>';
		return $form;
	}

}
