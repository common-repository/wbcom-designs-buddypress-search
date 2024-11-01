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
$bpsearch_live_search_settings                       = get_option( 'bpsearch_live_search_settings' );
$bpsearch_live_search_settings['enable_live_search'] = ( isset( $bpsearch_live_search_settings['enable_live_search'] ) ) ? $bpsearch_live_search_settings['enable_live_search'] : '';
$bpsearch_live_search_settings['number_of_results']  = ( isset( $bpsearch_live_search_settings['number_of_results'] ) ) ? $bpsearch_live_search_settings['number_of_results'] : '';
?>
<div class="wbcom-tab-content">
<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
   
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpsearch_live_search_settings_section' );
		do_settings_sections( 'bpsearch_live_search_settings_section' );
		?>
		<div class="container">
		<div class="form-table">
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options">
				<div class="wbcom-settings-section-options"> 
				<div class="bp-search-parent-field">
					<div class="wbcom-settings-section-options-heading">							
						<label for='bp_live_search_enable'><?php esc_html_e( 'Enable Autocomplete', 'buddypress-search' ); ?></label>
					</div>
						<input type="checkbox" name="bpsearch_live_search_settings[enable_live_search]" value="1" id="bp_live_search_enable" <?php checked( $bpsearch_live_search_settings['enable_live_search'], 1 ); ?>/>
				</div>
				</div>
			</div>
			<div class="wbcom-settings-section-options">
				<div class="bp-search-parent-field">
					<div class="wbcom-settings-section-options-heading">							
						<label for='bp_search_number_of_results'><?php esc_html_e( 'Number of Results', 'buddypress-search' ); ?></label>
					</div>
					<input name="bpsearch_live_search_settings[number_of_results]" id="bp_search_number_of_results" type="number" min="1" step="1" value="<?php echo esc_html( $bpsearch_live_search_settings['number_of_results'] ); ?>" class="small-text">
					<label for="bp_search_number_of_results"><?php esc_html_e( 'results', 'buddypress-search' ); ?></label>
				</div>
				</div>
			</div>
		</div>
		</div>		
		<?php submit_button(); ?>
	</form>
</div>
</div>
