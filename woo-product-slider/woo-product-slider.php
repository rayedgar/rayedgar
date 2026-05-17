<?php
/**
 * Plugin Name: Woo Product Slider for Elementor
 * Description: A custom WooCommerce product slider widget for Elementor.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: woo-product-slider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Woo Product Slider Class
 */
final class Woo_Product_Slider_Plugin {

	/**
	 * Plugin Version
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP Version
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 */
	private static $_instance = null;

	/**
	 * Instance Control
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load Textdomain
	 */
	public function i18n() {
		load_plugin_textdomain( 'woo-product-slider' );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check if WooCommerce is installed and activated
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Register Widget
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

		// Register Scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		// Register Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
	}

	/**
	 * Admin notice if Elementor is not installed
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'woo-product-slider' ),
			'<strong>' . esc_html__( 'Woo Product Slider', 'woo-product-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'woo-product-slider' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice if WooCommerce is not installed
	 */
	public function admin_notice_missing_woocommerce() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'woo-product-slider' ),
			'<strong>' . esc_html__( 'Woo Product Slider', 'woo-product-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'woo-product-slider' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice if Elementor version is too low
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'woo-product-slider' ),
			'<strong>' . esc_html__( 'Woo Product Slider', 'woo-product-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'woo-product-slider' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice if PHP version is too low
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'woo-product-slider' ),
			'<strong>' . esc_html__( 'Woo Product Slider', 'woo-product-slider' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'woo-product-slider' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Init Widgets
	 */
	public function init_widgets( $widgets_manager ) {
		require_once( __DIR__ . '/widgets/class-woo-product-slider-widget.php' );
		$widgets_manager->register( new \Woo_Product_Slider_Widget() );
	}

	/**
	 * Widget Scripts
	 */
	public function widget_scripts() {
		wp_register_script( 'woo-product-slider-js', plugins_url( '/assets/js/woo-product-slider.js', __FILE__ ), [ 'jquery', 'elementor-frontend' ], self::VERSION, true );
	}

	/**
	 * Widget Styles
	 */
	public function widget_styles() {
		wp_register_style( 'woo-product-slider-css', plugins_url( '/assets/css/woo-product-slider.css', __FILE__ ), [], self::VERSION );
	}
}

Woo_Product_Slider_Plugin::instance();
