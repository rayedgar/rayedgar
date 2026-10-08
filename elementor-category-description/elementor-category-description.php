<?php
/**
 * Plugin Name: Elementor Category Description Widget
 * Description: Displays category descriptions.
 * Version: 1.0.0
 * Author: Jules
 */
if(!defined('ABSPATH'))exit;
define('ELEMENTOR_CATEGORY_DESCRIPTION_VERSION','1.0.0');
define('ELEMENTOR_CATEGORY_DESCRIPTION_PATH',plugin_dir_path(__FILE__));

final class Elementor_Category_Description {
	private static $_instance = null;
	public static function instance() {
		if ( is_null( self::$_instance ) ) self::$_instance = new self();
		return self::$_instance;
	}
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
	public function init() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
	}
	public function admin_notice_missing_main_plugin() {
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', esc_html__( 'Requires Elementor.', 'elementor-category-description' ) );
	}
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
