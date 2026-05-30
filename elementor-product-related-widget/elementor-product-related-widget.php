<?php
/**
 * Plugin Name: Elementor Product Related Widget
 * Description: Custom Elementor widget to display related products.
 * Version: 1.0.1
 * Author: Jules
 * Text Domain: elementor-product-related-widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Elementor_Product_Related_Widget {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ], 20 );
	}

	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce' ] );
			return;
		}

		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function admin_notice_missing_elementor() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-product-related-widget' ),
			'<strong>' . esc_html__( 'Elementor Product Related Widget', 'elementor-product-related-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-product-related-widget' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_missing_woocommerce() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: WooCommerce */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-product-related-widget' ),
			'<strong>' . esc_html__( 'Elementor Product Related Widget', 'elementor-product-related-widget' ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'elementor-product-related-widget' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function register_widgets( $widgets_manager ) {
		require_once( __DIR__ . '/widgets/product-related-widget.php' );
		$widgets_manager->register( new \Product_Related_Widget() );
	}
}

Elementor_Product_Related_Widget::instance();
