<?php
namespace {
    define('ABSPATH', true);
    define('ELEMENTOR_VERSION', '3.5.0');

    function esc_html__( $text, $domain ) { return $text; }
    function esc_html( $text ) { return $text; }
    function add_action( $hook, $callback ) {
        global $actions;
        $actions[$hook][] = $callback;
    }
    function did_action($hook) { return $hook === 'elementor/loaded'; }

    $actions = [];
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

    class Widgets_Manager {
        public function register($widget) {}
    }
}

namespace {
    require_once 'elementor-xr-widget/elementor-xr-widget.php';

    echo "Testing Elementor_XR_Widget...\n";

    global $actions;

    // Simulate plugins_loaded
    if (isset($actions['plugins_loaded'])) {
        foreach ($actions['plugins_loaded'] as $callback) {
            call_user_func($callback);
        }
    }

    // Check if elementor/widgets/register action was added
    if (isset($actions['elementor/widgets/register'])) {
        echo "Register Widgets Action Hooked!\n";

        // Trigger widget registration
        $widgets_manager = new \Elementor\Widgets_Manager();
        foreach ($actions['elementor/widgets/register'] as $callback) {
            call_user_func($callback, $widgets_manager);
        }
    } else {
        echo "Register Widgets Action NOT Hooked!\n";
        exit(1);
    }

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
