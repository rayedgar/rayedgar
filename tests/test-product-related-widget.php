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

    // WooCommerce and Elementor check will be handled by class_exists if we could,
    // but it's a built-in function.
    // We'll just define the classes instead.
    if (!class_exists('WooCommerce')) {
        class WooCommerce {}
    }
}

// Mock \Elementor\Widget_Base
namespace Elementor {
    if (!class_exists('Widget_Base')) {
        class Widget_Base {
            public function start_controls_section($id, $args) {}
            public function end_controls_section() {}
            public function add_control($id, $args) {}
            public function add_group_control($type, $args) {}
            public function add_responsive_control($id, $args) {}
            public function get_settings_for_display() {
                return [
                    'show_section_title' => 'yes',
                    'section_title' => 'Related Products',
                    'show_product_name' => 'yes',
                    'hover_title_on_image' => 'no',
                    'posts_per_page' => 4,
                    'columns' => '4',
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
        }
    }
}

// Mock WooCommerce and WP functions
namespace {
    if (!function_exists('is_singular')) {
        function is_singular($type) {
            return $type === 'product';
        }
    }

    if (!function_exists('wc_get_related_products')) {
        function wc_get_related_products($id, $limit) {
            return [101, 102, 103, 104];
        }
    }

    if (!function_exists('wc_get_product')) {
        function wc_get_product($id) {
            return new class {
                public function get_image() {
                    return '<img src="dummy.jpg" />';
                }
            };
        }
    }

    if (!function_exists('get_the_ID')) {
        function get_the_ID() {
            return 101;
        }
    }

    if (!function_exists('the_permalink')) {
        function the_permalink() {
            echo 'http://example.com/product';
        }
    }

    if (!function_exists('the_title')) {
        function the_title() {
            echo 'Sample Product';
        }
    }

    if (!function_exists('wp_reset_postdata')) {
        function wp_reset_postdata() {}
    }

    if (!class_exists('WP_Query')) {
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
    }

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
    echo "Widget Title: " . $widget->get_title() . "\n";

    // Mock global $post
    $GLOBALS['post'] = (object) ['ID' => 1];

    // Test register_controls
    $widget->public_register_controls();
    echo "Controls registered successfully.\n";

    // Test render (output buffering to capture)
    ob_start();
    $widget->public_render();
    $output = ob_get_clean();

    if (strpos($output, 'Related Products') !== false && strpos($output, 'Sample Product') !== false) {
        echo "Render test passed.\n";
    } else {
        echo "Render test failed.\n";
        echo "Output: " . $output . "\n";
    }
}
