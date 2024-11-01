<?php
/**
 * Merssage Ajax.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>

<?php global $current_message; ?>
<div class="bp-search-ajax-item bp-search-ajax-item_ajax">
	<a href='<?php echo esc_url( add_query_arg( array( 'no_frame' => '1' ), trailingslashit( bp_loggedin_user_domain() ) . 'messages/view/' . $current_message->thread_id . '/' ) ); ?>'>
		<div class="item">
			<div class="item-title">
				<?php echo esc_html( stripslashes( wp_strip_all_tags( $current_message->subject ) ) ); ?>
			</div>
			<div class="item-desc">
				<?php esc_html_e( 'From:', 'buddypress-search' ); ?> <?php echo esc_html( bp_core_get_user_displayname( $current_message->sender_id ) ); ?>
			</div>
		</div>
	</a>
</div>
