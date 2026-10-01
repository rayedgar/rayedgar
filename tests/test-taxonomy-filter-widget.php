<?php

// Mocking WordPress & Elementor classes and functions for testing
define('ABSPATH', true);

function esc_html__($text, $domain = 'default') {
    return $text;
}

function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function sanitize_text_field($str) {
    return trim(strip_tags($str));
}

function wp_unslash($val) {
    return $val;
}

function is_wp_error($thing) {
    return false;
}

function get_taxonomies($args = array(), $output = 'names') {
    $taxonomies = array(
        'category' => (object) array('name' => 'category', 'label' => 'Categories'),
        'post_tag' => (object) array('name' => 'post_tag', 'label' => 'Tags'),
        'product_cat' => (object) array('name' => 'product_cat', 'label' => 'Product Categories'),
    );
    return $taxonomies;
}

$mock_terms = array(
    'category:electronics' => (object) array('term_id' => 10, 'name' => 'Electronics', 'slug' => 'electronics'),
    'category:clothing' => (object) array('term_id' => 11, 'name' => 'Apparel & Clothing', 'slug' => 'clothing'),
    'product_cat:shoes' => (object) array('term_id' => 20, 'name' => 'Running Shoes', 'slug' => 'shoes'),
);

function get_term_by($field, $value, $taxonomy = '') {
    global $mock_terms;
    $key = $taxonomy . ':' . $value;
    if (isset($mock_terms[$key])) {
        return $mock_terms[$key];
    }
    // Search across terms if taxonomy not strictly specified
    foreach ($mock_terms as $term) {
        if ($term->slug === $value) {
            return $term;
        }
    }
    return false;
}

function get_term($term_id) {
    global $mock_terms;
    foreach ($mock_terms as $term) {
        if ($term->term_id == $term_id) {
            return $term;
        }
    }
    return false;
}

// Declare Elementor classes in top-level namespace or sub-namespace explicitly
if (!class_exists('Elementor\Widget_Base')) {
    eval('
    namespace Elementor {
        class Widget_Base {
            protected $settings = array();

            public function set_settings($settings) {
                $this->settings = array_merge($this->settings, $settings);
            }

            public function get_settings_for_display() {
                return $this->settings;
            }

            public function start_controls_section($id, $args = array()) {}
            public function end_controls_section() {}
            public function add_control($id, $args = array()) {}
            public function add_responsive_control($id, $args = array()) {}
            public function add_group_control($group, $args = array()) {}
        }

        class Controls_Manager {
            const TAB_CONTENT = "content";
            const TAB_STYLE = "style";
            const SELECT = "select";
            const TEXT = "text";
            const SWITCHER = "switcher";
            const CHOOSE = "choose";
            const DIMENSIONS = "dimensions";
            const COLOR = "color";
            const SLIDER = "slider";
        }

        class Group_Control_Typography {
            public static function get_type() {
                return "typography";
            }
        }

        class Utils {
            public static function validate_html_tag($tag) {
                $allowed = array("h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p");
                return in_array($tag, $allowed, true) ? $tag : "div";
            }
        }
    }
    ');
}

// Load the widget class
require_once __DIR__ . '/../elementor-taxonomy-filter-display/includes/class-taxonomy-filter-display-widget.php';

// Begin Test Assertions
echo "=== Running Active Taxonomy Filter Widget Tests ===\n";

$widget = new Elementor_Taxonomy_Filter_Display_Widget();

// Test 1: Widget Basics
echo "\nTest 1: Checking Widget Metadata...\n";
assert($widget->get_name() === 'taxonomy-filter-display', 'Widget name mismatch');
assert($widget->get_title() === 'Active Taxonomy Filter', 'Widget title mismatch');
assert($widget->get_icon() === 'eicon-filter', 'Widget icon mismatch');
echo "[PASS] Widget Metadata is correct.\n";

// Test 2: Active Filter Resolution with no GET parameters
echo "\nTest 2: Checking Fallback Default Title...\n";
$_GET = array();
$default_title = $widget->get_active_filter_title('category', 'All Categories');
assert($default_title === 'All Categories', "Expected 'All Categories', got '$default_title'");
echo "[PASS] Fallback default title resolved correctly.\n";

// Test 3: Active Filter Resolution with Elementor Loop Grid query parameter (`e-filter-...`)
echo "\nTest 3: Checking Elementor Loop Grid parameter resolution...\n";
$_GET = array(
    'e-filter-1a2b3c-category' => 'electronics',
);
$title = $widget->get_active_filter_title('category', 'All');
assert($title === 'Electronics', "Expected 'Electronics', got '$title'");
echo "[PASS] Resolved term 'Electronics' from e-filter-1a2b3c-category parameter.\n";

// Test 4: Active Filter Resolution with custom taxonomy key parameter
echo "\nTest 4: Checking Custom Taxonomy key resolution...\n";
$_GET = array(
    'product_cat' => 'shoes',
);
$title = $widget->get_active_filter_title('product_cat', 'All Products');
assert($title === 'Running Shoes', "Expected 'Running Shoes', got '$title'");
echo "[PASS] Resolved term 'Running Shoes' from product_cat parameter.\n";

// Test 5: Render HTML Output Verification
echo "\nTest 5: Checking Widget HTML Output rendering...\n";
$_GET = array(
    'category' => 'clothing',
);
$widget->set_settings(array(
    'taxonomy' => 'category',
    'show_label' => 'yes',
    'prefix_label' => 'Selected Category:',
    'default_value' => 'All',
    'html_tag' => 'h3',
));

ob_start();
// Using Reflection to call protected render method
$reflection = new ReflectionClass($widget);
$render_method = $reflection->getMethod('render');
$render_method->setAccessible(true);
$render_method->invoke($widget);
$output = ob_get_clean();

echo "Rendered HTML Output:\n" . $output . "\n";

assert(strpos($output, '<h3') !== false, 'HTML Tag <h3 missing');
assert(strpos($output, 'Selected Category:') !== false, 'Prefix label missing');
assert(strpos($output, 'Apparel & Clothing') !== false, 'Active term title missing');
echo "[PASS] Rendered HTML contains tag, label, and term title.\n";

echo "\n============================================\n";
echo "ALL TAXONOMY FILTER DISPLAY TESTS PASSED SUCCESSFULLY!\n";
echo "============================================\n";
