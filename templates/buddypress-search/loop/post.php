<?php
/**
 * Post listing.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<li class="bp-search-item bp-search-item_post">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo get_the_post_thumbnail_url( get_the_ID() ) ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="<?php the_title(); ?>"/>
			</a>
		</div>

		<div class="item">
			<h3 class="entry-title item-title">
				<a href="<?php the_permalink(); ?>" title="
												<?php
												echo esc_attr(
													sprintf(
															/* translators: %s: title attribute */
														__(
															'Permalink to %s',
															'buddypress-search'
														),
														the_title_attribute( 'echo=0' )
													)
												);
												?>
					" rel="bookmark"><?php the_title(); ?></a>
			</h3>

			<div class="entry-content entry-summary">
				<?php echo esc_html( make_clickable( get_the_excerpt() ) ); ?>
			</div>

			<?php if ( get_post_type() == 'post' ) { ?>
				<div class="entry-meta">
					<span class="author">
						<?php /* translators: %s: author link */ ?>
						<?php printf( esc_html__( 'By %s', 'buddypress-search' ), get_the_author_link() ); ?>
					</span> <span class="middot">&middot;</span> <span class="published">
						<?php the_date(); ?>
					</span>
				</div>
			<?php } ?>
		</div>
	</div>
</li>
