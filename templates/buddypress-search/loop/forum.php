<?php
/**
 * Forum.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

$forum_id           = get_the_ID();
$total_topic        = bbp_get_forum_topic_count( $forum_id );
$total_reply        = bbp_get_forum_reply_count( $forum_id );
$post_thumbnail_url = get_the_post_thumbnail_url( $forum_id ) ?: buddypress_search_get_post_thumbnail_default( get_post_type() );
?>
<li class="bp-search-item bp-search-item_forum">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php bbp_forum_permalink( get_the_ID() ); ?>">
				<img src="<?php echo esc_url( $post_thumbnail_url ); ?>" class="avatar forum-avatar" height="150" width="150" alt=""/>
			</a>
		</div>

		<div class="item">
			<div class="entry-title item-title">
				<a href="<?php bbp_forum_permalink( $forum_id ); ?>"><?php bbp_forum_title( $forum_id ); ?></a>
			</div>
			<div class="entry-content entry-summary">
				<?php echo esc_html( make_clickable( get_the_excerpt() ) ); ?>
			</div>
			<div class="entry-meta">
				<span class="topic-count">
					<?php
					/* translators: %s: the number of forum topic count */
					$forum_count_topic_custom = sprintf( _n( '%d topic', '%d topics', $total_topic, 'buddypress-search' ), $total_topic );
					echo esc_html( $forum_count_topic_custom );
					?>
					
				</span> <span class="middot">&middot;</span> <span class="reply-count">
				<?php
				/* translators: %s: the number of forum reply count */
				$forum_count_reply_custom = sprintf( _n( '%d reply', '%d replies', $total_reply, 'buddypress-search' ), $total_reply );
				echo esc_html( $forum_count_reply_custom );
				?>
				</span> <span class="middot">&middot;</span> <span class="freshness">
					<?php bbp_forum_freshness_link( $forum_id ); ?>
				</span>
			</div>
		</div>
	</div>
</li>
