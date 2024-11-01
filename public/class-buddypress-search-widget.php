<?php
/**
 * BuddyPress Search Widget
 *
 * @package Buddypress_Search
 * @subpackage Buddypress_Search/public/
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Poll Activity Graph Widget.
 *
 * @since 1.0.0
 */
class BuddyPress_Search_Widget extends WP_Widget {

	/**
	 * Working as a poll activity, we get things done better.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'BuddyPress Search widget', 'buddypress-search' ),
			'classname'                   => 'widget_buddypress_search_widget buddypress_search_widget widget',
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, _x( '(BuddyPress) Search', 'widget name', 'buddypress-search' ), $widget_ops );
	}

	/**
	 * Extends our front-end output method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Array of arguments for the widget.
	 * @param array $instance Widget instance data.
	 */
	public function widget( $args, $instance ) {
		global $wpdb, $current_user;

		extract( $args );
		if ( empty( $instance['title'] ) ) {
			$instance['title'] = __( 'Search', 'buddypress-search' );
		}

		/**
		 * Filters the title of the Poll graph widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo wp_kses_post( $before_widget );

		echo wp_kses_post( $before_title ) . esc_html( $title ) . wp_kses_post( $after_title );

		buddypress_search_template_part( 'search-form' );

		echo wp_kses_post( $after_widget );

	}

	/**
	 * Extends our update method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New instance data.
	 * @param array $old_instance Original instance data.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Extends our form method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current instance.
	 * @return mixed
	 */
	public function form( $instance ) {

		$defaults = array(
			'title' => __( 'Search', 'buddypress-search' ),
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = wp_strip_all_tags( $instance['title'] );
		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'buddypress-search' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>
		<?php

	}
}


add_action(
	'widgets_init',
	function() {
			register_widget( 'BuddyPress_Search_Widget' );
	}
);
