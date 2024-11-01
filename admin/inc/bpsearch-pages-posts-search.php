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
$bpsearch_pages_posts_search = get_option( 'bpsearch_pages_posts_search' );
if ( $bpsearch_pages_posts_search == '' ) {
	$bpsearch_pages_posts_search = array();
}
$exclude_post_type = buddypress_search_exclude_post_type();
$post_types        = get_post_types(
	array(
		'public'              => true,
		'exclude_from_search' => false,
	)
);
?>
<div class="wbcom-tab-content">
<div class="wbcom-admin-title-section">
<h3><?php esc_html_e( 'Search the following WordPress content and custom post types.', 'buddypress-search' ); ?></h3>
</div>
<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bpsearch_pages_posts_search_section' );
		do_settings_sections( 'bpsearch_pages_posts_search_section' );
		?>
	<div class="container wbcom-search-components-section-wrap">
		<div class="form-table pages-posts-search-table">
			<div class="wbcom-settings-section-wrap wbcom-settings-section-options">
				<?php
				foreach ( $post_types as $post_type ) :

					if ( in_array( $post_type, $exclude_post_type ) ) {
						continue;
					}

					$post_type_obj                                     = get_post_type_object( $post_type );
					$bpsearch_pages_posts_search[ 'bp_' . $post_type ] = ( isset( $bpsearch_pages_posts_search[ 'bp_' . $post_type ] ) ) ? $bpsearch_pages_posts_search[ 'bp_' . $post_type ] : '';

					?>
										
						<div class="wbcom-settings-section-options-heading wbcom-pages-posts-search-heading">
							<div class="post-type-title">
								<label><?php echo esc_html_e( $post_type ); ?></label>
							</div>
						</div>
						<div class="bp-search-parent-field">
							<label for='bp_search_pages_posts_<?php echo esc_attr( $post_type ); ?>'><?php echo esc_html( $post_type_obj->labels->name ); ?></label>
							<input type="checkbox" name="bpsearch_pages_posts_search[<?php echo esc_attr( 'bp_' . $post_type ); ?>]" value="1" id="bp_search_pages_posts_<?php echo esc_attr( $post_type ); ?>" <?php checked( $bpsearch_pages_posts_search[ 'bp_' . $post_type ], 1 ); ?>/>
						</div>

					<?php
					$taxonomies = (array) apply_filters( 'bp_search_settings_post_type_taxonomies', get_object_taxonomies( $post_type ), $post_type );

					foreach ( $taxonomies as $taxonomy ) :

						$taxonomy_obj = get_taxonomy( $taxonomy );
						if ( $taxonomy_obj->show_ui == false ) {
							continue;
						}

						$bpsearch_pages_posts_search[ $post_type ][ $taxonomy ] = ( isset( $bpsearch_pages_posts_search[ $post_type ][ $taxonomy ] ) ) ? $bpsearch_pages_posts_search[ $post_type ][ $taxonomy ] : 0;
						?>
						<div class="bp-search-parent-field">
							<label for='bp_search_pages_posts_<?php echo esc_attr( $post_type . '_' . $taxonomy ); ?>'><?php echo esc_html( $taxonomy_obj->labels->name ); ?></label>	
							<input type="checkbox" name="bpsearch_pages_posts_search[<?php echo esc_attr( $post_type ); ?>][<?php echo esc_attr( $taxonomy ); ?>]" value="1" id="bp_search_pages_posts_<?php echo esc_attr( $post_type . '_' . $taxonomy ); ?>" <?php checked( $bpsearch_pages_posts_search[ $post_type ][ $taxonomy ], 1 ); ?> />						
						</div>
						<?php
					endforeach; /* finish taxonomies*/

					if ( in_array( $post_type, array( 'post', 'page' ) ) ) :
						$bpsearch_pages_posts_search[ $post_type ]['meta_data'] = ( isset( $bpsearch_pages_posts_search[ $post_type ]['meta_data'] ) ) ? $bpsearch_pages_posts_search[ $post_type ]['meta_data'] : 0;
						?>
						<div class="bp-search-parent-field">
							<label for='bp_search_pages_posts_<?php echo esc_attr( $post_type . '_meta_data' ); ?>'><?php echo esc_html__( 'Meta Data', 'buddypress-search' ); ?></label>
							<input type="checkbox" name="bpsearch_pages_posts_search[<?php echo esc_attr( $post_type ); ?>][meta_data]" value="1" id="bp_search_pages_posts_<?php echo esc_attr( $post_type . '_meta_data' ); ?>" <?php checked( @$bpsearch_pages_posts_search[ $post_type ]['meta_data'], 1 ); ?>/>
						</div>
						
						<?php
					endif;
				endforeach;
				?>
			</div>
		</div></div>
			<?php submit_button(); ?>
		</form>
	</div>
</div>
