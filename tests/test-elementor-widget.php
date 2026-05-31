<?php
namespace Elementor {
    class Widget_Base {
        public $settings = [];
        public function get_settings_for_display() { return $this->settings; }
        public function start_controls_section($id, $args) {}
        public function add_control($id, $args) {}
        public function add_responsive_control($id, $args) {}
        public function end_controls_section() {}
    }
    class Controls_Manager {
        const SELECT = 'select';
        const CHOOSE = 'choose';
        const TEXT = 'text';
        const RAW_HTML = 'raw_html';
        const TAB_STYLE = 'style';
    }
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
    function apply_filters($tag, $value) {
        if ($tag === 'woocommerce_product_tabs') {
            return [
                'description' => [
                    'title' => 'Description',
                    'callback' => function(){ echo 'Standard Desc Content'; },
                    'priority' => 10
                ],
                'wcpt_tab_201' => [
                    'title' => 'Custom Tab',
                    'content' => 'Custom Content',
                    'styles' => ['font_size' => '25px'],
                    'priority' => 20
                ]
            ];
        }
        return $value;
    }
    function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function selected($a, $b) { echo $a === $b ? ' selected' : ''; }
    function admin_url($p) { return $p; }

    function get_post_meta($id, $key, $single) {
        global $post_meta;
        return isset($post_meta[$id][$key]) ? $post_meta[$id][$key] : '';
    }

    \Elementor\Plugin::$instance = new \Elementor\Plugin();

    class MockProduct {
        public $id;
        public function __construct($id) { $this->id = $id; }
        public function get_id() { return $this->id; }
    }

    function wcpt_render_tab_content( $key, $tab ) {
        echo '<div class="wcpt-tab-content-wrapper">';
        echo apply_filters( 'the_content', isset($tab['content']) ? $tab['content'] : '' );
        echo '</div>';
    }

    require_once 'woocommerce-custom-product-tabs/elementor-widget.php';

    global $product;
    $product = new MockProduct(1);

    $widget = new WCPT_Elementor_Widget();

    // Test Case 1: Tabs Layout with Renaming
    echo "Testing Elementor Widget: Tabs Layout with Renaming...\n";
    $widget->settings = [
        'display_layout' => 'tabs',
        'title_description' => 'Info Edited'
    ];
    ob_start();
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);
    $method->invoke($widget);
    $tabs_output = ob_get_clean();

    if (strpos($tabs_output, 'woocommerce-tabs') !== false && strpos($tabs_output, 'Info Edited') !== false && strpos($tabs_output, 'Standard Desc Content') !== false) {
        echo "Tabs Layout Test Passed!\n";
    } else {
        echo "Tabs Layout Test Failed!\n";
        echo "Output was: " . $tabs_output . "\n";
        exit(1);
    }

    // Test Case 2: Fields Layout
    echo "Testing Elementor Widget: Fields Layout...\n";
    $widget->settings = ['display_layout' => 'fields'];
    ob_start();
    $method->invoke($widget);
    $fields_output = ob_get_clean();
    if (strpos($fields_output, 'wcpt-stacked-field') !== false && strpos($fields_output, 'Custom Content') !== false) {
        echo "Fields Layout Test Passed!\n";
    } else {
        echo "Fields Layout Test Failed!\n";
        exit(1);
    }

    echo "All Elementor Widget tests passed!\n";
}
