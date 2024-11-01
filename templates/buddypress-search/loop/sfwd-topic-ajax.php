<?php
/**
 * Learndash topic ajax.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<?php $total = buddypress_search_get_total_quizzes_count( get_the_ID() ); ?>
<div class="bp-search-ajax-item bp-search-ajax-item_sfwd-topic">
	<a href="<?php echo esc_url( add_query_arg( array( 'no_frame' => '1' ), get_permalink() ) ); ?>">
		<div class="item-avatar">
			<img
				src="<?php echo get_the_post_thumbnail_url() ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
				class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
				alt="<?php the_title(); ?>"
			/>
		</div>

		<div class="item">
			<div class="item-title"><?php the_title(); ?></div>
			<div class="item-desc">
			<?php
			/* translators: %s: the number of quizz count */
			$$total_quiz_count_custom = sprintf( _n( '%d quiz', '%d quizzes', $total, 'buddypress-search' ), $total );
			echo esc_html( $total_quiz_count_custom );
			?>
			</div>

		</div>
	</a>
</div>
