<?php
/**
 * Plugin Name: Elementor Active Taxonomy Filter Display
 * Description: An Elementor widget that displays the current chosen taxonomy filter setting connected to the Loop Grid filter.
 * Version: 1.1.2
 * Author: Jules
 * Text Domain: elementor-taxonomy-filter-display
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ETFD_VERSION', '1.1.2' );
define( 'ETFD_PATH', plugin_dir_path( __FILE__ ) );
define( 'ETFD_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
final class Elementor_Taxonomy_Filter_Display_Plugin {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Register Widget
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Enqueue Scripts & Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-taxonomy-filter-display' ),
			'<strong>' . esc_html__( 'Elementor Active Taxonomy Filter Display', 'elementor-taxonomy-filter-display' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-taxonomy-filter-display' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function register_widgets( $widgets_manager ) {
		require_once ETFD_PATH . 'includes/class-taxonomy-filter-display-widget.php';
		$widgets_manager->register( new \Elementor_Taxonomy_Filter_Display_Widget() );
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'elementor-taxonomy-filter-display',
			ETFD_URL . 'assets/js/taxonomy-filter-display.js',
			array( 'jquery' ),
			ETFD_VERSION,
			true
		);

		wp_localize_script(
			'elementor-taxonomy-filter-display',
			'ETFD_Data',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}
}

Elementor_Taxonomy_Filter_Display_Plugin::get_instance();
