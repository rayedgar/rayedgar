<?php
/**
 * Plugin Name: Elementor XR Widget
 * Description: Adds a custom 'XR' tool to Elementor with text, typography, and color options.
 * Version: 1.0
 * Author: Jules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register XR Widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function register_xr_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/xr-widget.php' );

	$widgets_manager->register( new \XR_Widget() );

}
add_action( 'elementor/widgets/register', 'register_xr_widget' );
