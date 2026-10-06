<?php

namespace {
    // Mock ABSPATH
    define('ABSPATH', true);

    // Mock esc_html__
    if (!function_exists('esc_html__')) {
        function esc_html__($text, $domain) {
            return $text;
        }
    }

    // Mock esc_html
    if (!function_exists('esc_html')) {
        function esc_html($text) {
            return $text;
        }
    }

    // Mock add_action
    if (!function_exists('add_action')) {
        function add_action($hook, $callback) {
            // No-op
        }
    }

    if (!function_exists('did_action')) {
        function did_action($hook) {
            return true;
        }
    }

    if (!class_exists('WooCommerce')) {
        class WooCommerce {}
    }
}

// Mock \Elementor\Widget_Base
namespace Elementor {
    if (!class_exists('Widget_Base')) {
        class Widget_Base {
            public function get_id() { return '123'; }
            public function start_controls_section($id, $args) {}
            public function end_controls_section() {}
            public function add_control($id, $args) {}
            public function add_group_control($type, $args) {}
            public function add_responsive_control($id, $args) {}
            public function start_controls_tabs($id) {}
            public function end_controls_tabs() {}
            public function start_controls_tab($id, $args) {}
            public function end_controls_tab() {}
            public function get_settings_for_display() {
                return $GLOBALS['test_settings'] ?? [
                    'show_section_title' => 'yes',
                    'section_title' => 'Related Products',
                    'product_title_position' => 'underneath',
                    'products_count' => 4,
                    'hover_reveal_effect' => 'fade',
                ];
            }

            public function get_render_attribute_string($element) {
                return '';
            }

            public function add_render_attribute($element, $key, $value = null, $overwrite = false) {}
        }
    }

    if (!class_exists('Controls_Manager')) {
        class Controls_Manager {
            const TAB_CONTENT = 'content';
            const TAB_STYLE = 'style';
            const SWITCHER = 'switcher';
            const TEXT = 'text';
            const NUMBER = 'number';
            const SELECT = 'select';
            const COLOR = 'color';
            const SLIDER = 'slider';
            const DIMENSIONS = 'dimensions';
            const CHOOSE = 'choose';
            const HIDDEN = 'hidden';
        }
    }

    if (!class_exists('Group_Control_Typography')) {
        class Group_Control_Typography {
            public static function get_type() {
                return 'typography';
            }
        }
    }

    if (!class_exists('Group_Control_Border')) {
        class Group_Control_Border {
            public static function get_type() {
                return 'border';
            }
        }
    }

    if (!class_exists('Group_Control_Box_Shadow')) {
        class Group_Control_Box_Shadow {
            public static function get_type() {
                return 'box-shadow';
            }
        }
    }

    if (!class_exists('Plugin')) {
        class Plugin {
            public static $instance;
            public $editor;
            public function __construct() {
                $this->editor = new class {
                    public function is_edit_mode() {
                        return true;
                    }
                };
            }
        }
    }
}

// Mock WooCommerce and WP functions
namespace {
    $GLOBALS['is_singular_product'] = true;
    function is_singular($type) {
        if ($type === 'product') return $GLOBALS['is_singular_product'];
        return false;
    }

    $GLOBALS['related_ids'] = [101, 102, 103, 104];
    function wc_get_related_products($id, $limit) {
        return $GLOBALS['related_ids'];
    }

    function wc_get_product($id) {
        if ($id === 999) return false;
        return new class {
            public function get_image() {
                return '<img src="dummy.jpg" />';
            }
        };
    }

    function get_the_ID() {
        return 101;
    }

    function the_permalink() {
        echo 'http://example.com/product';
    }

    function the_title() {
        echo 'Sample Product';
    }

    function wp_reset_postdata() {}

    class WP_Query {
        public $posts;
        private $index = 0;
        public function __construct($args) {
            $this->posts = [1, 2, 3, 4];
        }
        public function have_posts() {
            return $this->index < count($this->posts);
        }
        public function the_post() {
            $this->index++;
        }
    }

    // Initialize Elementor instance
    \Elementor\Plugin::$instance = new \Elementor\Plugin();

    // Include the widget class
    require_once __DIR__ . '/../elementor-product-related-widget/widgets/product-related-widget.php';

    if (!class_exists('Testable_Product_Related_Widget')) {
        class Testable_Product_Related_Widget extends Product_Related_Widget {
            public function public_register_controls() {
                $this->register_controls();
            }
            public function public_render() {
                $this->render();
            }
        }
    }

    // Test instantiation
    $widget = new Testable_Product_Related_Widget();
    echo "Widget Name: " . $widget->get_name() . "\n";

    // Mock global $post
    $GLOBALS['post'] = (object) ['ID' => 1];

    // Test Case: Underneath Position Visibility
    echo "Test Case: Underneath Position Visibility\n";
    $GLOBALS['test_settings'] = [
        'show_section_title' => 'yes',
        'section_title' => 'Related Products',
        'product_title_position' => 'underneath',
        'products_count' => 4,
        'hover_reveal_effect' => 'fade',
    ];
    ob_start();
    $widget->public_render();
    $output = ob_get_clean();
    if (strpos($output, 'class="product-item-content"') !== false && strpos($output, 'class="product-name"') !== false) {
        echo " - Visibility test passed.\n";
    } else {
        echo " - Visibility test failed.\n";
    }

    // Test Case: Controls Registration
    echo "Test Case: Controls Registration\n";
    try {
        $widget->public_register_controls();
        echo " - Controls registration test passed.\n";
    } catch (\Exception $e) {
        echo " - Controls registration test failed: " . $e->getMessage() . "\n";
    }
}
