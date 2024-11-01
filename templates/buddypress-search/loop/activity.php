<?php
/**
 * BuddyPress Activity.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<li class="bp-search-item bp-search-item_activity <?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>" data-bp-activity-id="<?php bp_activity_id(); ?>" data-bp-timestamp="<?php bp_nouveau_activity_timestamp(); ?>">
	<div class="list-wrap">
		<div class="activity-avatar item-avatar">
			<a href="<?php bp_activity_user_link(); ?>">
				<?php bp_activity_avatar( array( 'type' => 'full' ) ); ?>
			</a>
		</div>

		<div class="item activity-content">
			<div class="activity-header">
				<?php
				echo wp_kses_post( bp_get_activity_action( array( 'no_timestamp' => true ) ) );
				?>
			</div>
			<?php if ( bp_nouveau_activity_has_content() ) : ?>
				<div class="activity-inner"><?php echo esc_html( wp_trim_words( $GLOBALS['activities_template']->activity->content, '20', '...' ) ); ?></div>
			<?php endif; ?>
			<div class="item-meta">
				<a href="<?php bp_activity_thread_permalink(); ?>">
					<time>
						<?php echo esc_html( human_time_diff( bp_nouveau_get_activity_timestamp() ) ) . '&nbsp;' . esc_html__( 'ago', 'buddypress-search' ); ?>
					</time>
				</a>
			</div>
		</div>
	</div>
</li>
