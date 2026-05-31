<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Widget for Custom Product Tabs.
 */
class WCPT_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'wcpt_product_tabs';
	}

	public function get_title() {
		return __( 'Product Custom Tabs/Fields', 'wcpt' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'wcpt' ),
			]
		);

		$this->add_control(
			'display_layout',
			[
				'label' => __( 'Display Layout', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'tabs',
				'options' => [
					'tabs' => [
						'title' => __( 'Standard Tabs', 'wcpt' ),
						'icon' => 'eicon-tabs',
					],
					'fields' => [
						'title' => __( 'Separate Stacked Fields', 'wcpt' ),
						'icon' => 'eicon-editor-list-ol',
					],
				],
				'toggle' => false,
			]
		);

		$this->add_control(
			'style_notice',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					'<div class="elementor-control-field-description">%s <a href="%s" target="_blank">%s</a></div>',
					__( 'Individual field styling (spacing, size, borders) is managed within each', 'wcpt' ),
					admin_url( 'edit.php?post_type=wc_product_tab' ),
					__( 'Product Tab item.', 'wcpt' )
				),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_standard_titles',
			[
				'label' => __( 'Standard Tab Titles', 'wcpt' ),
			]
		);

		$this->add_control(
			'title_description',
			[
				'label' => __( 'Info Tab Title', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. Info', 'wcpt' ),
			]
		);

		$this->add_control(
			'title_additional_info',
			[
				'label' => __( 'More Information Tab Title', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'e.g. More Information', 'wcpt' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tabs',
			[
				'label' => __( 'Tab Titles / Headers', 'wcpt' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tabs_alignment',
			[
				'label' => __( 'Alignment', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wcpt' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wcpt' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wcpt' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tabs' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .wcpt-stacked-field h3' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tabs_typography',
				'selector' => '{{WRAPPER}} .tabs li a, {{WRAPPER}} .wcpt-stacked-field h3',
			]
		);

		$this->add_responsive_control(
			'tabs_padding',
			[
				'label' => __( 'Padding', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{VALUE}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wcpt-stacked-field h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tabs_title_normal',
			[
				'label' => __( 'Normal', 'wcpt' ),
			]
		);

		$this->add_control(
			'tabs_title_color',
			[
				'label' => __( 'Text Color', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wcpt-stacked-field h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_title_bg_color',
			[
				'label' => __( 'Background Color', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs li' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wcpt-stacked-field h3' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_title_active',
			[
				'label' => __( 'Active', 'wcpt' ),
			]
		);

		$this->add_control(
			'tabs_title_color_active',
			[
				'label' => __( 'Text Color', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs li.active a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tabs_title_bg_color_active',
			[
				'label' => __( 'Background Color', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tabs li.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'wcpt' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label' => __( 'Alignment', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'wcpt' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wcpt' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wcpt' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wcpt-tab-content-wrapper' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-product-attributes' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text Color', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wcpt-tab-content-wrapper' => 'color: {{VALUE}};',
					'{{WRAPPER}} .woocommerce-product-attributes' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .wcpt-tab-content-wrapper, {{WRAPPER}} .woocommerce-product-attributes',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'wcpt' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-Tabs-panel, {{WRAPPER}} .wcpt-tab-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $product;

		if ( ! $product ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo __( 'Please use this widget on a Single Product page.', 'wcpt' );
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$layout   = $settings['display_layout'];

		// Fetch all active tabs for this product (standard + custom)
		$all_tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( empty( $all_tabs ) ) {
			return;
		}

		// Apply title overrides
		if ( ! empty( $settings['title_description'] ) && isset( $all_tabs['description'] ) ) {
			$all_tabs['description']['title'] = $settings['title_description'];
		}
		if ( ! empty( $settings['title_additional_info'] ) && isset( $all_tabs['additional_information'] ) ) {
			$all_tabs['additional_information']['title'] = $settings['title_additional_info'];
		}

		// Re-sort tabs based on priority
		uasort( $all_tabs, function( $a, $b ) {
			$pA = isset( $a['priority'] ) ? (int) $a['priority'] : 10;
			$pB = isset( $b['priority'] ) ? (int) $b['priority'] : 10;
			return $pA - $pB;
		} );

		if ( 'tabs' === $layout ) {
			$this->render_tabs( $all_tabs );
		} else {
			$this->render_fields( $all_tabs );
		}
	}

	protected function render_tabs( $tabs ) {
		?>
		<div class="woocommerce-tabs wc-tabs-wrapper">
			<ul class="tabs wc-tabs" role="tablist">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<li class="tab-title-<?php echo esc_attr( $key ); ?>" role="tab">
						<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel">
					<?php $this->render_tab_content( $key, $tab ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function render_fields( $fields ) {
		foreach ( $fields as $key => $field ) {
			echo '<div class="wcpt-stacked-field">';
			// For stacked fields, we don't need the h3 for standard tabs as they usually have their own
			// or we want them suppressed if they are description/additional_info.
			// Actually, if we are suppressing the internal H2, we might want to keep our H3.
			// But the user might want more control.
			echo '<h3>' . esc_html( $field['title'] ) . '</h3>';
			$this->render_tab_content( $key, $field );
			echo '</div>';
		}
	}

	/**
	 * Helper to render tab content, supporting both standard callbacks and custom content.
	 */
	protected function render_tab_content( $key, $tab ) {
		// Custom tabs added by our plugin will have 'styles' and 'content' keys.
		// Standard WooCommerce tabs will have a 'callback'.
		if ( isset( $tab['callback'] ) && is_callable( $tab['callback'] ) ) {
			// Wrap in our style div to ensure widget alignment applies
			echo '<div class="wcpt-tab-content-wrapper">';

			// Capture output to strip standard headers if they exist
			ob_start();
			call_user_func( $tab['callback'], $key, $tab );
			$content = ob_get_clean();

			// Remove <h2> tags from standard tab callbacks (e.g. "Description" or "Additional Information")
			// because they are redundant in a tabbed layout or we provide our own in stacked layout.
			$content = preg_replace( '/<h2[^>]*>.*?<\/h2>/si', '', $content );
			echo $content;

			echo '</div>';
		} elseif ( function_exists( 'wcpt_render_tab_content' ) ) {
			wcpt_render_tab_content( $key, $tab );
		}
	}
}
