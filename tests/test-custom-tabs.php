<?php
/**
 * Unit tests for ProductTabs Plus plugin.
 */

namespace Elementor {
	class Widget_Base {
		protected function start_controls_section( $id, $args ) {}
		protected function end_controls_section() {}
		protected function add_control( $id, $args ) {}
		protected function add_responsive_control( $id, $args ) {}
		protected function start_controls_tabs( $id ) {}
		protected function end_controls_tabs() {}
		protected function start_controls_tab( $id, $args ) {}
		protected function end_controls_tab() {}
		public function get_settings_for_display() {
			return array(
				'title_description'            => 'Custom Info',
				'title_additional_information' => 'Specs',
				'title_reviews'                => 'Customer Feedback',
				'display_layout'               => 'tabs',
			);
		}
	}
	class Controls_Manager {
		const TAB_CONTENT = 'content';
		const TAB_STYLE   = 'style';
		const TEXT        = 'text';
		const SELECT      = 'select';
		const SLIDER      = 'slider';
		const DIMENSIONS  = 'dimensions';
		const COLOR       = 'color';
		const RAW_HTML    = 'raw_html';
	}
}

namespace {

	define( 'ABSPATH', true );

	// Mock WordPress globals and helper functions
	$post_meta_db = array();
	$posts_db = array();
	$options_db = array();

	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
	function did_action( $hook ) { return 0; }
	function register_post_type( $post_type, $args ) {}
	function add_meta_box() {}
	function wp_nonce_field() {}
	function esc_attr( $str ) { return htmlspecialchars( (string) $str, ENT_QUOTES ); }
	function esc_html( $str ) { return htmlspecialchars( (string) $str, ENT_QUOTES ); }
	function esc_html__( $text, $domain = 'default' ) { return esc_html( $text ); }
	function esc_url( $str ) { return $str; }
	function esc_html_e( $text, $domain = 'default' ) { echo esc_html( $text ); }
	function __( $text, $domain = 'default' ) { return $text; }
	function _x( $text, $context, $domain = 'default' ) { return $text; }
	function selected( $selected, $current, $echo = true ) {
		$result = ( (string) $selected === (string) $current ) ? 'selected="selected"' : '';
		if ( $echo ) { echo $result; }
		return $result;
	}
	function sanitize_text_field( $str ) { return trim( strip_tags( (string) $str ) ); }
	function wp_strip_all_tags( $str ) { return strip_tags( (string) $str ); }
	function wp_kses_post( $str ) { return $str; }
	function apply_filters( $tag, $value ) { return $value; }
	function plugin_dir_path( $file ) { return __DIR__ . '/../producttabs_plus/'; }
	function plugin_dir_url( $file ) { return 'http://example.com/wp-content/plugins/producttabs_plus/'; }

	function get_post_meta( $post_id, $key = '', $single = false ) {
		global $post_meta_db;
		if ( isset( $post_meta_db[ $post_id ][ $key ] ) ) {
			return $post_meta_db[ $post_id ][ $key ];
		}
		return $single ? '' : array();
	}

	function update_post_meta( $post_id, $key, $value ) {
		global $post_meta_db;
		if ( ! isset( $post_meta_db[ $post_id ] ) ) {
			$post_meta_db[ $post_id ] = array();
		}
		$post_meta_db[ $post_id ][ $key ] = $value;
		return true;
	}

	function get_option( $option, $default = false ) {
		global $options_db;
		return isset( $options_db[ $option ] ) ? $options_db[ $option ] : $default;
	}

	function update_option( $option, $value ) {
		global $options_db;
		$options_db[ $option ] = $value;
		return true;
	}

	function get_posts( $args = array() ) {
		global $posts_db;
		$results = array();
		foreach ( $posts_db as $post ) {
			if ( isset( $args['post_type'] ) && $post->post_type !== $args['post_type'] ) {
				continue;
			}
			if ( isset( $args['post_status'] ) && $post->post_status !== $args['post_status'] ) {
				continue;
			}
			$results[] = $post;
		}
		return $results;
	}

	function get_post( $post_id ) {
		global $posts_db;
		return isset( $posts_db[ $post_id ] ) ? $posts_db[ $post_id ] : null;
	}

	function get_the_title( $post_id ) {
		$p = get_post( $post_id );
		return $p ? $p->post_title : '';
	}

	function has_term( $term, $taxonomy, $post_id ) {
		global $post_meta_db;
		$terms = isset( $post_meta_db[ $post_id ]['_mock_terms'][ $taxonomy ] ) ? $post_meta_db[ $post_id ]['_mock_terms'][ $taxonomy ] : array();
		return in_array( (string) $term, array_map( 'strval', $terms ), true );
	}

	function wc_get_product_id_by_sku( $sku ) {
		if ( 'SKU-APPAREL-1' === $sku ) {
			return 101;
		}
		return 0;
	}

	// Load plugin file
	require_once __DIR__ . '/../producttabs_plus/producttabs_plus.php';
	ptp_init_elementor_widget();

	echo "Running ProductTabs Plus Unit Tests...\n\n";

	// Test 1: Category and Product Targeting Matching
	echo "Test 1: Rule Matching logic...\n";

	// Setup mock product post 100
	update_post_meta( 100, '_mock_terms', array( 'product_cat' => array( 12, 'clothing' ) ) );

