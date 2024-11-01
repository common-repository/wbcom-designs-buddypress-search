<?php
/**
 * BBPress topic listings.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

$topic_id = get_the_ID();
$total    = bbp_get_topic_reply_count( $topic_id )
?>
<li class="bp-search-item bp-search-item_topic">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php bbp_topic_permalink( $topic_id ); ?>">
				<img
					src="<?php echo get_the_post_thumbnail_url( bbp_get_forum_id( $topic_id ) ) ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
					class="avatar forum-avatar"
					height="150"
					width="150"
					alt=""
				/>
			</a>
		</div>

		<div class="item">
			<h3 class="entry-title item-title">
				<a href="<?php bbp_topic_permalink( $topic_id ); ?>"><?php bbp_topic_title( $topic_id ); ?></a>
			</h3>
			<div class="entry-content entry-summary">
				<?php echo esc_html( wp_trim_words( bbp_get_topic_content( $topic_id ), 30, '...' ) ); ?>
			</div>
			<div class="entry-meta">
				<span class="reply-count">
					<?php
					/* translators: %s: the number of lesson count */
					$topic_rply_count_custom = sprintf( _n( '%d reply', '%d replies', $total, 'buddypress-search' ), $total );
					echo esc_html( $topic_rply_count_custom );
					?>
				</span>
				<span class="middot">&middot;</span>
				<span class="freshness">
					<?php bbp_topic_freshness_link( $topic_id ); ?>
				</span>
			</div>
			<?php
			$discussion_tags = get_the_terms( $topic_id, bbpress()->topic_tag_tax_id );

			if ( ! empty( $discussion_tags ) ) {
				?>
				<div class="item-tags">
					<span class="item-tag-cap">
						<?php
						esc_html_e( 'Tags:', 'buddypress-search' );
						?>
					</span>
					<?php
					$tags_count = ( is_array( $discussion_tags ) || is_object( $discussion_tags ) ) ? count( $discussion_tags ) : 0;
					$loop_count = 1;
					foreach ( $discussion_tags as $key => $discussion_tag ) {
						?>
						<span class="discussion-tag">
							<?php
							echo esc_html( $discussion_tag->name );
							if ( $tags_count != $loop_count ) {
								echo ', ';
							}
							?>
						</span>
						<?php
						$loop_count++;
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</li>
