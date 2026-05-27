<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Product_Related_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'product_related';
	}

	public function get_title() {
		return esc_html__( 'Product Related', 'elementor-product-related-widget' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'related', 'products' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'elementor-product-related-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_section_title',
			[
				'label' => esc_html__( 'Show Section Title', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor-product-related-widget' ),
				'label_off' => esc_html__( 'Hide', 'elementor-product-related-widget' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'section_title',
			[
				'label' => esc_html__( 'Section Title', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Related Products', 'elementor-product-related-widget' ),
				'condition' => [
					'show_section_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_align',
			[
				'label' => esc_html__( 'Image Alignment', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .product-item-content' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'margin-right: auto; margin-left: 0;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right' => 'margin-left: auto; margin-right: 0;',
				],
			]
		);

		$this->add_control(
			'product_title_position',
			[
				'label' => esc_html__( 'Product Title Position', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'underneath',
				'options' => [
					'none'       => esc_html__( 'None', 'elementor-product-related-widget' ),
					'underneath' => esc_html__( 'Underneath Image', 'elementor-product-related-widget' ),
					'overlay'    => esc_html__( 'Overlay on Hover', 'elementor-product-related-widget' ),
				],
				'selectors' => [
					'{{WRAPPER}} .product-hover-overlay' => 'opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; transition: opacity 0.3s; pointer-events: none;',
					'{{WRAPPER}} .product-image-wrapper:hover .product-hover-overlay' => 'opacity: 1;',
					'{{WRAPPER}} .product-name a' => 'color: inherit; text-decoration: none;',
					'{{WRAPPER}} .product-image-wrapper img' => 'width: 100%; height: auto; display: block;',
					'{{WRAPPER}} .product-name' => 'display: block; width: 100%; margin: 0; padding: 0;',
					'{{WRAPPER}} .product-item-content' => 'display: block;',
					'{{WRAPPER}} .product-hover-overlay .product-name' => 'margin: 0; padding: 10px;',
					'{{WRAPPER}} .dummy-image' => 'width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'product_box_margin',
			[
				'label' => esc_html__( 'Box Margin', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .related-product-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Products Count', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => esc_html__( 'Image Width', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-item-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
				],
				'selectors' => [
					'{{WRAPPER}} .related-products-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->end_controls_section();

		// Style Section for Section Title
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Section Title', 'elementor-product-related-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_section_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'title_align',
			[
				'label' => esc_html__( 'Alignment', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .related-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .related-title' => 'color: {{VALUE}};',
				],
			]
		);



		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .related-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .related-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section for Product Name
		$this->start_controls_section(
			'product_name_style',
			[
				'label' => esc_html__( 'Product Name', 'elementor-product-related-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'product_title_position!' => 'none',
				],
			]
		);

		$this->add_control(
			'product_name_color',
			[
				'label' => esc_html__( 'Color', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-name' => 'color: {{VALUE}};',
					'{{WRAPPER}} .product-hover-overlay .product-name' => 'color: {{VALUE}};',
				],
				'condition' => [
					'product_title_position!' => 'none',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'product_name_typography',
				'selector' => '{{WRAPPER}} .product-name, {{WRAPPER}} .product-hover-overlay .product-name',
				'condition' => [
					'product_title_position!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'product_name_align',
			[
				'label' => esc_html__( 'Alignment', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor-product-related-widget' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .product-name' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .product-hover-overlay' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
				],
				'condition' => [
					'product_title_position!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'product_name_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .product-name' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0;',
				],
				'condition' => [
					'product_title_position' => 'underneath',
				],
			]
		);

		$this->end_controls_section();

		// Style Section for Product Image / Hover
		$this->start_controls_section(
			'product_image_style',
			[
				'label' => esc_html__( 'Image & Hover', 'elementor-product-related-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_overlay_bg',
			[
				'label' => esc_html__( 'Hover Overlay Background', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-hover-overlay' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'product_title_position' => 'overlay',
				],
			]
		);

		$this->add_responsive_control(
			'product_grid_spacing',
			[
				'label' => esc_html__( 'Grid Spacing (Gap)', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .related-products-grid' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_image_spacing',
			[
				'label' => esc_html__( 'Image Spacing (Margin)', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .product-image-wrapper' => 'margin-top: {{TOP}}{{UNIT}} !important; margin-right: {{RIGHT}}{{UNIT}} !important; margin-bottom: {{BOTTOM}}{{UNIT}} !important; margin-left: {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'product_box_padding',
			[
				'label' => esc_html__( 'Box Padding', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .related-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'product_box_style_tabs' );

		$this->start_controls_tab(
			'product_box_style_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor-product-related-widget' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'product_box_border',
				'selector' => '{{WRAPPER}} .related-product-item',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'product_box_shadow',
				'selector' => '{{WRAPPER}} .related-product-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'product_box_style_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor-product-related-widget' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'product_box_border_hover',
				'selector' => '{{WRAPPER}} .related-product-item:hover',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'product_box_shadow_hover',
				'selector' => '{{WRAPPER}} .related-product-item:hover',
			]
		);

		$this->add_control(
			'product_box_bg_hover',
			[
				'label' => esc_html__( 'Background Color', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .related-product-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'product_box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor-product-related-widget' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .related-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .product-image-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! is_singular( 'product' ) ) {
			if ( $is_editor ) {
				echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Related products are only visible on single product pages.', 'elementor-product-related-widget' ) . '</div>';
			}
			return;
		}

		global $post;

		// Ensure we have a product object
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			if ( $is_editor ) {
				echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Product data not found.', 'elementor-product-related-widget' ) . '</div>';
			}
			return;
		}

		$related_ids = wc_get_related_products( $post->ID, $settings['posts_per_page'] );

		if ( empty( $related_ids ) ) {
			if ( $is_editor ) {
				echo '<div class="elementor-alert elementor-alert-info">' . esc_html__( 'No related products found for this product.', 'elementor-product-related-widget' ) . '</div>';
			}
			return;
		}

		$args = [
			'post_type' => 'product',
			'post__in'  => $related_ids,
			'posts_per_page' => $settings['posts_per_page'],
			'orderby' => 'post__in',
		];

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-product-related-wrapper' );
		$this->add_render_attribute( 'grid', 'class', 'related-products-grid' );
		$this->add_render_attribute( 'grid', 'style', 'display: grid;' );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'yes' === $settings['show_section_title'] && ! empty( $settings['section_title'] ) ) : ?>
				<h2 class="related-title"><?php echo esc_html( $settings['section_title'] ); ?></h2>
			<?php endif; ?>

			<div <?php echo $this->get_render_attribute_string( 'grid' ); ?>>
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$product = wc_get_product( get_the_ID() );
					?>
					<div class="related-product-item">
						<div class="product-item-content">
							<div class="product-image-wrapper" style="position: relative; overflow: hidden;">
								<a href="<?php the_permalink(); ?>">
									<?php echo $product->get_image(); ?>
								</a>
								<?php if ( 'overlay' === $settings['product_title_position'] ) : ?>
									<div class="product-hover-overlay">
										<h3 class="product-name hover-title">
											<?php the_title(); ?>
										</h3>
									</div>
								<?php endif; ?>
							</div>

							<?php if ( 'underneath' === $settings['product_title_position'] ) : ?>
								<h3 class="product-name">
									<a href="<?php the_permalink(); ?>">
										<?php the_title(); ?>
									</a>
								</h3>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var section_title = settings.section_title;
		var show_section_title = settings.show_section_title;
		var product_title_position = settings.product_title_position;
		var columns = settings.columns;
		#>
		<div class="elementor-product-related-wrapper">
			<# if ( 'yes' === show_section_title && section_title ) { #>
				<h2 class="related-title">{{{ section_title }}}</h2>
			<# } #>

			<div class="related-products-grid" style="display: grid;">
				<# /* Dummy products for preview */
				for ( var i = 0; i < settings.posts_per_page; i++ ) {
				#>
				<div class="related-product-item">
					<div class="product-item-content">
						<div class="product-image-wrapper" style="position: relative; overflow: hidden;">
							<div class="dummy-image">
								<i class="eicon-image-bold" style="font-size: 48px; color: #ccc;"></i>
							</div>
							<# if ( 'overlay' === product_title_position ) { #>
								<div class="product-hover-overlay">
									<h3 class="product-name hover-title">
										Product Title {{ i + 1 }}
									</h3>
								</div>
							<# } #>
						</div>

						<# if ( 'underneath' === product_title_position ) { #>
							<h3 class="product-name">
								Product Title {{ i + 1 }}
							</h3>
						<# } #>
					</div>
				</div>
				<# } #>
			</div>
		</div>

		<style>
			.elementor-element-{{ id }} .dummy-image {
				background: #eee;
				aspect-ratio: 1/1;
				display: flex;
				align-items: center;
				justify-content: center;
			}
		</style>
		<?php
	}
}
