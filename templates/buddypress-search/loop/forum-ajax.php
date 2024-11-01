<?php
/**
 * Ajax forum.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

$total              = bbp_get_forum_topic_count( get_the_ID() );
$post_thumbnail_url = get_the_post_thumbnail_url( get_the_ID() ) ?: buddypress_search_get_post_thumbnail_default( get_post_type() );
?>
<div class="bp-search-ajax-item bp-search-ajax-item_forum">
	<a href="<?php echo esc_url( add_query_arg( array( 'no_frame' => '1' ), bbp_get_forum_permalink( get_the_ID() ) ) ); ?>">
		<div class="item-avatar">
			<img
				src="<?php echo esc_url( $post_thumbnail_url ); ?>"
				class="avatar forum-avatar"
				height="150"
				width="150"
				alt=""
			/>
		</div>
		<div class="item">
			<div class="item-title"><?php bbp_forum_title( get_the_ID() ); ?></div>
			<div class="item-desc">
			<?php
			/* translators: %s: the number of forum topic count */
			$forum_topic_count_custom = sprintf( _n( '%d topic', '%d topics', $total, 'buddypress-search' ), $total );
			echo esc_html( $forum_topic_count_custom );
			?>
			</div>
		</div>
	</a>
</div>
