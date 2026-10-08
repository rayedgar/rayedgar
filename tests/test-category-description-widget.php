<?php

// Mock WordPress environment
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', true );
}

// Mock WordPress Translation & Utility Functions
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_js' ) ) {
	function esc_js( $text ) {
		return $text;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $text ) {
		return trim( $text );
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return false;
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $content ) {
		return $content;
	}
}

if ( ! function_exists( 'wpautop' ) ) {
	function wpautop( $pee, $br = true ) {
		return '<p>' . trim( $pee ) . '</p>';
	}
}

$mock_actions = array();
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $tag, $callback ) {
		global $mock_actions;
		$mock_actions[ $tag ][] = $callback;
	}
}

if ( ! function_exists( 'did_action' ) ) {
	function did_action( $tag ) {
		return 1;
	}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return dirname( $file ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file ) {
		return 'http://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
	}
}

// Mock WordPress Taxonomy Functions & Terms
$mock_terms = array(
	10 => (object) array(
		'term_id'     => 10,
		'name'        => 'Apparel',
		'slug'        => 'apparel',
		'taxonomy'    => 'category',
		'description' => 'A great selection of clothing and apparel items.',
	),
	20 => (object) array(
		'term_id'     => 20,
		'name'        => 'Shoes',
		'slug'        => 'shoes',
		'taxonomy'    => 'product_cat',
		'description' => 'High quality shoes and sneakers.',
	),
	30 => (object) array(
		'term_id'     => 30,
		'name'        => 'Empty Category',
		'slug'        => 'empty-cat',
		'taxonomy'    => 'category',
		'description' => '',
	),
);

$mock_queried_object = null;

if ( ! function_exists( 'get_taxonomies' ) ) {
	function get_taxonomies( $args = array(), $output = 'names' ) {
		return array(
			'category'    => (object) array( 'name' => 'category', 'label' => 'Categories' ),
			'product_cat' => (object) array( 'name' => 'product_cat', 'label' => 'Product Categories' ),
		);
	}
}

if ( ! function_exists( 'get_terms' ) ) {
	function get_terms( $args = array() ) {
		global $mock_terms;
		return array_values( $mock_terms );
	}
}

if ( ! function_exists( 'get_term' ) ) {
	function get_term( $term_id, $taxonomy = '' ) {
		global $mock_terms;
		return isset( $mock_terms[ $term_id ] ) ? $mock_terms[ $term_id ] : null;
	}
}

