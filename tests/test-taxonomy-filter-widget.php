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

function wp_json_encode($data) {
    return json_encode($data);
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

if (!class_exists('Elementor\Widget_Base')) {
    eval('
    namespace Elementor {
        class Repeater {
            public function add_control($id, $args = array()) {}
            public function get_controls() { return array(); }
        }

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
            const REPEATER = "repeater";
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

echo "=== Running Active Taxonomy Filter Widget Tests ===\n";

$widget = new Elementor_Taxonomy_Filter_Display_Widget();

// Test 1: Widget Basics
echo "\nTest 1: Checking Widget Metadata...\n";
assert($widget->get_name() === 'taxonomy-filter-display', 'Widget name mismatch');
assert($widget->get_title() === 'Active Taxonomy Filter', 'Widget title mismatch');
assert($widget->get_icon() === 'eicon-filter', 'Widget icon mismatch');
echo "[PASS] Widget Metadata is correct.\n";

// Test 2: Active Filter Resolution with Repeater, Before/After Text & Order
echo "\nTest 2: Checking Repeater Taxonomy Ordering & Before/After Text Formatting...\n";
$_GET = array(
    'e-filter-1a2b3c-category' => 'electronics',
    'product_cat' => 'shoes',
);

$taxonomies_list = array(
    array(
        'enable' => 'yes',
        'taxonomy' => 'product_cat',
        'before_text' => 'Cat: ',
        'after_text' => ' | ',
    ),
    array(
        'enable' => 'yes',
        'taxonomy' => 'category',
        'before_text' => 'Tag: ',
        'after_text' => '',
    ),
);

$output = $widget->get_active_filter_output($taxonomies_list, 'All');
echo "Active Filter Output: '$output'\n";
assert($output === 'Cat: Running Shoes | Tag: Electronics', "Unexpected output '$output'");
echo "[PASS] Repeater ordering & before/after formatting resolved correctly.\n";

// Test 3: Disabled taxonomy filter in repeater list
echo "\nTest 3: Checking disabled taxonomy switcher...\n";
$taxonomies_list[0]['enable'] = 'no'; // Disable product_cat
$output_disabled = $widget->get_active_filter_output($taxonomies_list, 'All');
echo "Active Filter Output with disabled item: '$output_disabled'\n";
assert($output_disabled === 'Tag: Electronics', "Unexpected output '$output_disabled'");
echo "[PASS] Disabled taxonomy skipped correctly.\n";

// Test 4: Default fallback text when no parameters match
echo "\nTest 4: Checking Default Fallback...\n";
$_GET = array();
$default_output = $widget->get_active_filter_output($taxonomies_list, 'All Items');
assert($default_output === 'All Items', "Expected 'All Items', got '$default_output'");
echo "[PASS] Default fallback text returned when no filters active.\n";

echo "\n============================================\n";
echo "ALL TAXONOMY FILTER DISPLAY TESTS PASSED SUCCESSFULLY!\n";
echo "============================================\n";
