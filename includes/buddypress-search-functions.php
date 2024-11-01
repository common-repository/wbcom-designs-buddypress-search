<?php
/**
 * This file is used for general functions of this plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/admin/includes
 */
function buddypress_search_template_stack_location( $retval, $stacks ) {
	$retval[] = BP_SEARCH_DIR . '/templates/';
	return $retval;
}
add_filter( 'bp_add_template_stack_locations', 'buddypress_search_template_stack_location', 10, 2 );

/**
 * Returns array of search field labels.
 *
 * @since Buddypress search 1.0.0
 */
function buddypress_search_get_user_fields() {
	return apply_filters(
		'buddypress_search_get_user_fields',
		array(
			'user_meta'    => __( 'User Meta', 'buddypress-search' ),
			'display_name' => __( 'Display Name', 'buddypress-search' ),
			'user_email'   => __( 'User Email', 'buddypress-search' ),
			'user_login'   => __( 'Username', 'buddypress-search' ),
		)
	);
}

/**
 * Returns array of exclude post type.
 *
 * @since Buddypress search 1.0.0
 */
function buddypress_search_exclude_post_type() {
	return apply_filters( 'buddypress_search_exclude_post_type', array( 'attachment', 'bmpro_spam', 'bmpro_avatar_spam' ) );
}


/**
 * Return settings API option
 *
 * @since 1.0.0
 *
 * @param string $option Option.
 * @param string $default Default.
 * @param bool   $slug Slug.
 *
 * @return mixed
 */
function buddypress_search_get_form_option( $option, $default = '', $slug = false ) {

	// Get the option and sanitize it.
	$value = get_option( $option, $default );

	// Slug?
	if ( true === $slug ) {
		$value = esc_attr( apply_filters( 'editable_slug', $value ) );

		// Not a slug.
	} else {
		$value = esc_attr( $value );
	}

	// Fallback to default.
	if ( empty( $value ) ) {
		$value = $default;
	}

	// Allow plugins to further filter the output.
	return apply_filters( 'bp_search_get_form_option', $value, $option );
}

/**
 * Locate template.
 *
 * @param  string $template Template File.
 */
