<?php
/**
 * Products listing.
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/templates/buddypress-search/loop
 */

?>
<?php $product = wc_get_product( get_the_ID() ); ?>
<li class="bp-search-item bp-search-item_product">
	<div class="list-wrap">
		<div class="item-avatar">
			<a href="<?php the_permalink(); ?>">
				<img
					src="<?php echo get_the_post_thumbnail_url() ?: esc_url( buddypress_search_get_post_thumbnail_default( get_post_type() ) ); ?>"
					class="attachment-post-thumbnail size-post-thumbnail wp-post-image"
					alt="<?php the_title(); ?>"
				/>
			</a>
		</div>

		<div class="item">
			<span class="entry-meta">
				<?php echo esc_html( wc_get_product_category_list( get_the_ID() ) ); ?>
			</span>

			<h3 class="entry-title item-title">
				<?php /* translators: %s: Title Atttributes */ ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'buddypress-search' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h3>
			<div class="rating-custom clearfix">
				<?php wc_get_template( 'single-product/rating.php' ); ?>
			</div>

			<?php if ( $product->is_on_sale() ) : ?>
				<div class="product-sale">
					<span class="onsale"><?php esc_html_e( 'Sale!', 'buddypress-search' ); ?></span>
				</div>
			<?php endif; ?>

		</div>

		<div class="item-extra">
			<?php echo esc_html( wc_price( wc_get_price_to_display( $product ) ) . $product->get_price_suffix() ); ?>
		</div>
	</div>
</li>
