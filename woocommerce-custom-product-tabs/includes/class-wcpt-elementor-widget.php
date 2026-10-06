<?php
/**
 * Elementor Widget Integration for WooCommerce Custom Product Tabs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
	return;
}

/**
 * Class WCPT_Elementor_Widget
 */
class WCPT_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'woocommerce-custom-product-tabs';
	}

	public function get_title() {
		return __( 'Custom Product Tabs', 'woocommerce-custom-product-tabs' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return array( 'woocommerce-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'woocommerce', 'tabs', 'custom tabs', 'product tabs', 'product' );
	}

	protected function register_controls() {

		// Content Section - Tab Overrides
		$this->start_controls_section(
			'section_tab_overrides',
			array(
				'label' => __( 'Tab Title Overrides', 'woocommerce-custom-product-tabs' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'override_notice',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>Note:</strong> Customize titles for default WooCommerce tabs or custom tabs registered via WooCommerce Custom Product Tabs manager.', 'woocommerce-custom-product-tabs' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'title_description',
			array(
				'label'       => __( 'Description Tab Title', 'woocommerce-custom-product-tabs' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Info', 'woocommerce-custom-product-tabs' ),
				'placeholder' => __( 'Info', 'woocommerce-custom-product-tabs' ),
			)
		);

		$this->add_control(
			'title_additional_information',
			array(
				'label'       => __( 'Additional Information Tab Title', 'woocommerce-custom-product-tabs' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'More Information', 'woocommerce-custom-product-tabs' ),
				'placeholder' => __( 'More Information', 'woocommerce-custom-product-tabs' ),
			)
		);

		$this->add_control(
			'title_reviews',
			array(
				'label'       => __( 'Reviews Tab Title', 'woocommerce-custom-product-tabs' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Reviews', 'woocommerce-custom-product-tabs' ),
				'placeholder' => __( 'Reviews', 'woocommerce-custom-product-tabs' ),
			)
		);

		$this->end_controls_section();

		// Content Section - Layout & Animation Settings
		$this->start_controls_section(
			'section_layout_settings',
			array(
				'label' => __( 'Layout & Settings', 'woocommerce-custom-product-tabs' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'display_layout',
			array(
				'label'        => __( 'Display Layout', 'woocommerce-custom-product-tabs' ),
				'type'         => \Elementor\Controls_Manager::SELECT,
				'default'      => 'tabs',
				'options'      => array(
					'tabs'   => __( 'Tabs Header', 'woocommerce-custom-product-tabs' ),
					'fields' => __( 'Stacked Fields', 'woocommerce-custom-product-tabs' ),
				),
				'prefix_class' => 'wcpt-layout%s-',
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'      => __( 'Animation Speed (ms)', 'woocommerce-custom-product-tabs' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wcpt-tabs-wrapper' => '--wcpt-animation-speed: {{SIZE}}ms;',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Tab Headers
		$this->start_controls_section(
			'section_tab_header_style',
			array(
				'label' => __( 'Tab Headers', 'woocommerce-custom-product-tabs' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => __( 'Header Spacing (Margin Bottom)', 'woocommerce-custom-product-tabs' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'tab_padding',
			array(
				'label'      => __( 'Tab Link Padding', 'woocommerce-custom-product-tabs' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important; display: inline-block !important; width: 100% !important;',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_header_style_tabs' );

		// Normal Tab State
		$this->start_controls_tab(
			'tab_header_normal',
			array(
				'label' => __( 'Normal', 'woocommerce-custom-product-tabs' ),
			)
		);

		$this->add_control(
			'tab_text_color',
			array(
				'label'     => __( 'Text Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li a' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'tab_bg_color',
			array(
				'label'     => __( 'Background Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li a' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		// Active Tab State
		$this->start_controls_tab(
			'tab_header_active',
			array(
				'label' => __( 'Active', 'woocommerce-custom-product-tabs' ),
			)
		);

		$this->add_control(
			'tab_active_text_color',
			array(
				'label'     => __( 'Text Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li.active a' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'tab_active_bg_color',
			array(
				'label'     => __( 'Background Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li.active' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .woocommerce-tabs ul.tabs li.active a' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Style Section - Content Area
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content Panel', 'woocommerce-custom-product-tabs' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_text_color',
			array(
				'label'     => __( 'Text Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wcpt-stacked-panel'     => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_bg_color',
			array(
				'label'     => __( 'Background Color', 'woocommerce-custom-product-tabs' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wcpt-stacked-panel'     => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'woocommerce-custom-product-tabs' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-Tabs-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wcpt-stacked-panel'     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Fetch WooCommerce product tabs
		$tabs = array();
		if ( function_exists( 'wc_get_product_tabs' ) ) {
			$tabs = wc_get_product_tabs();
		}

		// Apply tab title overrides
		if ( ! empty( $tabs['description'] ) && ! empty( $settings['title_description'] ) ) {
			$tabs['description']['title'] = esc_html( $settings['title_description'] );
		}
		if ( ! empty( $tabs['additional_information'] ) && ! empty( $settings['title_additional_information'] ) ) {
			$tabs['additional_information']['title'] = esc_html( $settings['title_additional_information'] );
		}
		if ( ! empty( $tabs['reviews'] ) && ! empty( $settings['title_reviews'] ) ) {
			$tabs['reviews']['title'] = esc_html( $settings['title_reviews'] );
		}

		// Fallback sample tabs if empty (e.g. in Elementor editor mode without WooCommerce global post)
		if ( empty( $tabs ) ) {
			$tabs = array(
				'description'            => array(
					'title'    => ! empty( $settings['title_description'] ) ? $settings['title_description'] : __( 'Info', 'woocommerce-custom-product-tabs' ),
					'callback' => function() {
						echo '<p>' . esc_html( __( 'Sample description content for WooCommerce Product Tabs preview.', 'woocommerce-custom-product-tabs' ) ) . '</p>';
					},
				),
				'additional_information' => array(
					'title'    => ! empty( $settings['title_additional_information'] ) ? $settings['title_additional_information'] : __( 'More Information', 'woocommerce-custom-product-tabs' ),
					'callback' => function() {
						echo '<p>' . esc_html( __( 'Sample additional details, attributes, and specifications.', 'woocommerce-custom-product-tabs' ) ) . '</p>';
					},
				),
			);
		}

		?>
		<div class="wcpt-tabs-wrapper woocommerce-tabs wcpt-layout-<?php echo esc_attr( $settings['display_layout'] ); ?>" style="min-height: 150px;">
			<ul class="tabs wcpt-tabs-nav" role="tablist">
				<?php
				$i = 0;
				foreach ( $tabs as $key => $tab ) :
					$i++;
					$active_class = ( 1 === $i ) ? 'active' : '';
					?>
					<li class="<?php echo esc_attr( $active_class ); ?> <?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab">
						<a href="#tab-<?php echo esc_attr( $key ); ?>" class="wcpt-tab-link"><?php echo wp_kses_post( $tab['title'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="wcpt-tabs-panels">
				<?php
				$j = 0;
				foreach ( $tabs as $key => $tab ) :
					$j++;
					$display_style = ( 1 === $j ) ? 'display: block;' : 'display: none;';
					?>
					<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wcpt-panel wcpt-animate" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" style="<?php echo esc_attr( $display_style ); ?>">
						<?php
						if ( isset( $tab['callback'] ) && is_callable( $tab['callback'] ) ) {
							call_user_func( $tab['callback'], $key, $tab );
						} elseif ( ! empty( $tab['content'] ) ) {
							echo wp_kses_post( $tab['content'] );
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$(document).off('click.wcpt').on('click.wcpt', '.wcpt-tabs-wrapper .wcpt-tabs-nav a', function(e) {
				e.preventDefault();
				var $link = $(this);
				var $tabLi = $link.parent('li');
				var targetId = $link.attr('href');
				var $wrapper = $link.closest('.wcpt-tabs-wrapper');

				$tabLi.addClass('active').siblings().removeClass('active');
				var $panel = $wrapper.find(targetId);

				$panel.siblings('.wcpt-panel').hide();
				$panel.removeClass('wcpt-animate');
				// Trigger reflow for restart animation
				if ($panel[0]) {
					void $panel[0].offsetWidth;
				}
				$panel.addClass('wcpt-animate').show();
			});
		});
		</script>
		<style>
		.wcpt-animate {
			animation: wcptFadeIn var(--wcpt-animation-speed, 300ms) ease-in-out;
		}
		@keyframes wcptFadeIn {
			from { opacity: 0; transform: translateY(4px); }
			to { opacity: 1; transform: translateY(0); }
		}
		</style>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var titleDesc = settings.title_description || 'Info';
		var titleAdd  = settings.title_additional_information || 'More Information';
		var titleRev  = settings.title_reviews || 'Reviews';
		#>
		<div class="wcpt-tabs-wrapper woocommerce-tabs" style="min-height: 150px;">
			<ul class="tabs wcpt-tabs-nav">
				<li class="active description_tab"><a href="#tab-description">{{{ titleDesc }}}</a></li>
				<li class="additional_information_tab"><a href="#tab-additional_information">{{{ titleAdd }}}</a></li>
				<li class="reviews_tab"><a href="#tab-reviews">{{{ titleRev }}}</a></li>
			</ul>
			<div class="wcpt-tabs-panels" style="padding: 15px; border: 1px dashed #ccc; min-height: 80px;">
				<div id="tab-description" class="woocommerce-Tabs-panel">
					<p><?php esc_html_e( 'Description panel preview in Elementor editor.', 'woocommerce-custom-product-tabs' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Register Elementor Widget.
 */
function wcpt_register_elementor_widget( $widgets_manager ) {
	$widgets_manager->register( new \WCPT_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'wcpt_register_elementor_widget' );