function buddypress_search_locate_template( $template ) {
	$file = $template;

	if ( file_exists( BP_SEARCH_DIR . '/templates/buddypress-search/' . $file ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Loads BuddyPress Search template.
 *
 * @since 1.0.0
 *
 *  @param  string $template Template File.
 *  @param  boolen $variation File variation.
 */
function buddypress_search_load_template( $template, $variation = false ) {
	$file = $template;

	if ( $variation ) {
		$file .= '-' . $variation;
	}

	bp_get_template_part( 'buddypress-search/' . $file );
}

/**
 * BuddyPress Search page content.
 *
 * @since 1.0.0
 *
 * @param  string $template Template File.
 * @param  boolen $variation File variation.
 * @param boolen $echo Echo the output.
 */
function buddypress_search_template_part( $template, $variation = '', $echo = true ) {

	ob_start();

	buddypress_search_load_template( $template, $variation );
	// Get the output buffer contents.
	$output = ob_get_clean();

	$allowed_atts                = array(
		'align'      => array(),
		'class'      => array(),
		'type'       => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'title'      => array(),
	);
	$allowedposttags['form']     = $allowed_atts;
	$allowedposttags['div']      = $allowed_atts;
	$allowedposttags['img']      = $allowed_atts;
	$allowedposttags['label']    = $allowed_atts;
	$allowedposttags['input']    = $allowed_atts;
	$allowedposttags['textarea'] = $allowed_atts;
	$allowedposttags['iframe']   = $allowed_atts;
	$allowedposttags['script']   = $allowed_atts;
	$allowedposttags['style']    = $allowed_atts;
	$allowedposttags['strong']   = $allowed_atts;
	$allowedposttags['small']    = $allowed_atts;
	$allowedposttags['table']    = $allowed_atts;
	$allowedposttags['span']     = $allowed_atts;
	$allowedposttags['abbr']     = $allowed_atts;
	$allowedposttags['code']     = $allowed_atts;
	$allowedposttags['pre']      = $allowed_atts;
	$allowedposttags['div']      = $allowed_atts;
	$allowedposttags['img']      = $allowed_atts;
	$allowedposttags['h1']       = $allowed_atts;
	$allowedposttags['h2']       = $allowed_atts;
	$allowedposttags['h3']       = $allowed_atts;
	$allowedposttags['h4']       = $allowed_atts;
	$allowedposttags['h5']       = $allowed_atts;
	$allowedposttags['h6']       = $allowed_atts;
	$allowedposttags['ol']       = $allowed_atts;
	$allowedposttags['ul']       = $allowed_atts;
	$allowedposttags['li']       = $allowed_atts;
	$allowedposttags['em']       = $allowed_atts;
	$allowedposttags['hr']       = $allowed_atts;
	$allowedposttags['br']       = $allowed_atts;
	$allowedposttags['tr']       = $allowed_atts;
	$allowedposttags['td']       = $allowed_atts;
	$allowedposttags['p']        = $allowed_atts;
	$allowedposttags['a']        = $allowed_atts;
	$allowedposttags['b']        = $allowed_atts;
	$allowedposttags['i']        = $allowed_atts;
	$allowedposttags['nav']      = $allowed_atts;
	$allowedposttags['button']   = $allowed_atts;
	$allowedposttags['header']   = $allowed_atts;
	// Echo or return the output buffer contents.
	if ( true === $echo ) {
		echo wp_kses( $output, $allowedposttags );
	} else {
		return $output;
	}
}


/**
 * Output the search url
 *
 * @since bbPress
 *
 * @uses bbp_get_search_url() To get the search url
 */
function buddypress_search_url() {
	echo esc_url( buddypress_search_get_url() );
}

/**
 * Return the search url
 *
 * @since bbPress
 *
 * @param boolen $default Default.
 * @return string Search url
 */
function buddypress_search_get_url( $default = true ) {
	global $wp_rewrite;

	// Pretty permalinks.
	if ( $wp_rewrite->using_permalinks() ) {
		$url = $wp_rewrite->root . bbp_get_search_slug();
		$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks.
	} else {
		$url = add_query_arg( array( bbp_get_search_rewrite_id() => '' ), home_url( '/' ) );
	}

	if ( true === $default ) {
		return apply_filters( 'buddypress_search_get_url', add_query_arg( 'buddypress_search', 1, $url ) );
	} else {
		return apply_filters( 'buddypress_search_get_url', $url );
	}

}


/**
 * Returns a trimmed activity content string.
 * Must be used while inside activity loop
 *
 * @since  1.0.0
 */
function buddypress_search_activity_intro( $character_limit = 50 ) {
	$content = '';
	if ( bp_activity_has_content() ) {
		$content = bp_get_activity_content_body();

		if ( $content ) {
			$content = wp_strip_all_tags( $content, true );

			$shortened_content = substr( $content, 0, $character_limit );
			if ( strlen( $content ) > $character_limit ) {
				$shortened_content .= '&hellip;';
			}

			$content = $shortened_content;
		}
	}

	return apply_filters( 'buddypress_search_activity_intro', $content );
}



/**
 * Returns the defulat post thumbnail based on post type
 *
 * @since 1.0.0
 *
 * @param string $post_type Post Type.
 */
function buddypress_search_get_post_thumbnail_default( $post_type ) {

	$default = array(
		'product'             => BP_SEARCH_PLUGIN_URL . '/public/images/search/product.svg',
		'sfwd-courses'        => BP_SEARCH_PLUGIN_URL . '/public/images/search/course.svg',
		'sfwd-lessons'        => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'sfwd-topic'          => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'sfwd-quiz'           => BP_SEARCH_PLUGIN_URL . '/public/images/search/quiz.svg',
		'post'                => BP_SEARCH_PLUGIN_URL . '/public/images/search/blog-post.svg',
		'forum'               => BP_SEARCH_PLUGIN_URL . '/public/images/search/forum.svg',
		'topic'               => BP_SEARCH_PLUGIN_URL . '/public/images/search/forum.svg',
		'reply'               => BP_SEARCH_PLUGIN_URL . '/public/images/search/forum.svg',
		'bp-member-type'      => BP_SEARCH_PLUGIN_URL . '/public/images/search/membership.svg',
		'memberpressproduct'  => BP_SEARCH_PLUGIN_URL . '/public/images/search/membership.svg',
		'wp-parser-function'  => BP_SEARCH_PLUGIN_URL . '/public/images/search/code.svg',
		'wp-parser-class'     => BP_SEARCH_PLUGIN_URL . '/public/images/search/code.svg',
		'wp-parser-hook'      => BP_SEARCH_PLUGIN_URL . '/public/images/search/code.svg',
		'wp-parser-method'    => BP_SEARCH_PLUGIN_URL . '/public/images/search/code.svg',
		'command'             => BP_SEARCH_PLUGIN_URL . '/public/images/search/code.svg',
		'course'              => BP_SEARCH_PLUGIN_URL . '/public/images/search/course.svg',
		'llms_membership'     => BP_SEARCH_PLUGIN_URL . '/public/images/search/membership.svg',
		'lesson'              => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'llms_assignment'     => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'llms_assignment'     => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'llms_certificate'    => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'llms_my_certificate' => BP_SEARCH_PLUGIN_URL . '/public/images/search/course-content.svg',
		'llms_quiz'           => BP_SEARCH_PLUGIN_URL . '/public/images/search/quiz.svg',
	);

	return isset( $default[ $post_type ] ) ?
			$default[ $post_type ] :
			BP_SEARCH_PLUGIN_URL . '/public/images/search/post-type.svg';
}


/**
 * Returns a trimmed reply content string.
 * Works for replies as well as topics.
 * Must be used while inside the loop
 *
 * @since 1.0.0
 *
 * @param int $character_limit Character Limit.
 */
function buddypress_search_reply_intro( $character_limit = 50 ) {
	$content = '';

	switch ( get_post_type( get_the_ID() ) ) {
		case 'topic':
			$reply_content = bbp_get_topic_content( get_the_ID() );
			break;
		case 'reply':
			$reply_content = bbp_get_reply_content( get_the_ID() );
			break;
		default:
			$reply_content = get_the_content();
			break;
	}

	if ( $reply_content ) {
		$content = wp_strip_all_tags( $reply_content, true );
	}

	return apply_filters( 'buddypress_search_reply_intro', $content );
}

/**
 * Returns   highlighted search keyword and trimmed content string with
 *
 * @since 1.0.0
 *
 * @param string $content Search Result content.
 * @param int    $character_limit Character Limit.
 *
 * @return mixed|void
 */
function buddypress_search_result_intro( $content, $character_limit = 50 ) {

	$content           = wp_strip_all_tags( $content, true );
	$shortened_content = substr( $content, 0, $character_limit );

	if ( strlen( $content ) > $character_limit ) {
		$shortened_content .= '&hellip;';
	}

	$content = $shortened_content;

	return apply_filters( 'buddypress_search_result_intro', $content );
}


/**
 * Returns total number of LearnDash lessons
 *
 * @since 1.0.0
 * @param int $course_id Course ID.
 */
function buddypress_search_get_total_lessons_count( $course_id ) {
	$lesson_ids = learndash_course_get_children_of_step( $course_id, $course_id, 'sfwd-lessons' );

	return count( $lesson_ids );
}

/**
 * Returns total number of LearnDash topics
 *
 * @since 1.0.0
 * @param int $lesson_id Lesson ID.
 */
function buddypress_search_get_total_topics_count( $lesson_id ) {
	$course_id = learndash_get_course_id( $lesson_id );
	$topic_ids = learndash_course_get_children_of_step( $course_id, $lesson_id, 'sfwd-topic' );

	return count( $topic_ids );
}

/**
 * Returns total number of LearnDash quizzes.
 *
 * @since 1.0.0
 * @param int $lesson_id Lesson ID.
 */
function buddypress_search_get_total_quizzes_count( $lesson_id ) {
	$course_id = learndash_get_course_id( $lesson_id );
	$quiz_ids  = learndash_course_get_children_of_step( $course_id, $lesson_id, 'sfwd-quiz' );

	return count( $quiz_ids );
}
add_filter( 'template_include', 'buddypress_search_overwrite_wp_native_results', 999 );

/**
 * Force native wp search section to load page template so we can hook stuff into it.
 *
 * @since 1.0.0
 *
 * @param string $template Template URL.
 **/
function buddypress_search_overwrite_wp_native_results( $template ) {
	if ( wp_verify_nonce( ! empty( $_REQUEST['bp-search-nonce'] ), 'buddypress-seach-nonce' ) ) {
		if ( ! is_admin() && is_search() && isset( $_REQUEST['buddypress_search'] ) ) { // if search page.

			$live_template = locate_template(
				array(
					'buddypress-global-search.php',
					'page.php',
					'single.php',
					'index.php',
				)
			);

			if ( '' != $live_template ) {
				return $live_template;
			}
		}
	} else {
		if ( ! is_admin() && is_search() && isset( $_REQUEST['buddypress_search'] ) ) { // if search page.

			$live_template = locate_template(
				array(
					'buddypress-global-search.php',
					'page.php',
					'single.php',
					'index.php',
				)
			);

			if ( '' != $live_template ) {
				return $live_template;
			}
		}
	}
	return $template;
}

/**
 * Load dummy post for wp native search result. magic starts here.
 *
 * @since 1.0.0
 *
 * @param string $template Template URL.
 */
function buddypress_search_result_page_dummy_post_load( $template ) {
	global $wp_query;
	if ( wp_verify_nonce( ! empty( $_REQUEST['bp-search-nonce'] ), 'buddypress-seach-nonce' ) ) {
		return false;
	}
	if ( ! is_search() || ! isset( $_REQUEST['buddypress_search'] ) ) { // cancel if not search page.
		return $template;
	}

	$dummy = array(
		'ID'                    => 0,
		'post_status'           => 'public',
		'post_author'           => 0,
		'post_parent'           => 0,
		'post_type'             => 'page',
		'post_date'             => 0,
		'post_date_gmt'         => 0,
		'post_modified'         => 0,
		'post_modified_gmt'     => 0,
		'post_content'          => '',
		'post_title'            => '',
		'post_excerpt'          => '',
		'post_content_filtered' => '',
		'post_mime_type'        => '',
		'post_password'         => '',
		'post_name'             => '',
		'guid'                  => '',
		'menu_order'            => 0,
		'pinged'                => '',
		'to_ping'               => '',
		'ping_status'           => '',
		'comment_status'        => 'closed',
		'comment_count'         => 0,
		'filter'                => 'raw',
		'is_404'                => false,
		'is_page'               => false,
		'is_single'             => false,
		'is_archive'            => false,
		'is_tax'                => false,
		'is_search'             => true,
	);
	// Set the $post global.
	$post = new WP_Post( (object) $dummy );

	// Copy the new post global into the main $wp_query.
	$wp_query->post          = $post;
	$wp_query->posts         = array( $post );
	$wp_query->post_count    = 1;
	$wp_query->max_num_pages = 0;

	return $template;
}
add_filter( 'template_include', 'buddypress_search_result_page_dummy_post_load', 999 );

/**
 * Force native wp search page not to look any data into db to save query and performance
 *
 * @since 1.0.0
 *
 * @param mixed $query WP Query.
 *
 * @return mixed
 **/
function buddypress_search_clear_native_search_query( $query ) {
	if ( ! wp_verify_nonce( ! empty( $_REQUEST['bp-search-nonce'] ), 'buddypress-seach-nonce' ) ) {
		return false;
	}
	if ( isset( $_GET['s'] ) && isset( $_REQUEST['buddypress_search'] ) ) {
		unset( $query->query['page'] );
		unset( $query->query['pagename'] );
		unset( $query->query_vars['page'] );
		unset( $query->query_vars['pagename'] );
	}

	if ( ! is_admin() && is_search() && isset( $_REQUEST['buddypress_search'] ) ) {
		remove_filter( 'pre_get_posts', 'buddypress_search_clear_native_search_query' ); // only do first time.

	}

	return $query;
}
add_filter( 'pre_get_posts', 'buddypress_search_clear_native_search_query' );

/**
 * Add 'buddypress' and 'directory' in Body tag classes list
 *
 * @param string $wp_classes Body Class.
 *
 * @return mixed|void
 */
function buddypress_search_body_class( $wp_classes ) {
	if ( wp_verify_nonce( ! empty( $_REQUEST['bp-search-nonce'] ), 'buddypress-seach-nonce' ) ) {
		return false;
	}
	if ( ! is_admin() && is_search() && isset( $_REQUEST['buddypress_search'] ) ) { // if search page.
		$wp_classes[] = 'buddypress';
		$wp_classes[] = 'directory';
		$wp_classes[] = 'buddypress-search';
	}

	return apply_filters( 'buddypress_search_body_class', $wp_classes );
}
add_filter( 'body_class', 'buddypress_search_body_class', 10, 1 );


/**
 * BuddyPress global search items options
 *
 * @since 1.0.0
 *
 * @return mixed|void
 */
function buddypress_search_items() {

	$items = array(
		'posts'          => __( 'Blog Posts', 'buddypress-search' ),
		'pages'          => __( 'Pages', 'buddypress-search' ),
		'posts_comments' => __( 'Post Comments', 'buddypress-search' ),
		'members'        => __( 'Members', 'buddypress-search' ),
	);

	// forums?

	$items['forum'] = __( 'Forums', 'buddypress-search' );
	$items['topic'] = __( 'Forum Discussions', 'buddypress-search' );
	$items['reply'] = __( 'Forum Replies', 'buddypress-search' );

	// other buddypress components.
	$bp_components = array(
		'groups'   => __( 'Groups', 'buddypress-search' ),
		'activity' => __( 'Activity', 'buddypress-search' ),
		'messages' => __( 'Messages', 'buddypress-search' ),
		/*
		* should we search notifications as well?
		*'notifications' => __( 'Notifications', 'buddypress-search' ),
		*/
	);

	// only the active ones please!
	foreach ( $bp_components as $component => $label ) {
		if ( function_exists( 'bp_is_active' ) && bp_is_active( $component ) ) {
			$items[ $component ] = $label;

			if ( 'activity' === $component ) {
				$items['activity_comment'] = __( 'Activity Comments', 'buddypress-search' );
			}
		}
	}

	return apply_filters( 'buddypress_search_items', $items );
}

/**
 * Outputs BuddyPress search pagination number viewing and total
 *
 * @since 1.0.0
 *
 * @param int $total_items Total Items.
 * @param int $items_per_page Total Items per page.
 * @param int $curr_paged Items in current page.
 * @return mixed|void
 */
function buddypress_search_pagination_page_counts( $total_items, $items_per_page, $curr_paged ) {

	if ( $curr_paged == 0 ) {
		$curr_paged = 1;
	}

	$to_num   = $curr_paged * $items_per_page;
	$from_num = $to_num - ( $items_per_page - 1 );

	?>
	<div class="pag-count bottom">
		<div class="pag-data">
			<?php
			/* translators: %s: */
			printf( esc_html__( 'Viewing %1$d - %2$d of %3$d results', 'buddypress-search' ), esc_html( $from_num ), esc_html( min( $total_items, $to_num ) ), esc_html( $total_items ) );
			?>
		</div>
	</div>
	<?php
}

/**
 * Outputs BuddyPress search pagination number viewing and total
 *
 * @since 1.0.0
 *
 * @param int $total_items Total Items.
 * @param int $items_per_page Total Items per page.
 * @param int $curr_paged Items in current page.
 * @param int $slug Page Slug.
 * @param int $links_on_each_side Links on each side.
 * @param int $hashlink hashlink.
 * @param int $param_key parameter key.
 * @return mixed|void
 */
function buddypress_search_pagination( $total_items, $items_per_page, $curr_paged, $slug, $links_on_each_side = 2, $hashlink = '', $param_key = 'list' ) {
	$use_bootstrap = false;
	if ( wp_verify_nonce( ! empty( $_REQUEST['bp-search-nonce'] ), 'buddypress-seach-nonce' ) ) {
		return false;
	}
	if ( defined( 'BOOTSTRAP_ACTIVE' ) ) {
		$use_bootstrap = true;
	}

	$s = $links_on_each_side; // no of tabs to show for previos/next paged links.
	if ( $curr_paged == 0 ) {
		$curr_paged = 1;
	}
	/*
	$elements : an array of arrays; each child array will have following structure
	$child[0] = text of the link
	$child[1] = page no of target page
	$child[2] = link type :: link|current|nolink
	 */
	$elements    = array();
	$no_of_pages = ceil( $total_items / $items_per_page );
	// prev lik.
	if ( $curr_paged > 1 ) {
		$elements[] = array( '&larr;', $curr_paged - 1, 'link' );
	}
	// generating $s(2) links before the current one.
	if ( $curr_paged > 1 ) {
		$rev_array = array(); // paged in reverse order.
		$i         = $curr_paged - 1;
		$counter   = 0;
		while ( $counter < $s && $i > 0 ) {
			$rev_array[] = $i;
			$i --;
			$counter ++;
		}
		$arr = array_reverse( $rev_array );
		if ( $counter == $s ) {
			$elements[] = array( ' &hellip; ', '', 'nolink' );
		}
		foreach ( $arr as $el ) {
			$elements[] = array( $el, $el, 'link' );
		}
		unset( $rev_array );
		unset( $arr );
		unset( $i );
		unset( $counter );
	}

	// generating $s+1(3) links after the current one (includes current).
	if ( $curr_paged <= $no_of_pages ) {
		$i       = $curr_paged;
		$counter = 0;
		while ( $counter < $s + 1 && $i <= $no_of_pages ) {
			if ( $i == $curr_paged ) {
				$elements[] = array( $i, $i, 'current' );
			} else {
				$elements[] = array( $i, $i, 'link' );
			}
			$counter ++;
			$i ++;
		}
		if ( $counter == $s + 1 ) {
			$elements[] = array( ' &hellip; ', '', 'nolink' );
		}
		unset( $i );
		unset( $counter );
	}
	// next link.
	if ( $curr_paged < $no_of_pages ) {
		$elements[] = array( '&rarr;', $curr_paged + 1, 'link' );
	}
	/* enough php, lets echo some html */
	if ( isset( $elements ) && count( $elements ) > 1 ) {
		?>
		<div class="pagination navigation">
			<?php if ( $use_bootstrap ) : ?>
			<div class='pagination-links'>
				<?php else : ?>
				<div class="pagination-links">
					<?php endif; ?>
					<?php
					foreach ( $elements as $e ) {
						$link_html = '';
						$class     = '';
						switch ( $e[2] ) {
							case 'link':
								unset( $_GET[ $param_key ] );
								$base_link = get_bloginfo( 'url' ) . "/$slug?";
								foreach ( $_GET as $k => $v ) {
									$base_link .= "$k=$v&";
								}
								$base_link .= "$param_key=$e[1]";
								if ( isset( $hashlink ) && $hashlink != '' ) {
									$base_link .= "#$hashlink";
								}
								$link_html = "<a href='$base_link' title='$e[0]' class='page-numbers' data-pagenumber='$e[1]'>$e[0]</a>";
								break;
							case 'current':
								$class = 'active';
								if ( $use_bootstrap ) {
									$link_html = "<span>$e[0] <span class='sr-only'>(current)</span></span>";
								} else {
									$link_html = "<span class='page-numbers current'>$e[0]</span>";
								}
								break;
							default:
								if ( $use_bootstrap ) {
									$link_html = "<span>$e[0]</span>";
								} else {
									$link_html = "<span class='page-numbers'>$e[0]</span>";
								}
								break;
						}

						// $link_html = "<li class='" . esc_attr($class) . "'>" . $link_html . "</li>";
						echo wp_kses_post( $link_html );
					}
					?>
					<?php if ( $use_bootstrap ) : ?>
				</div>
				<?php else : ?>
			</div>
		<?php endif; ?>
		</div>
		<?php
	}
}
