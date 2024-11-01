<?php
/**
 * Post Ajax.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<div class="bp-search-ajax-item bp-search-ajax-item_post">
	<a href="<?php echo esc_url( add_query_arg( array( 'no_frame' => '1' ), get_permalink() ) ); ?>">
		<div class="item-avatar">
			<img
				src="<?php echo get_the_post_thumbnail_url( get_the_ID() ) ?: esc_html( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
				class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
				alt="<?php the_title(); ?>"
			/>
		</div>

		<div class="item">
			<div class="item-title"><?php the_title(); ?></div>
			<?php
				$content = wp_strip_all_tags( make_clickable( get_the_excerpt() ) );
				preg_match_all( '^\[(.*?)\]^', $content, $matches, PREG_PATTERN_ORDER );  // strip all shortcodes in the ajax search content.
				$content         = str_replace( $matches[0], '', $content );
				$trimmed_content = wp_trim_words( $content, 20, '&hellip;' );
			?>
			<div class="item-desc"><?php echo esc_html( $trimmed_content ); ?></div>

		</div>
	</a>
</div>
