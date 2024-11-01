<?php
/**
 * BuddyPress object search form.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

global $buddypress_search_obj;
$nonce = isset( $_REQUEST['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['bp-search-nonce'] ) ) : '';
if ( ! empty( $nonce ) ) {
	if ( ! wp_verify_nonce( $nonce, 'buddypress-seach-nonce' ) ) {
		die( esc_html_e( 'Security check', 'buddypress-search' ) );
	}
}
$search_term             = ! empty( $_REQUEST['s'] ) ? esc_html( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) ) : '';
$bpsearch_search_results = get_option( 'bpsearch_search_results_settings' );

if ( isset( $bpsearch_search_results['overide_default_search'] ) && $bpsearch_search_results['overide_default_search'] == 1 && isset( $bpsearch_search_results['search_results_page'] ) && $bpsearch_search_results['search_results_page'] != '' ) {
	$url = get_permalink( $bpsearch_search_results['search_results_page'] );
} else {
	$url = home_url( '/' );
}
?>

<div class="<?php bp_nouveau_search_container_class(); ?> buddypress-search-form">
	<form action="<?php echo esc_url( $url ); ?>" method="get" class="buddypress-dir-search-form" id="<?php bp_nouveau_search_selector_id( 'search-form' ); ?>" role="search">
		<input
			id="<?php bp_nouveau_search_selector_id( 'search' ); ?>"
			name="s"
			type="search"
			value="<?php echo $buddypress_search_obj->has_buddypress_search_results() ? esc_html( $search_term ) : ''; ?>"
			placeholder="<?php echo $buddypress_search_obj->has_buddypress_search_results() ? esc_html_e( 'Search Network&hellip;', 'buddypress-search' ) : esc_html_e( 'Try different keywords&hellip;', 'buddypress-search' ); ?>"
		/>
		<?php wp_nonce_field( 'buddypress-seach-nonce', 'bp-search-nonce' ); ?>
		<button type="submit" id="<?php bp_nouveau_search_selector_id( 'search-submit' ); ?>" class="nouveau-search-submit submit button">
			<span class="dashicons dashicons-search" aria-hidden="true"></span>
			<span id="button-text" class="screen-reader-text"><?php esc_html_e( 'Search', 'buddypress-search' ); ?></span>
		</button>

	</form>
</div>
