<?php

// Mocking WordPress and WooCommerce functions for testing.
define('ABSPATH', true);

$post_meta = [];
$posts = [
    (object) ['ID' => 1, 'post_title' => 'Product A'],
    (object) ['ID' => 2, 'post_title' => 'Product B'],
];

function is_singular($type) {
    return $type === 'product';
}

function get_post_meta($id, $key, $single) {
    global $post_meta;
    return isset($post_meta[$id][$key]) ? $post_meta[$id][$key] : null;
}

function update_post_meta($id, $key, $value) {
    global $post_meta;
    $post_meta[$id][$key] = $value;
}

function add_action($tag, $callback) {}
function add_shortcode($tag, $callback) {}
function register_activation_hook($file, $callback) {}
function add_menu_page() {}

class WP_Query {
    public $posts = [];
    public $current_post = -1;
    public $post_count = 0;

    public function __construct($args) {
        global $posts, $post_meta;
        $this->posts = $posts;
        $this->post_count = count($posts);

        // Simple mock sort based on meta
        usort($this->posts, function($a, $b) use ($post_meta) {
            $vA = isset($post_meta[$a->ID]['_product_views_count']) ? $post_meta[$a->ID]['_product_views_count'] : 0;
            $vB = isset($post_meta[$b->ID]['_product_views_count']) ? $post_meta[$b->ID]['_product_views_count'] : 0;
            return $vB - $vA;
        });
    }

    public function have_posts() {
        return $this->current_post + 1 < $this->post_count;
    }

    public function the_post() {
        $this->current_post++;
        $GLOBALS['post'] = $this->posts[$this->current_post];
    }
}

function wp_reset_postdata() {}
function get_the_title() { global $post; return $post->post_title; }
function get_the_ID() { global $post; return $post->ID; }

// Include the plugin file
require_once 'woocommerce2-most-visited-tracker/woocommerce2-most-visited-tracker.php';

// Test Tracking
echo "Testing Tracking...\n";
global $post;
$post = $posts[0]; // Product A
w2mv_track_product_view();
w2mv_track_product_view();
$post = $posts[1]; // Product B
w2mv_track_product_view();

$viewsA = get_post_meta(1, '_product_views_count', true);
$viewsB = get_post_meta(2, '_product_views_count', true);

echo "Product A Views: $viewsA (Expected 2)\n";
echo "Product B Views: $viewsB (Expected 1)\n";

if ($viewsA == 2 && $viewsB == 1) {
    echo "Tracking Test Passed!\n";
} else {
    echo "Tracking Test Failed!\n";
    exit(1);
}

// Test Data Retrieval
echo "Testing Data Retrieval...\n";
$top = w2mv_get_top_products();
echo "Top Product: " . $top[0]->post_title . " with " . $top[0]->views . " views\n";

if ($top[0]->post_title === 'Product A' && $top[0]->views == 2) {
    echo "Data Retrieval Test Passed!\n";
} else {
    echo "Data Retrieval Test Failed!\n";
    exit(1);
}

echo "All tests passed successfully!\n";