if ( ! function_exists( 'get_term_by' ) ) {
	function get_term_by( $field, $value, $taxonomy = '' ) {
		global $mock_terms;
		foreach ( $mock_terms as $term ) {
			if ( isset( $term->$field ) && $term->$field === $value ) {
				return $term;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'term_description' ) ) {
	function term_description( $term_id ) {
		$term = get_term( $term_id );
		return $term ? $term->description : '';
	}
}

if ( ! function_exists( 'get_queried_object' ) ) {
	function get_queried_object() {
		global $mock_queried_object;
		return $mock_queried_object;
	}
}

// Mock Elementor namespace classes
if ( ! class_exists( 'Elementor_Controls_Manager' ) ) {
	class Elementor_Controls_Manager {
		const TAB_CONTENT = 'content';
		const TAB_STYLE   = 'style';
		const SELECT      = 'select';
		const TEXTAREA    = 'textarea';
		const SWITCHER    = 'switcher';
		const CHOOSE      = 'choose';
		const COLOR       = 'color';
		const DIMENSIONS  = 'dimensions';
	}
}

if ( ! class_exists( 'Elementor_Group_Control_Typography' ) ) {
	class Elementor_Group_Control_Typography {
		public static function get_type() {
			return 'typography';
		}
	}
}

if ( ! class_exists( 'Elementor_Editor' ) ) {
	class Elementor_Editor {
		public function is_edit_mode() {
			return false;
		}
	}
}

if ( ! class_exists( 'Elementor_Plugin' ) ) {
	class Elementor_Plugin {
		public static $instance;
		public $editor;

		public function __construct() {
			$this->editor = new Elementor_Editor();
		}
	}

	Elementor_Plugin::$instance = new Elementor_Plugin();
}

if ( ! class_exists( 'Elementor_Widget_Base' ) ) {
	abstract class Elementor_Widget_Base {
		abstract public function get_name();
		abstract public function get_title();
		abstract public function get_icon();
		abstract public function get_categories();

		protected $controls = array();
		protected $settings = array();

		public function get_id() {
			return 'test_widget_id';
		}

		protected function register_controls() {}
		protected function render() {}
		protected function content_template() {}

		public function add_control( $id, array $args = array() ) {
			$this->controls[ $id ] = $args;
		}

		public function add_responsive_control( $id, array $args = array() ) {
			$this->controls[ $id ] = $args;
		}

		public function add_group_control( $type, array $args = array() ) {
			$this->controls[ $type ] = $args;
		}

		public function start_controls_section( $id, array $args = array() ) {}
		public function end_controls_section() {}

		public function set_settings( $settings ) {
			$this->settings = $settings;
		}

		public function get_settings_for_display( $setting = null ) {
			return $this->settings;
		}

		public function get_registered_controls() {
			return $this->controls;
		}

		public function test_register_controls() {
			$this->register_controls();
		}
	}
}

// Map class aliases to expected Elementor namespace classes
if ( ! class_exists( 'Elementor\Controls_Manager', false ) ) {
	class_alias( 'Elementor_Controls_Manager', 'Elementor\Controls_Manager' );
}
if ( ! class_exists( 'Elementor\Group_Control_Typography', false ) ) {
	class_alias( 'Elementor_Group_Control_Typography', 'Elementor\Group_Control_Typography' );
}
if ( ! class_exists( 'Elementor\Plugin', false ) ) {
	class_alias( 'Elementor_Plugin', 'Elementor\Plugin' );
}
if ( ! class_exists( 'Elementor\Widget_Base', false ) ) {
	class_alias( 'Elementor_Widget_Base', 'Elementor\Widget_Base' );
}

// Include main plugin & widget files
require_once __DIR__ . '/../elementor-category-description/elementor-category-description.php';
require_once __DIR__ . '/../elementor-category-description/widgets/class-category-description-widget.php';

echo "Running Elementor Category Description Widget Tests...\n\n";

$widget = new Elementor_Category_Description_Widget();

// Test 1: Widget Meta
echo "Test 1: Checking Widget Metadata...\n";
if ( 'category_description' === $widget->get_name() && 'Category Description' === $widget->get_title() ) {
	echo "✓ Test 1 Passed: Widget Name & Title correct.\n\n";
} else {
	echo "✗ Test 1 Failed: Incorrect Widget Metadata.\n";
	exit( 1 );
}

// Test 2: Register Controls
echo "Test 2: Registering Controls...\n";
$widget->test_register_controls();
$controls = $widget->get_registered_controls();

if ( isset( $controls['source'], $controls['taxonomy'], $controls['term_id'], $controls['html_tag'], $controls['fallback_text'] ) ) {
	echo "✓ Test 2 Passed: Content and Style Controls registered successfully.\n\n";
} else {
	echo "✗ Test 2 Failed: Missing expected controls.\n";
	exit( 1 );
}

// Test 3: Custom Term Description Fetching (Source = Custom)
echo "Test 3: Fetching custom term description (Apparel - ID 10)...\n";
$settings_custom = array(
	'source'         => 'custom',
	'taxonomy'       => 'category',
	'term_id'        => '10',
	'enable_wpautop' => 'yes',
	'html_tag'       => 'div',
);
$desc = $widget->get_description_text( $settings_custom );
if ( strpos( $desc, 'A great selection of clothing and apparel items.' ) !== false ) {
	echo "✓ Test 3 Passed: Successfully fetched custom term description.\n\n";
} else {
	echo "✗ Test 3 Failed: Could not fetch custom term description.\n";
	exit( 1 );
}

// Test 4: Current Queried Object Description
echo "Test 4: Fetching description from current queried object (Shoes - ID 20)...\n";
$GLOBALS['mock_queried_object'] = (object) array(
	'term_id'     => 20,
	'taxonomy'    => 'product_cat',
	'description' => 'High quality shoes and sneakers.',
);
$settings_current = array(
	'source'         => 'current',
	'enable_wpautop' => 'no',
	'html_tag'       => 'p',
);
$desc_current = $widget->get_description_text( $settings_current );
if ( 'High quality shoes and sneakers.' === $desc_current ) {
	echo "✓ Test 4 Passed: Successfully fetched queried object term description.\n\n";
} else {
	echo "✗ Test 4 Failed: Could not fetch queried term description.\n";
	exit( 1 );
}

// Test 5: Fallback Text Handling when Category Description is Empty
echo "Test 5: Fallback text when category description is empty...\n";
$settings_fallback = array(
	'source'         => 'custom',
	'term_id'        => '30', // Empty description term
	'fallback_text'  => 'Default description fallback content.',
	'enable_wpautop' => 'no',
);
$desc_fallback = $widget->get_description_text( $settings_fallback );
if ( 'Default description fallback content.' === $desc_fallback ) {
	echo "✓ Test 5 Passed: Fallback text returned when term description is empty.\n\n";
} else {
	echo "✗ Test 5 Failed: Fallback text not applied.\n";
	exit( 1 );
}

// Test 6: Elementor Taxonomy Filter query parameter parsing
echo "Test 6: Elementor Taxonomy Filter query parameter parsing...\n";
$_GET['e-filter-12345-category'] = 'apparel';
$settings_tax_filter = array(
	'source'         => 'taxonomy_filter',
	'taxonomy'       => 'category',
	'enable_wpautop' => 'no',
);
$desc_filter = $widget->get_description_text( $settings_tax_filter );
if ( 'A great selection of clothing and apparel items.' === $desc_filter ) {
	echo "✓ Test 6 Passed: Taxonomy filter URL query parameter parsed and rendered successfully.\n\n";
} else {
	echo "✗ Test 6 Failed: Failed to parse taxonomy filter parameter.\n";
	exit( 1 );
}

// Test 7: Loop Grid / Product Category Sync
echo "Test 7: Loop Grid product_cat query parameter parsing...\n";
unset($_GET['e-filter-12345-category']);
$_GET['product_cat'] = 'shoes';
$settings_loop_grid = array(
	'source'         => 'loop_grid',
	'taxonomy'       => 'product_cat',
	'enable_wpautop' => 'no',
);
$desc_loop_grid = $widget->get_description_text( $settings_loop_grid );
if ( 'High quality shoes and sneakers.' === $desc_loop_grid ) {
	echo "✓ Test 7 Passed: Loop Grid product_cat URL parameter parsed and rendered successfully.\n\n";
} else {
	echo "✗ Test 7 Failed: Failed to parse Loop Grid product_cat parameter.\n";
	exit( 1 );
}

// Test 8: HTML Output Rendering with JS Listener
echo "Test 8: Testing HTML Output Rendering with JS Listener...\n";
$widget->set_settings( $settings_custom );
ob_start();
$render_method = new ReflectionMethod( $widget, 'render' );
$render_method->setAccessible( true );
$render_method->invoke( $widget );
$output = ob_get_clean();

if ( strpos( $output, 'id="elementor-category-description-test_widget_id"' ) !== false && strpos( $output, 'update(' ) !== false && strpos( $output, 'product-category' ) !== false ) {
	echo "✓ Test 8 Passed: Widget rendered valid HTML structure with JS filter script.\n\n";
} else {
	echo "✗ Test 8 Failed: Rendered HTML/JS output incorrect.\n";
	echo "Output was: $output\n";
	exit( 1 );
}

echo "All Category Description Widget tests completed successfully!\n";
