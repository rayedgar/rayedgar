<?php
/**
 * Plugin Name: Elementor Category Description Widget
 * Description: Displays category and taxonomy descriptions selected via Elementor taxonomy filters or term selection.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: elementor-category-description
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ELEMENTOR_CATEGORY_DESCRIPTION_VERSION', '1.0.0' );
define( 'ELEMENTOR_CATEGORY_DESCRIPTION_PATH', plugin_dir_path( __FILE__ ) );
define( 'ELEMENTOR_CATEGORY_DESCRIPTION_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Elementor Category Description Class
 */
final class Elementor_Category_Description {

	/**
	 * Instance
	 *
	 * @var Elementor_Category_Description
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Elementor_Category_Description Instance
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
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Register Widget
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		// Legacy hook support for older Elementor versions
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
	}

	/**
	 * Admin notice for missing Elementor plugin dependency
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-category-description' ),
			'<strong>' . esc_html__( 'Elementor Category Description Widget', 'elementor-category-description' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-category-description' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Register Category Description Widget
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once ELEMENTOR_CATEGORY_DESCRIPTION_PATH . 'widgets/class-category-description-widget.php';

		$widget = new \Elementor_Category_Description_Widget();

		if ( is_object( $widgets_manager ) && method_exists( $widgets_manager, 'register' ) ) {
			$widgets_manager->register( $widget );
		} elseif ( is_object( $widgets_manager ) && method_exists( $widgets_manager, 'register_widget_type' ) ) {
			$widgets_manager->register_widget_type( $widget );
		}
	}
}

Elementor_Category_Description::instance();
