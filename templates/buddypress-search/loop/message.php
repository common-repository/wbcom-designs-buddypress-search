<?php
/**
 * Message listing.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<?php global $current_message; ?>
<li class="bp-search-item bp-search-item_message">
	<p class="message_participants">
		<?php
		esc_html_e( 'Conversation between', 'buddypress-search' );
		$participants = array();
		foreach ( $current_message->recepients as $recepient_id ) {
			if ( $recepient_id == get_current_user_id() ) {
				continue;
			}

			$participants[] = bp_core_get_userlink( $recepient_id );
		}

		echo ' ' . esc_html( implode( ', ', $participants ) ) . ' ' . esc_html_e( 'and you.', 'buddypress-search' );
		?>
		<span class='view_thread_link'>
			<a href='<?php echo esc_url( trailingslashit( bp_loggedin_user_domain() ) ) . 'messages/view/' . esc_html( $current_message->thread_id ) . '/'; ?>'>
				<?php esc_html_e( 'View Conversation', 'buddypress-search' ); ?>
			</a>
		</span>
	</p>
	<div class="conversation">
		<div class="item-avatar">
			<a href="<?php echo esc_url( bp_core_get_userlink( $current_message->sender_id, true, true ) ); ?>">
				<?php
				echo wp_kses_post(
					bp_core_fetch_avatar(
						array(
							'item_id' => $current_message->sender_id,
							'width'   => 50,
							'height'  => 50,
						)
					)
				);
				?>
			</a>
		</div>

		<div class="item">
			<div class="item-title">
				<a href="<?php echo esc_url( trailingslashit( bp_loggedin_user_domain() ) ) . 'messages/view/' . esc_html( $current_message->thread_id ) . '/'; ?>">
					<?php echo esc_html( stripslashes( $current_message->subject ) ); ?>
				</a>
			</div>
			<div class="item-desc">
				<?php
					$content         = wp_strip_all_tags( $current_message->message );
					$trimmed_content = wp_trim_words( $content, 20, '&hellip;' );
					echo esc_html( $trimmed_content );
				?>
			</div>
		</div>
	</div>
</li>
