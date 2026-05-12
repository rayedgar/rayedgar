<?php
namespace {
    define('ABSPATH', true);

    function esc_html__( $text, $domain ) { return $text; }
    function esc_html( $text ) { return $text; }
    function add_action( $hook, $callback ) {}
}

namespace Elementor {
    class Widget_Base {
        public function get_settings_for_display() {
            return ['text' => 'Test XR Content'];
        }
        protected function start_controls_section($id, $args) {}
        protected function add_control($id, $args) {}
        protected function add_group_control($type, $args) {}
        protected function end_controls_section() {}
    }

    class Controls_Manager {
        const TAB_CONTENT = 'content';
        const TEXT = 'text';
        const COLOR = 'color';
    }

    class Group_Control_Typography {
        public static function get_type() {
            return 'typography';
        }
    }
}

namespace {
    require_once 'elementor-xr-widget/widgets/xr-widget.php';

    $widget = new \XR_Widget();

    echo "Testing XR_Widget...\n";
    echo "Name: " . $widget->get_name() . " (Expected: xr)\n";
    echo "Title: " . $widget->get_title() . " (Expected: XR)\n";

    if ($widget->get_name() === 'xr' && $widget->get_title() === 'XR') {
        echo "Widget Metadata Test Passed!\n";
    } else {
        echo "Widget Metadata Test Failed!\n";
        exit(1);
    }

    // Verify render method doesn't crash
    ob_start();
    // We need to use reflection to call protected render()
    $reflection = new ReflectionClass($widget);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);
    $method->invoke($widget);
    $output = ob_get_clean();

    echo "Render Output: " . trim($output) . "\n";
    if (strpos($output, 'Test XR Content') !== false) {
        echo "Render Test Passed!\n";
    } else {
        echo "Render Test Failed!\n";
        exit(1);
    }

    echo "All XR Widget tests passed!\n";
}
