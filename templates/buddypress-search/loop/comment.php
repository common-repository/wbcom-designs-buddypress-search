<?php
/**
 * Current Comments.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<?php global $current_comment; ?>
<li class="bp-search-item bp-search-item_posts_comments">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php comment_author_link( $current_comment ); ?>">
				<?php echo get_avatar( $current_comment->user_id, 50 ); ?>
			</a>
		</div>

		<div class="item">
			<a href="<?php comment_link( $current_comment ); ?>">
				<div class="item-desc"><?php echo esc_html( buddypress_search_result_intro( $current_comment->comment_content, 150 ) ); ?></div>
			</a>
		</div>
	</div>
</li>
