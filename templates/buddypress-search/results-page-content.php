<?php
/**
 * Result page content template.
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
$no_results_class = ! $buddypress_search_obj->has_buddypress_search_results() ? 'bp-search-no-results' : '';
?>

<div class="bp-search-page buddypress-wrap bp-dir-hori-nav">

	<div class="bp-search-results-wrapper dir-form <?php echo esc_attr( $no_results_class ); ?>">

		<nav class="search_filters item-list-tabs main-navs dir-navs bp-navs no-ajax" role="navigation">
			<ul class="component-navigation search-nav">
				<?php
				$buddypress_search_obj->print_tabs();
				?>
			</ul>
		</nav>

		<div class="bp-search-form-wrapper dir-search no-ajax">
			<?php buddypress_search_template_part( 'search-form' ); ?>
		</div>

		<div class="search_results">
			<?php do_action( 'buddypress_search_before_result' ); ?>
			<?php $buddypress_search_obj->print_results(); ?>
			<?php do_action( 'buddypress_search_after_result' ); ?>
		</div>

	</div>

</div><!-- .bp-search-page -->
