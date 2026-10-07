<?php
/**
 * Plugin Name: Advanced Related Products Elementor Widget
 * Description:
 * Version: 1.0.6
 * Author: Jules
 * Text Domain: elementor-product-related-widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class Elementor_Product_Related_Widget {

	private static $_instance = null;

	public static function instance() {
		return self::$_instance ??= new self();
	}

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ], 20 );
	}

	public function init() {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_deps' ] );
			return;
		}
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function admin_notice_missing_deps() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html__( 'Advanced Related Products Elementor Widget requires Elementor and WooCommerce to be active.', 'elementor-product-related-widget' ) );
	}

	public function register_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/product-related-widget.php';
		$widgets_manager->register( new \Product_Related_Widget() );
	}
}

Elementor_Product_Related_Widget::instance();
