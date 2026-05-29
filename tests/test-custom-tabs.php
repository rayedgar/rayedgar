<?php

// Mocking WordPress and WooCommerce functions for testing.
define('ABSPATH', true);

$post_meta = [];
$posts = [];
$terms = [];

function add_action($tag, $callback) {}
function add_filter($tag, $callback) {}
function _x($text, $context, $domain) { return $text; }
function __($text, $domain) { return $text; }
function register_post_type($post_type, $args) {}
function wp_nonce_field($action, $name) {}
function get_post_meta($id, $key, $single) {
    global $post_meta;
    return isset($post_meta[$id][$key]) ? $post_meta[$id][$key] : '';
}
function update_post_meta($id, $key, $value) {
    global $post_meta;
    $post_meta[$id][$key] = $value;
}
function apply_filters($tag, $value) { return $value; }
function get_posts($args) {
    global $posts;
    return array_filter($posts, function($post) use ($args) {
        return $post->post_type === $args['post_type'];
    });
}
function has_term($term, $taxonomy, $post_id) {
    global $terms;
    if (!isset($terms[$post_id])) return false;
    if (is_array($term)) {
        return !empty(array_intersect($term, $terms[$post_id]));
    }
    return in_array($term, $terms[$post_id]);
}

function wp_strip_all_tags($text) {
    return strip_tags($text);
}

function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

class MockProduct {
    public $id;
    public function __construct($id) { $this->id = $id; }
    public function get_id() { return $this->id; }
}

// Include the plugin file
require_once 'woocommerce-custom-product-tabs/woocommerce-custom-product-tabs.php';

// Setup Mock Data
$posts = [
    (object) [
        'ID' => 101,
        'post_title' => 'Global Tab',
        'post_content' => 'Global Content',
        'post_type' => 'wc_product_tab'
    ],
    (object) [
        'ID' => 102,
        'post_title' => 'Category Tab',
        'post_content' => 'Category Content',
        'post_type' => 'wc_product_tab'
    ],
    (object) [
        'ID' => 103,
        'post_title' => 'Product Tab',
        'post_content' => 'Product Content',
        'post_type' => 'wc_product_tab'
    ],
];

update_post_meta(101, '_wcpt_display_rule', 'all');
update_post_meta(101, '_wcpt_priority', 10);

update_post_meta(102, '_wcpt_display_rule', 'categories');
update_post_meta(102, '_wcpt_categories', [5]);
update_post_meta(102, '_wcpt_priority', 20);

update_post_meta(103, '_wcpt_display_rule', 'products');
update_post_meta(103, '_wcpt_products', [1]);
update_post_meta(103, '_wcpt_priority', 30);

// New Stacked Field Tab
$posts[] = (object) [
    'ID' => 104,
    'post_title' => 'Stacked Field Tab',
    'post_content' => 'Stacked Content',
    'post_type' => 'wc_product_tab'
];
update_post_meta(104, '_wcpt_display_as', 'field');
update_post_meta(104, '_wcpt_display_rule', 'all');
update_post_meta(104, '_wcpt_priority', 5);
update_post_meta(104, '_wcpt_font_size', '20px');
update_post_meta(104, '_wcpt_margin', 15);

// Set styling for Global Tab
update_post_meta(101, '_wcpt_line_height', '1.8');
update_post_meta(101, '_wcpt_border_width', 2);
update_post_meta(101, '_wcpt_border_color', '#ff0000');

// Test Case 1: Product 1 (Category 5)
echo "Testing Product 1 (Category 5)...\n";
global $product, $terms;
$product = new MockProduct(1);
$terms[1] = [5];

$tabs = wcpt_product_tabs([]);
print_r(array_keys($tabs));

if (isset($tabs['wcpt_tab_101']) && isset($tabs['wcpt_tab_102']) && isset($tabs['wcpt_tab_103'])) {
    echo "Test Case 1 Passed!\n";
} else {
    echo "Test Case 1 Failed!\n";
    exit(1);
}

// Test Case 2: Product 2 (Category 6)
echo "Testing Product 2 (Category 6)...\n";
$product = new MockProduct(2);
$terms[2] = [6];

$tabs = wcpt_product_tabs([]);
print_r(array_keys($tabs));

if (isset($tabs['wcpt_tab_101']) && !isset($tabs['wcpt_tab_102']) && !isset($tabs['wcpt_tab_103'])) {
    echo "Test Case 2 Passed!\n";
} else {
    echo "Test Case 2 Failed!\n";
    exit(1);
}

// Test Case 2b: Verify Stacked Field excluded from Tabs
echo "Testing Stacked Field exclusion from tabs...\n";
if (!isset($tabs['wcpt_tab_104'])) {
    echo "Exclusion Test Passed!\n";
} else {
    echo "Exclusion Test Failed!\n";
    exit(1);
}

// Test Case 3: Verify Styles
echo "Testing Styles on Global Tab...\n";
$global_tab = $tabs['wcpt_tab_101'];
ob_start();
wcpt_render_tab_content('wcpt_tab_101', $global_tab);
$output = ob_get_clean();

echo "Output: $output\n";
if (strpos($output, 'line-height: 1.8;') !== false && strpos($output, 'border-left: 2px solid #ff0000;') !== false) {
    echo "Style Test Passed!\n";
} else {
    echo "Style Test Failed!\n";
    exit(1);
}

// Test Case 4: Verify Attribute Link Removal
echo "Testing Attribute Link Removal...\n";
$attributes = [
    'color' => [
        'value' => '<a href="http://example.com">Red</a>'
    ]
];
$stripped = wcpt_remove_attribute_links($attributes, $product);
echo "Stripped Value: " . $stripped['color']['value'] . "\n";
if ($stripped['color']['value'] === 'Red') {
    echo "Attribute Test Passed!\n";
} else {
    echo "Attribute Test Failed!\n";
    exit(1);
}

// Test Case 5: Verify Stacked Field Rendering
echo "Testing Stacked Field Rendering...\n";
ob_start();
wcpt_render_stacked_fields();
$stacked_output = ob_get_clean();
echo "Stacked Output: $stacked_output\n";

if (strpos($stacked_output, 'Stacked Content') !== false &&
    strpos($stacked_output, 'font-size: 20px;') !== false &&
    strpos($stacked_output, 'margin: 15px 0;') !== false) {
    echo "Stacked Rendering Test Passed!\n";
} else {
    echo "Stacked Rendering Test Failed!\n";
    exit(1);
}

echo "All tests passed successfully!\n";
