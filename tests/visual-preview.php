<?php
namespace Elementor {
    class Widget_Base {
        public $settings = [];
        public function get_settings_for_display() { return $this->settings; }
        public function start_controls_section($id, $args) {}
        public function add_control($id, $args) {}
        public function add_responsive_control($id, $args) {}
        public function end_controls_section() {}
        public function add_group_control($type, $args) {}
        public function start_controls_tabs($id) {}
        public function end_controls_tabs() {}
        public function start_controls_tab($id, $args) {}
        public function end_controls_tab() {}
        public function get_id() { return 'mock-id'; }
    }
    class Controls_Manager {
        const SELECT = 'select';
        const CHOOSE = 'choose';
        const TEXT = 'text';
        const RAW_HTML = 'raw_html';
        const TAB_STYLE = 'style';
        const COLOR = 'color';
        const DIMENSIONS = 'dimensions';
        const SLIDER = 'slider';
    }
    class Group_Control_Typography {
        public static function get_type() { return 'typography'; }
    }
    class Group_Control_Border {
        public static function get_type() { return 'border'; }
    }
    class Plugin {
        public static $instance;
        public $editor;
        public function __construct() { $this->editor = (object)['is_edit_mode' => function(){return false;}]; }
    }
}

namespace {
    define('ABSPATH', true);

    function add_action($tag, $callback) {}
    function add_filter($tag, $callback) {}
    function _x($text, $context, $domain) { return $text; }
    function __($text, $domain) { return $text; }
    function apply_filters($tag, $value) {
        if ($tag === 'woocommerce_product_tabs') {
            return [
                'description' => [
                    'title' => 'Product Description',
                    'callback' => function(){ echo '<h2>Description</h2><p>Responsive Layout Test Content.</p>'; },
                    'priority' => 10
                ]
            ];
        }
        if ($tag === 'the_content' || $tag === 'the_title') { return $value; }
        return $value;
    }
    function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES); }
    function selected($a, $b) { echo $a === $b ? ' selected' : ''; }
    function admin_url($p) { return $p; }

    \Elementor\Plugin::$instance = new \Elementor\Plugin();

    class MockProduct {
        public $id = 1;
        public function get_id() { return $this->id; }
    }

    function wcpt_render_tab_content( $key, $tab ) {
        echo '<div class="wcpt-tab-content-wrapper">';
        echo apply_filters( 'the_content', isset($tab['content']) ? $tab['content'] : '' );
        echo '</div>';
    }

    require_once 'woocommerce-custom-product-tabs/elementor-widget.php';

    global $product;
    $product = new MockProduct();

    $widget = new WCPT_Elementor_Widget();
    $reflection = new ReflectionClass($widget);
    $render = $reflection->getMethod('render');
    $render->setAccessible(true);

    echo "<html><head><style>
        body { font-family: sans-serif; padding: 40px; background: #fafafa; }
        .tabs { list-style: none; padding: 0; display: flex; }
        .tabs li { list-style: none; }

        /* Applied Global Fix Styles mimicking the Elementor Selectors */
        .woocommerce-tabs ul.tabs li a {
			white-space: normal !important;
			word-wrap: break-word !important;
			display: inline-block !important; /* FIXED */
			width: 100% !important; /* FIXED */
            box-sizing: border-box;
            background: #3498db;
            color: white;
            padding: 30px !important; /* Increased padding to test bottom visibility */
            text-decoration: none;
		}

        .woocommerce-tabs ul.tabs {
            margin-bottom: 20px !important; /* Testing the Spacing control */
            padding: 0;
            display: flex;
        }

        .woocommerce-tabs ul.tabs li {
			margin-bottom: 0 !important;
			margin-top: 0 !important;
			box-sizing: border-box !important;
            border: 2px solid #2980b9 !important;
		}

		.woocommerce-Tabs-panel,
		.wcpt-tab-content-wrapper {
			margin-bottom: 0 !important;
			margin-top: 0 !important;
			box-sizing: border-box !important;
		}

        .elementor-widget-mock-id .woocommerce-Tabs-panel {
            padding: 30px !important;
            border: 2px solid #2980b9 !important;
            background: #ecf0f1 !important;
        }

    </style></head><body>";

    echo "<h1>Elementor Widget Preview (Padding and Spacing Fix)</h1>";
    echo "<p>The tab link (blue) should show large padding on all sides, and there should be a 20px gap below the tabs.</p>";

    echo "<div class='elementor-widget-mock-id'>";
    $widget->settings = ['tabs_animation' => 'none'];
    $render->invoke($widget);
    echo "</div>";

    echo "</body></html>";
}
