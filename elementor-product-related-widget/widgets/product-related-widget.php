<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Product_Related_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'product_related'; }
	public function get_title() { return esc_html__( 'Advanced Product Related', 'elementor-product-related-widget' ); }
	public function get_icon() { return 'eicon-products'; }
	public function get_categories() { return [ 'general' ]; }
	public function get_keywords() { return [ 'woocommerce', 'related', 'products' ]; }

	protected function register_controls() {
		$cols = array_combine( range( 1, 16 ), array_map( 'strval', range( 1, 16 ) ) );

		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Content', 'elementor-product-related-widget' ), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ] );

		$this->add_responsive_control( 'show_section_title', [
			'label' => esc_html__( 'Show Section Title', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default' => 'yes',
		] );

		$this->add_responsive_control( 'section_title', [
			'label' => esc_html__( 'Section Title', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Related Products', 'elementor-product-related-widget' ),
			'condition' => [ 'show_section_title' => 'yes' ],
		] );

		$this->add_responsive_control( 'products_count', [
			'label' => esc_html__( 'Amount of Products to show', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'min' => 1, 'max' => 50, 'default' => 4,
		] );

		$this->add_responsive_control( 'columns', [
			'label' => esc_html__( 'Columns', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '4',
			'options' => $cols,
			'selectors' => [ '{{WRAPPER}} .related-products-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);' ],
		] );

		$this->add_responsive_control( 'product_alignment', [
			'label' => esc_html__( 'Product Alignment', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
			'options' => [
				'left' => [ 'title' => esc_html__( 'Left', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-center' ],
				'right' => [ 'title' => esc_html__( 'Right', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-right' ],
			],
			'default' => 'left',
			'selectors' => [
				'{{WRAPPER}} .product-item-content' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .product-name' => 'text-align: {{VALUE}};',
			],
		] );

		$this->add_responsive_control( 'product_alignment_flex', [
			'type' => \Elementor\Controls_Manager::HIDDEN,
			'default' => 'left',
			'selectors' => [ '{{WRAPPER}} .product-hover-overlay' => 'justify-content: {{VALUE}}; display: flex; align-items: center;' ],
			'selectors_dictionary' => [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ],
		] );

		$this->add_responsive_control( 'product_title_position', [
			'label' => esc_html__( 'Product Title Position', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'underneath',
			'options' => [
				'none' => esc_html__( 'None', 'elementor-product-related-widget' ),
				'underneath' => esc_html__( 'Underneath Image', 'elementor-product-related-widget' ),
				'overlay' => esc_html__( 'Overlay on Hover', 'elementor-product-related-widget' ),
			],
			'selectors' => [
				'{{WRAPPER}} .product-hover-overlay' => 'opacity:0;position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;transition:all .3s ease;pointer-events:none;z-index:10;',
				'{{WRAPPER}} .product-image-wrapper:hover .product-hover-overlay' => 'opacity:1;',
				'{{WRAPPER}} .product-name a' => 'color:inherit;text-decoration:none;transition:all .3s ease;',
				'{{WRAPPER}} .product-image-wrapper img' => 'width:100%;height:auto;display:block;',
				'{{WRAPPER}} .product-name' => 'display:block;width:100%;margin:0;padding:0;opacity:1;visibility:visible;transition:all .3s ease;',
				'{{WRAPPER}} .product-item-content' => 'display:block;width:100%;',
				'{{WRAPPER}} .product-hover-overlay .product-name' => 'margin:0;padding:10px;',
				'{{WRAPPER}} .product-image-wrapper' => 'position:relative;overflow:hidden;',
			],
		] );

		$this->add_responsive_control( 'hover_reveal_effect', [
			'label' => esc_html__( 'Hover Reveal Effect', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'fade',
			'options' => [
				'fade' => esc_html__( 'Fade', 'elementor-product-related-widget' ),
				'slide-up' => esc_html__( 'Slide Up', 'elementor-product-related-widget' ),
				'zoom-in' => esc_html__( 'Zoom In', 'elementor-product-related-widget' ),
			],
			'condition' => [ 'product_title_position' => 'overlay' ],
		] );

		$this->add_responsive_control( 'hover_reveal_speed', [
			'label' => esc_html__( 'Reveal Speed (ms)', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [ 'px' => [ 'min' => 100, 'max' => 1000, 'step' => 50 ] ],
			'default' => [ 'size' => 300 ],
			'selectors' => [ '{{WRAPPER}} .product-hover-overlay' => 'transition-duration: {{SIZE}}ms;' ],
			'condition' => [ 'product_title_position' => 'overlay' ],
		] );

		$this->add_responsive_control( 'image_width', [
			'label' => esc_html__( 'Image Width', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em' ],
			'range' => [ 'px' => [ 'min' => 0, 'max' => 1000 ], '%' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors' => [ '{{WRAPPER}} .product-image-wrapper' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_title_style', [
			'label' => esc_html__( 'Section Title', 'elementor-product-related-widget' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_section_title' => 'yes' ],
		] );

		$this->add_responsive_control( 'title_align', [
			'label' => esc_html__( 'Alignment', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
			'options' => [
				'left' => [ 'title' => esc_html__( 'Left', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-center' ],
				'right' => [ 'title' => esc_html__( 'Right', 'elementor-product-related-widget' ), 'icon' => 'eicon-text-align-right' ],
			],
			'selectors' => [ '{{WRAPPER}} .related-title' => 'text-align: {{VALUE}};' ],
		] );

		$this->add_control( 'title_color', [
			'label' => esc_html__( 'Color', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .related-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .related-title' ] );

		$this->add_responsive_control( 'title_spacing', [
			'label' => esc_html__( 'Spacing', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors' => [ '{{WRAPPER}} .related-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'product_name_style', [
			'label' => esc_html__( 'Product Name', 'elementor-product-related-widget' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => [ 'product_title_position!' => 'none' ],
		] );

		$this->start_controls_tabs( 'product_name_style_tabs' );

		$this->start_controls_tab( 'product_name_style_normal', [ 'label' => esc_html__( 'Normal', 'elementor-product-related-widget' ) ] );
		$this->add_control( 'product_name_color', [
			'label' => esc_html__( 'Color', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .product-name' => 'color: {{VALUE}};',
				'{{WRAPPER}} .product-name a' => 'color: {{VALUE}};',
				'{{WRAPPER}} .product-hover-overlay .product-name' => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'product_name_bg', [
			'label' => esc_html__( 'Background Color', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .product-name' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .product-hover-overlay .product-name' => 'background-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'product_name_style_hover', [ 'label' => esc_html__( 'Hover', 'elementor-product-related-widget' ) ] );
		$this->add_control( 'product_name_color_hover', [
			'label' => esc_html__( 'Hover Text Color', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .related-product-item:hover .product-name' => 'color: {{VALUE}};',
				'{{WRAPPER}} .related-product-item:hover .product-name a' => 'color: {{VALUE}};',
				'{{WRAPPER}} .product-name:hover' => 'color: {{VALUE}};',
				'{{WRAPPER}} .product-name a:hover' => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'product_name_bg_hover', [
			'label' => esc_html__( 'Hover Text Background Color & Transparency', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .related-product-item:hover .product-name' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .product-name:hover' => 'background-color: {{VALUE}};',
			],
		] );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name' => 'product_name_typography', 'selector' => '{{WRAPPER}} .product-name, {{WRAPPER}} .product-hover-overlay .product-name' ] );

		$this->add_responsive_control( 'product_name_padding', [
			'label' => esc_html__( 'Text Padding', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [ '{{WRAPPER}} .product-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'product_name_border_radius', [
			'label' => esc_html__( 'Text Border Radius', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [ '{{WRAPPER}} .product-name' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'product_name_spacing', [
			'label' => esc_html__( 'Spacing', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors' => [ '{{WRAPPER}} .product-name' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0;' ],
			'condition' => [ 'product_title_position' => 'underneath' ],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'product_image_style', [
			'label' => esc_html__( 'Image & Hover', 'elementor-product-related-widget' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'hover_overlay_bg', [
			'label' => esc_html__( 'Hover Overlay Background', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .product-hover-overlay' => 'background-color: {{VALUE}};' ],
			'condition' => [ 'product_title_position' => 'overlay' ],
		] );

		$this->add_responsive_control( 'product_grid_column_gap', [
			'label' => esc_html__( 'Columns Gap', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors' => [ '{{WRAPPER}} .related-products-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'product_grid_rows_gap', [
			'label' => esc_html__( 'Rows Gap (Vertical Spacing)', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'selectors' => [ '{{WRAPPER}} .related-products-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'product_box_padding', [
			'label' => esc_html__( 'Box Padding', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [ '{{WRAPPER}} .related-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->start_controls_tabs( 'product_box_style_tabs' );
		$this->start_controls_tab( 'product_box_style_normal', [ 'label' => esc_html__( 'Normal', 'elementor-product-related-widget' ) ] );
		$this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'product_box_border', 'selector' => '{{WRAPPER}} .related-product-item' ] );
		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'product_box_shadow', 'selector' => '{{WRAPPER}} .related-product-item' ] );
		$this->end_controls_tab();

		$this->start_controls_tab( 'product_box_style_hover', [ 'label' => esc_html__( 'Hover', 'elementor-product-related-widget' ) ] );
		$this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name' => 'product_box_border_hover', 'selector' => '{{WRAPPER}} .related-product-item:hover' ] );
		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name' => 'product_box_shadow_hover', 'selector' => '{{WRAPPER}} .related-product-item:hover' ] );
		$this->add_responsive_control( 'product_box_bg_hover', [ 'label' => esc_html__( 'Background Color', 'elementor-product-related-widget' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .related-product-item:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control( 'product_box_border_radius', [
			'label' => esc_html__( 'Border Radius', 'elementor-product-related-widget' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				'{{WRAPPER}} .related-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .product-image-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$max = (int) ( $s['products_count'] ?? 4 );
		foreach ( $s as $k => $v ) {
			if ( strpos( $k, 'products_count_' ) === 0 && ! empty( $v ) ) $max = max( $max, (int) $v );
		}

		global $post;
		$pid = ( $post && is_singular( 'product' ) ) ? $post->ID : 0;
		$rel = ( $pid && function_exists( 'wc_get_related_products' ) ) ? wc_get_related_products( $pid, $max ) : [];

		$args = ! empty( $rel ) ? [
			'post_type' => 'product', 'post_status' => 'publish', 'post__in' => $rel, 'posts_per_page' => $max, 'orderby' => 'post__in',
		] : array_filter( [
			'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => $max, 'post__not_in' => $pid ? [ $pid ] : null,
		] );

		$q = new \WP_Query( $args );
		if ( ! $q->have_posts() ) {
			if ( $is_editor ) $this->render_dummy( $s, $max );
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'elementor-product-related-wrapper', 'hover-reveal-' . ( $s['hover_reveal_effect'] ?? 'fade' ) ] );
		$this->add_render_attribute( 'grid', [ 'class' => 'related-products-grid', 'style' => 'display: grid;' ] );
		$id = $this->get_id();
		?>
		<style>
			.elementor-element-<?php echo $id; ?> .hover-reveal-slide-up .product-hover-overlay { transform: translateY(20px); }
			.elementor-element-<?php echo $id; ?> .product-image-wrapper:hover .product-hover-overlay { transform: translateY(0); opacity: 1; }
			.elementor-element-<?php echo $id; ?> .hover-reveal-zoom-in .product-hover-overlay { transform: scale(0.8); }
			.elementor-element-<?php echo $id; ?> .product-image-wrapper:hover .product-hover-overlay { transform: scale(1); opacity: 1; }
		</style>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( 'yes' === $s['show_section_title'] && ! empty( $s['section_title'] ) ) : ?>
				<h2 class="related-title"><?php echo esc_html( $s['section_title'] ); ?></h2>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string( 'grid' ); ?>>
				<?php while ( $q->have_posts() ) : $q->the_post();
					$p = function_exists('wc_get_product') ? wc_get_product( get_the_ID() ) : false;
					$img = ($p && method_exists($p, 'get_image')) ? $p->get_image() : (function_exists('get_the_post_thumbnail') ? get_the_post_thumbnail( get_the_ID(), 'woocommerce_thumbnail' ) : '');
					if ( empty( $img ) ) $img = '<div class="dummy-image" style="background:#eee;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;width:100%;min-height:100px;"><i class="eicon-image-bold" style="font-size:48px;color:#ccc;"></i></div>';
					?>
					<div class="related-product-item">
						<div class="product-item-content">
							<div class="product-image-wrapper">
								<a href="<?php the_permalink(); ?>"><?php echo $img; ?></a>
								<?php if ( 'overlay' === $s['product_title_position'] ) : ?>
									<div class="product-hover-overlay"><h3 class="product-name hover-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div>
								<?php endif; ?>
							</div>
							<?php if ( 'underneath' === $s['product_title_position'] ) : ?>
								<h3 class="product-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
		<?php
	}

	private function render_dummy( $s, $max ) {
		?>
		<div class="elementor-product-related-wrapper hover-reveal-<?php echo esc_attr( $s['hover_reveal_effect'] ?? 'fade' ); ?>">
			<?php if ( 'yes' === $s['show_section_title'] && ! empty( $s['section_title'] ) ) : ?><h2 class="related-title"><?php echo esc_html( $s['section_title'] ); ?></h2><?php endif; ?>
			<div class="related-products-grid" style="display: grid;">
				<?php for ( $i = 0; $i < $max; $i++ ) : ?>
					<div class="related-product-item">
						<div class="product-item-content">
							<div class="product-image-wrapper">
								<div class="dummy-image" style="background:#eee;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;width:100%;min-height:100px;"><i class="eicon-image-bold" style="font-size:48px;color:#ccc;"></i></div>
								<?php if ( 'overlay' === $s['product_title_position'] ) : ?><div class="product-hover-overlay"><h3 class="product-name hover-title">Product Title <?php echo ($i+1); ?></h3></div><?php endif; ?>
							</div>
							<?php if ( 'underneath' === $s['product_title_position'] ) : ?><h3 class="product-name">Product Title <?php echo ($i+1); ?></h3><?php endif; ?>
						</div>
					</div>
				<?php endfor; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<# var section_title = settings.section_title, show_section_title = settings.show_section_title, product_title_position = settings.product_title_position; #>
		<div class="elementor-product-related-wrapper hover-reveal-{{ settings.hover_reveal_effect }}">
			<# if ( 'yes' === show_section_title && section_title ) { #><h2 class="related-title">{{{ section_title }}}</h2><# } #>
			<div class="related-products-grid" style="display: grid;">
				<# var max_posts = parseInt(settings.products_count) || 4;
				for ( var key in settings ) { if ( key.indexOf('products_count_') === 0 && settings[key] ) max_posts = Math.max( max_posts, parseInt(settings[key]) ); }
				for ( var i = 0; i < max_posts; i++ ) { #>
				<div class="related-product-item">
					<div class="product-item-content">
						<div class="product-image-wrapper">
							<div class="dummy-image" style="background:#eee;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;width:100%;min-height:100px;"><i class="eicon-image-bold" style="font-size:48px;color:#ccc;"></i></div>
							<# if ( 'overlay' === product_title_position ) { #><div class="product-hover-overlay"><h3 class="product-name hover-title">Product Title {{ i + 1 }}</h3></div><# } #>
						</div>
						<# if ( 'underneath' === product_title_position ) { #><h3 class="product-name">Product Title {{ i + 1 }}</h3><# } #>
					</div>
				</div>
				<# } #>
			</div>
		</div>
		<style>
			.elementor-element-{{ id }} .hover-reveal-slide-up .product-hover-overlay { transform: translateY(20px); }
			.elementor-element-{{ id }} .product-image-wrapper:hover .product-hover-overlay { transform: translateY(0); opacity: 1; }
			.elementor-element-{{ id }} .hover-reveal-zoom-in .product-hover-overlay { transform: scale(0.8); }
			.elementor-element-{{ id }} .product-image-wrapper:hover .product-hover-overlay { transform: scale(1); opacity: 1; }
		</style>
		<?php
	}
}
