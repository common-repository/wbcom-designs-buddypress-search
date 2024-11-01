<?php
/**
 * Learndash topic listings.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<?php $total = buddypress_search_get_total_quizzes_count( get_the_ID() ); ?>
<li class="bp-search-item bp-search-item_sfwd-topic">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php the_permalink(); ?>">
				<img
					src="<?php echo get_the_post_thumbnail_url() ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
					class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
					alt="<?php the_title(); ?>"
				/>
			</a>
		</div>

		<div class="item">
			<h3 class="entry-title item-title">
				<?php /* translators: %s: Title Attributes */ ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'buddypress-search' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h3>

			<div class="entry-content entry-summary">
				<?php
				/* translators: %s: the number of total quizzes count */
				$total_quizess_count_custom = sprintf( _n( '%d quiz', '%d quizzes', $total, 'buddypress-search' ), $total );
				echo esc_html( $total_quizess_count_custom );
				?>
			</div>
		</div>
	</div>
</li>
