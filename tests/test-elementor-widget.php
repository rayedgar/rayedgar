<?php
namespace Elementor {
    class Widget_Base {
        public $settings = [];
        public function get_settings_for_display() { return $this->settings; }
        public function start_controls_section($id, $args) {}
        public function add_control($id, $args) {}
        public function end_controls_section() {}
    }
    class Controls_Manager { const SELECT = 'select'; }
    class Plugin {
        public static $instance;
        public $editor;
        public function __construct() { $this->editor = (object)['is_edit_mode' => function(){return false;}]; }
    }
}

namespace {
    define('ABSPATH', true);

    $post_meta = [];
    $posts = [];
    $terms = [];

    function add_action($tag, $callback) {}
    function add_filter($tag, $callback) {}
    function _x($text, $context, $domain) { return $text; }
    function __($text, $domain) { return $text; }
    function apply_filters($tag, $value) { return $value; }
    function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function selected($a, $b) { echo $a === $b ? ' selected' : ''; }

    function get_post_meta($id, $key, $single) {
        global $post_meta;
        return isset($post_meta[$id][$key]) ? $post_meta[$id][$key] : '';
    }
    function update_post_meta($id, $key, $value) {
        global $post_meta;
        $post_meta[$id][$key] = $value;
    }
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

    \Elementor\Plugin::$instance = new \Elementor\Plugin();

    class MockProduct {
        public $id;
        public function __construct($id) { $this->id = $id; }
        public function get_id() { return $this->id; }
    }

    // Include rendering functions from main file
    function wcpt_render_tab_content( $key, $tab ) {
        $style_attr = '';
        if ( ! empty( $tab['styles'] ) ) {
            $styles = $tab['styles'];
            $css    = array();
            if ( ! empty( $styles['font_size'] ) ) {
                $css[] = 'font-size: ' . esc_attr( $styles['font_size'] ) . ';';
            }
            if ( ! empty( $css ) ) {
                $style_attr = ' style="' . implode( ' ', $css ) . '"';
            }
        }
        echo '<div class="wcpt-tab-content-wrapper"' . $style_attr . '>';
        echo apply_filters( 'the_content', $tab['content'] );
        echo '</div>';
    }

    require_once 'woocommerce-custom-product-tabs/elementor-widget.php';

    // Setup Mock Data
    $posts = [
        (object) [
            'ID' => 201,
            'post_title' => 'Elementor Tab',
            'post_content' => 'Elementor Content',
            'post_type' => 'wc_product_tab'
        ],
    ];
    update_post_meta(201, '_wcpt_display_rule', 'all');
    update_post_meta(201, '_wcpt_priority', 10);
    update_post_meta(201, '_wcpt_font_size', '25px');

    global $product;
    $product = new MockProduct(1);

    $widget = new WCPT_Elementor_Widget();

    // Test Case 1: Tabs Layout
    echo "Testing Elementor Widget: Tabs Layout...\n";
    $widget->settings = ['display_layout' => 'tabs'];
    ob_start();
    // Use reflection to call protected render()
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);
    $method->invoke($widget);
    $tabs_output = ob_get_clean();
    echo "Tabs Output Snippet: " . substr($tabs_output, 0, 100) . "...\n";
    if (strpos($tabs_output, 'woocommerce-tabs') !== false && strpos($tabs_output, 'Elementor Tab') !== false) {
        echo "Tabs Layout Test Passed!\n";
    } else {
        echo "Tabs Layout Test Failed!\n";
        exit(1);
    }

    // Test Case 2: Fields Layout
    echo "Testing Elementor Widget: Fields Layout...\n";
    $widget->settings = ['display_layout' => 'fields'];
    ob_start();
    $method->invoke($widget);
    $fields_output = ob_get_clean();
    echo "Fields Output: $fields_output\n";
    if (strpos($fields_output, 'wcpt-stacked-field') !== false && strpos($fields_output, 'font-size: 25px;') !== false) {
        echo "Fields Layout Test Passed!\n";
    } else {
        echo "Fields Layout Test Failed!\n";
        exit(1);
    }

    echo "All Elementor Widget tests passed!\n";
}
