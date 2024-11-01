<?php
/**
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
$bpsearch_bp_search = get_option( 'bpsearch_bp_search' );
if ( $bpsearch_bp_search == '' ) {
	$bpsearch_bp_search = array();
}
$user_fields = buddypress_search_get_user_fields();
$groups      = bp_xprofile_get_groups(
	array(
		'fetch_fields' => true,
	)
);

$bpsearch_bp_search['bp_search_groups_hidden']  = ( isset( $bpsearch_bp_search['bp_search_groups_hidden'] ) ) ? $bpsearch_bp_search['bp_search_groups_hidden'] : '';
$bpsearch_bp_search['bp_search_groups_private'] = ( isset( $bpsearch_bp_search['bp_search_groups_private'] ) ) ? $bpsearch_bp_search['bp_search_groups_private'] : '';
$bpsearch_bp_search['bp_search_members']        = ( isset( $bpsearch_bp_search['bp_search_members'] ) ) ? $bpsearch_bp_search['bp_search_members'] : '';
$bpsearch_bp_search['bp_search_groups_public']  = ( isset( $bpsearch_bp_search['bp_search_groups_public'] ) ) ? $bpsearch_bp_search['bp_search_groups_public'] : '';
$bpsearch_bp_search['bp_search_groups']         = ( isset( $bpsearch_bp_search['bp_search_groups'] ) ) ? $bpsearch_bp_search['bp_search_groups'] : '';
$bpsearch_bp_search['bp_search_activity']       = ( isset( $bpsearch_bp_search['bp_search_activity'] ) ) ? $bpsearch_bp_search['bp_search_activity'] : '';
?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'Search the following BuddyPress components.', 'buddypress-search' ); ?></h3>
	</div>	
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpsearch_bp_search_section' );
		do_settings_sections( 'bpsearch_bp_search_section' );
		?>
		<div class="container wbcom-search-components-section-wrap">
			<div class="form-table buddypress-components-table">
				<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Enable Search Components', 'buddypress-search' ); ?></label>
				</div>
				<div class="bp-search-parent-field">
						<label for="bp_search_activity"><?php esc_html_e( 'Search in buddypress activities?', 'buddypress-search' ); ?></label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_activity]" value="1" id="bp_search_activity" <?php checked( @$bpsearch_bp_search['bp_search_activity'], 1 ); ?>/>						
				</div>
				<div class="bp-search-parent-field">
						<label for="bp_search_groups"><?php esc_html_e( 'Search in buddypress groups?', 'buddypress-search' ); ?></label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_groups]" value="1" id="bp_search_groups" <?php checked( @$bpsearch_bp_search['bp_search_groups'], 1 ); ?>/>						
				</div>
				<div class="bp-search-parent-field">
						<label for="bp_search_groups_public"><?php esc_html_e( 'Search in public groups?', 'buddypress-search' ); ?>	</label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_groups_public]" value="1" id="bp_search_groups_public" <?php checked( @$bpsearch_bp_search['bp_search_groups_public'], 1 ); ?>/>						
				</div>
				<div class="bp-search-parent-field">
						<label for="bp_search_groups_private"><?php esc_html_e( 'Search in private groups?', 'buddypress-search' ); ?></label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_groups_private]" value="1" id="bp_search_groups_private" <?php checked( @$bpsearch_bp_search['bp_search_groups_private'], 1 ); ?>/>						
				</div>
				<div class="bp-search-parent-field">
						<label for="bp_search_groups_hidden"><?php esc_html_e( 'Search in hidden groups?', 'buddypress-search' ); ?></label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_groups_hidden]" value="1" id="bp_search_groups_hidden" <?php checked( @$bpsearch_bp_search['bp_search_groups_hidden'], 1 ); ?>/>						
				</div>
				<div class="bp-search-parent-field">
						<label for='bp_search_members'><?php esc_html_e( 'Members', 'buddypress-search' ); ?></label>
						<input type="checkbox" name="bpsearch_bp_search[bp_search_members]" value="1" id="bp_search_members" <?php checked( @$bpsearch_bp_search['bp_search_members'], 1 ); ?>/>						
				</div>
				</div>

				<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'WordPress User Fields', 'buddypress-search' ); ?></label>
				</div>

				<div class="wbcom-settings-section-options">
				<?php
				foreach ( $user_fields as $field_key => $field_label ) :
					$bpsearch_bp_search['bp_search_member'][ $field_key ] = ( isset( $bpsearch_bp_search['bp_search_member'][ $field_key ] ) ) ? $bpsearch_bp_search['bp_search_member'][ $field_key ] : 0;
					?>
					<div class="bp-search-child-field">	
							<label for='bp_search_members_<?php echo esc_attr( $field_key ); ?>'><?php echo esc_html( $field_label ); ?></label>
							<input type="checkbox" name="bpsearch_bp_search[bp_search_member][<?php echo esc_attr( $field_key ); ?>]" value="1" id="bp_search_members_<?php echo esc_attr( $field_key ); ?>" <?php checked( $bpsearch_bp_search['bp_search_member'][ $field_key ], 1 ); ?>/>						
					</div>
				<?php endforeach; ?>
				</div>
				</div>

				<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Xprofile Fields', 'buddypress-search' ); ?></label>
				</div>
				<div class="wbcom-settings-section-options">
				<?php
				if ( ! empty( $groups ) ) :
					foreach ( $groups as $group ) :
						if ( ! empty( $group->fields ) ) :
							?>
							<div class="bp-search-child-field wbcom-options-components-heading">	
								<label><strong><?php echo esc_html_e( $group->name ); ?></strong></label>
							</div>
							<?php
							foreach ( $group->fields as $field ) :
								$bpsearch_bp_search['bp_search_member'][ $field->id ] = ( isset( $bpsearch_bp_search['bp_search_member'][ $field->id ] ) ) ? $bpsearch_bp_search['bp_search_member'][ $field->id ] : 0;
								?>
							<div class="bp-search-child-field">
									<label for='bp_search_members_<?php echo esc_attr( $field->id ); ?>'><?php echo esc_html_e( $field->name ); ?></label>
									<input type="checkbox" name="bpsearch_bp_search[bp_search_member][<?php echo esc_attr( $field->id ); ?>]" value="1" id="bp_search_members_<?php echo esc_attr( $field->id ); ?>" <?php checked( $bpsearch_bp_search['bp_search_member'][ $field->id ], 1 ); ?>/>								
							</div>						
								<?php
							endforeach;

							endif;
					endforeach;
				endif;
				?>
				</div>
			</div>
		<?php submit_button(); ?>		
		</div>
	</form>
</div>
</div>