	// Setup custom tab post 1
	$tab_post1 = (object) array(
		'ID'          => 1,
		'post_type'   => 'ptp_custom_tab',
		'post_status' => 'publish',
		'post_title'  => 'Sizing Guide',
		'post_content'=> '<p>Size chart details</p>',
	);
	$posts_db[1] = $tab_post1;

	update_post_meta( 1, '_ptp_categories', '12, clothing' );
	update_post_meta( 1, '_ptp_priority', '15' );

	$matches = ptp_is_tab_matching_product( 1, 100 );
	if ( $matches ) {
		echo " [PASS] Category rule matched successfully.\n";
	} else {
		echo " [FAIL] Category rule match failed.\n";
		exit( 1 );
	}

	// Setup custom tab post 2 (targeted to SKU-APPAREL-1 => product 101)
	$tab_post2 = (object) array(
		'ID'          => 2,
		'post_type'   => 'ptp_custom_tab',
		'post_status' => 'publish',
		'post_title'  => 'Care Instructions',
		'post_content'=> '<p>Wash cold only.</p>',
	);
	$posts_db[2] = $tab_post2;

	update_post_meta( 2, '_ptp_products', 'SKU-APPAREL-1' );
	update_post_meta( 2, '_ptp_priority', '5' );

	$matches_sku = ptp_is_tab_matching_product( 2, 101 );
	if ( $matches_sku ) {
		echo " [PASS] Product SKU rule matched successfully.\n";
	} else {
		echo " [FAIL] Product SKU rule match failed.\n";
		exit( 1 );
	}

	// Test 2: Tab Filtering & Priority Manual PHP Sorting
	echo "Test 2: Tab injection and priority sorting...\n";

	$GLOBALS['product'] = new class {
		public function get_id() { return 101; }
	};

	$initial_tabs = array(
		'description'            => array(
			'title'    => 'Description',
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab',
		),
		'additional_information' => array(
			'title'    => 'Additional Information',
			'priority' => 20,
			'callback' => 'woocommerce_product_additional_information_tab',
		),
	);

	$result_tabs = ptp_add_custom_product_tabs( $initial_tabs );

	$tab_keys = array_keys( $result_tabs );
	if ( isset( $result_tabs['additional_information'] ) && 'Specs' === $result_tabs['additional_information']['title'] ) {
		echo " [PASS] Default additional_information tab title renamed to Specs.\n";
	} else {
		echo " [FAIL] Default additional_information tab title rename failed.\n";
		exit( 1 );
	}

	if ( count( $result_tabs ) === 3 && 'ptp_tab_2' === $tab_keys[0] && 'description' === $tab_keys[1] ) {
		echo " [PASS] Tab injected and priority sorted (Priority 5 came before Priority 10).\n";
	} else {
		echo " [FAIL] Tab injection or priority sorting failed.\n";
		var_dump( $tab_keys );
		exit( 1 );
	}

	// Test 3: Plain text product attributes tag stripping
	echo "Test 3: Plain text product attributes filter...\n";

	update_option( 'ptp_plain_text_attributes', true );
	$attributes = array(
		'material' => array(
			'name'  => 'Material',
			'value' => '<a href="http://example.com">100% Organic Cotton</a>',
		),
	);

	$filtered_attrs = ptp_filter_product_attributes( $attributes, null );
	if ( '100% Organic Cotton' === $filtered_attrs['material']['value'] ) {
		echo " [PASS] HTML tags stripped from attribute value successfully.\n";
	} else {
		echo " [FAIL] Attribute tag stripping failed.\n";
		exit( 1 );
	}

	// Test 4: Elementor Widget Render Method
	echo "Test 4: Elementor Widget render output...\n";

	ob_start();
	$widget = new \PTP_Elementor_Widget();
	$reflector = new \ReflectionClass( $widget );
	$method = $reflector->getMethod( 'render' );
	$method->setAccessible( true );
	$method->invoke( $widget );
	$output = ob_get_clean();

	if ( strpos( $output, 'Custom Info' ) !== false && strpos( $output, 'ptp-tabs-wrapper' ) !== false ) {
		echo " [PASS] Elementor Widget rendered custom title overrides and wrapper HTML successfully.\n";
	} else {
		echo " [FAIL] Elementor Widget render failed.\n";
		echo $output;
		exit( 1 );
	}

	// Test 5: Global Attribute Alignment CSS Injection
	echo "Test 5: Global Attribute Alignment CSS...\n";
	update_option( 'ptp_attribute_alignment', 'right' );
	ob_start();
	ptp_inject_global_styles();
	$styles = ob_get_clean();

	if ( strpos( $styles, 'text-align: right !important;' ) !== false && strpos( $styles, 'table.woocommerce-product-attributes' ) !== false ) {
		echo " [PASS] Global attribute right alignment CSS generated successfully.\n";
	} else {
		echo " [FAIL] Global attribute alignment CSS generation failed.\n";
		exit( 1 );
	}

	// Also run existing plugin tests to ensure no regressions
	echo "\nRunning existing plugin test runner...\n";
	passthru( 'php ' . __DIR__ . '/test-plugin.php', $exit_code );
	if ( 0 !== $exit_code ) {
		echo " [FAIL] Existing plugin test runner failed.\n";
		exit( 1 );
	}

	echo "\nAll ProductTabs Plus tests passed successfully!\n";
}
