<?php
/**
 * Result page template.
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
$post_title = '';
$nonce      = isset( $_GET['bp-search-nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['bp-search-nonce'] ) ) : '';
if ( isset( $_GET ) && ! wp_verify_nonce( $nonce, 'bp-search-nonce' ) ) {
	if ( empty( $_GET['s'] ) || '' === $_GET['s'] ) {
		$post_title = __( 'No results found', 'buddypress-search' );
	} elseif ( $buddypress_search_obj->has_buddypress_search_results() ) {
		/* translators: %s: Get search term */
		$post_title = sprintf( __( 'Showing results for \'%s\'', 'buddypress-search' ), esc_html( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) );

	} else {
		/* translators: %s: Get search term */
		$post_title = sprintf( __( 'No results for \'%s\'', 'buddypress-search' ), esc_html( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) );
	}
}
?>
<header class="entry-header">
	<h1 class="entry-title">
		<?php echo esc_html( stripslashes( $post_title ) ); ?>
	</h1>
</header>

<div id="buddypress" class="buddypress-search">

	<?php buddypress_search_template_part( 'results-page-content' ); ?>

</div>
