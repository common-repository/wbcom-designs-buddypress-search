<?php
/**
 * BBPress topic reply ajax.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<div class="bp-search-ajax-item bp-search-ajax-item_reply">
	<a href="<?php echo esc_url( add_query_arg( array( 'no_frame' => '1' ), bbp_get_reply_url( get_the_ID() ) ) ); ?>">
		<div class="item-avatar">
			<img
				src="<?php echo get_the_post_thumbnail_url( bbp_get_forum_id( get_the_ID() ) ) ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
				class="avatar forum-avatar"
				height="150"
				width="150"
				alt=""
			/>
		</div>
		<div class="item">
			<div class="item-title">
				<?php echo esc_html( stripslashes( wp_strip_all_tags( bbp_forum_title( get_the_ID() ) ) ) ); ?>
			</div>
			<div class="item-desc"><?php echo esc_html( buddypress_search_reply_intro( 30 ) ); ?></div>
		</div>
	</a>
</div>
