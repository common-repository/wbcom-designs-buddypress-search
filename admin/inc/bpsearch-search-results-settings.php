<?php
/**
 *
 * This file is called for general settings section at admin settings.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Search
 * @subpackage Buddypress_Search/admin/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bpsearch_search_results_settings = get_option( 'bpsearch_search_results_settings' );

?>
<div class="wbcom-tab-content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpsearch_search_results_settings_section' );
		do_settings_sections( 'bpsearch_search_results_settings_section' );
		?>
		<div class="container">
			<table class="form-table">		
				<tr class="bp-search-parent-field">						
					<th scope="row"><label for='overide_default_search'><?php esc_html_e( 'Overide Default WordPress Search', 'buddypress-search' ); ?></label></th>
					<td>						
						<label class="wb-switch">
							<input type="checkbox" id="overide_default_search" name="bpsearch_search_results_settings[overide_default_search]" value="1" <?php checked( isset( $bpsearch_search_results_settings['overide_default_search'] ) ? $bpsearch_search_results_settings['overide_default_search'] : '', '1' ); ?> >
							<div class="wb-slider wb-round"></div>
						</label>
					</td>
				</tr>
				<tr id="bp-search-search_results_page" style="display:none;">
					<th scope="row">
						<label><?php esc_html_e( 'Search results page', 'buddypress-search' ); ?></label>
					</th>
					<td>						
						<?php
						$args = array(
							'name'             => 'bpsearch_search_results_settings[search_results_page]',
							'id'               => 'search_results_page',
							'sort_column'      => 'menu_order',
							'sort_order'       => 'ASC',
							'show_option_none' => ' ',
							'class'            => 'search_results_page',
							'echo'             => false,
							'selected'         => absint( ( isset( $bpsearch_search_results_settings['search_results_page'] ) ) ? $bpsearch_search_results_settings['search_results_page'] : 0 ),
							'post_status'      => 'publish',
						);

						if ( isset( $value['args'] ) ) {
							$args = wp_parse_args( $value['args'], $args );
						}

						echo wp_dropdown_pages( $args ); // WPCS: XSS ok.
						?>

						<p class="description"><?php esc_html_e( 'Assign custom search results page.', 'buddypress-search' ); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<?php submit_button(); ?>
	</form>
</div>
