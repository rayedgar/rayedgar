<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor XR Widget.
 *
 * Elementor widget that displays text with typography and color options.
 */
class XR_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xr';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'XR', 'elementor-xr-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-text-area';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-xr-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'elementor-xr-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your text', 'elementor-xr-widget' ),
				'default' => esc_html__( 'XR Content', 'elementor-xr-widget' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor-xr-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xr-widget-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .xr-widget-text',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['text'] ) ) {
			return;
		}
		?>
		<div class="xr-widget-text">
			<?php echo esc_html( $settings['text'] ); ?>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function content_template() {
		?>
		<# if ( settings.text ) { #>
			<div class="xr-widget-text">
				{{ settings.text }}
			</div>
		<# } #>
		<?php
	}
}
