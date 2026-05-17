<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Woo Product Slider Widget
 */
class Woo_Product_Slider_Widget extends Widget_Base {

	public function get_name() {
		return 'woo-product-slider';
	}

	public function get_title() {
		return esc_html__( 'Woo Product Slider', 'woo-product-slider' );
	}

	public function get_icon() {
		return 'eicon-products-slider';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_script_depends() {
		return [ 'woo-product-slider-js' ];
	}

	public function get_style_depends() {
		return [ 'swiper', 'woo-product-slider-css' ];
	}

	protected function register_controls() {

		// Query Section
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'product_source',
			[
				'label' => esc_html__( 'Source', 'woo-product-slider' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => [
					'latest' => esc_html__( 'Latest Products', 'woo-product-slider' ),
					'featured' => esc_html__( 'Featured Products', 'woo-product-slider' ),
					'sale' => esc_html__( 'Sale Products', 'woo-product-slider' ),
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Product Count', 'woo-product-slider' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__( 'Order By', 'woo-product-slider' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date' => esc_html__( 'Date', 'woo-product-slider' ),
					'title' => esc_html__( 'Title', 'woo-product-slider' ),
					'price' => esc_html__( 'Price', 'woo-product-slider' ),
					'rand' => esc_html__( 'Random', 'woo-product-slider' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'woo-product-slider' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'ASC' => esc_html__( 'ASC', 'woo-product-slider' ),
					'DESC' => esc_html__( 'DESC', 'woo-product-slider' ),
				],
			]
		);

		$this->end_controls_section();

		// Slider Settings Section
		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slides_per_view',
			[
				'label' => esc_html__( 'Slides Per View', 'woo-product-slider' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'woo-product-slider' ),
				'label_off' => esc_html__( 'No', 'woo-product-slider' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__( 'Autoplay Speed (ms)', 'woo-product-slider' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'woo-product-slider' ),
				'label_off' => esc_html__( 'No', 'woo-product-slider' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'woo-product-slider' ),
				'label_off' => esc_html__( 'No', 'woo-product-slider' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_dots',
			[
				'label' => esc_html__( 'Show Dots', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'woo-product-slider' ),
				'label_off' => esc_html__( 'No', 'woo-product-slider' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// Content Section
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => esc_html__( 'Show Image', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Show Title', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_price',
			[
				'label' => esc_html__( 'Show Price', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label' => esc_html__( 'Show Rating', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_add_to_cart',
			[
				'label' => esc_html__( 'Show Add to Cart', 'woo-product-slider' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// Style Section - Product Item
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__( 'Product Item', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_alignment',
			[
				'label' => esc_html__( 'Alignment', 'woo-product-slider' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'woo-product-slider' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'woo-product-slider' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'woo-product-slider' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_background',
			[
				'label' => esc_html__( 'Background Color', 'woo-product-slider' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => esc_html__( 'Padding', 'woo-product-slider' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style Section - Title
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color', 'woo-product-slider' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .product-title',
			]
		);

		$this->end_controls_section();

		// Style Section - Price
		$this->start_controls_section(
			'section_style_price',
			[
				'label' => esc_html__( 'Price', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => esc_html__( 'Color', 'woo-product-slider' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->end_controls_section();

		// Style Section - Button
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Add to Cart Button', 'woo-product-slider' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_add_to_cart' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'woo-product-slider' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .add_to_cart_button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'woo-product-slider' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .add_to_cart_button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .add_to_cart_button',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type'      => 'product',
			'posts_per_page' => $settings['posts_per_page'],
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
		];

		if ( 'featured' === $settings['product_source'] ) {
			$args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			];
		} elseif ( 'sale' === $settings['product_source'] ) {
			$args['post__in'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
		}

		$products_query = new \WP_Query( $args );

		if ( ! $products_query->have_posts() ) {
			return;
		}

		$this->add_render_attribute( 'slider-container', [
			'class' => 'swiper woo-product-slider-container',
			'data-settings' => wp_json_encode( [
				'slidesPerView' => $settings['slides_per_view'],
				'autoplay'      => ( 'yes' === $settings['autoplay'] ),
				'autoplaySpeed' => $settings['autoplay_speed'],
				'loop'          => ( 'yes' === $settings['loop'] ),
				'showArrows'    => ( 'yes' === $settings['show_arrows'] ),
				'showDots'      => ( 'yes' === $settings['show_dots'] ),
			] ),
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'slider-container' ); ?>>
			<div class="swiper-wrapper">
				<?php
				while ( $products_query->have_posts() ) :
					$products_query->the_post();
					global $product;
					$product = wc_get_product( get_the_ID() );
					?>
					<div class="swiper-slide">
						<div class="product-item">
							<?php if ( 'yes' === $settings['show_image'] ) : ?>
								<div class="product-image">
									<?php echo $product->get_image(); ?>
								</div>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_title'] ) : ?>
								<h3 class="product-title"><?php the_title(); ?></h3>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_rating'] ) : ?>
								<div class="product-rating">
									<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
								</div>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_price'] ) : ?>
								<div class="price"><?php echo $product->get_price_html(); ?></div>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_add_to_cart'] ) : ?>
								<div class="add-to-cart">
									<?php
									woocommerce_template_loop_add_to_cart( [
										'class' => 'add_to_cart_button button',
									] );
									?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_dots'] ) : ?>
				<div class="swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();
	}

}
