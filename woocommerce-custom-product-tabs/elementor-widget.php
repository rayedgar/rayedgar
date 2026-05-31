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
			'section_style',
			[
				'label' => __( 'Style', 'wcpt' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_alignment',
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
					'{{WRAPPER}} .wcpt-stacked-field h3' => 'text-align: {{VALUE}};',
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

		$product_id = $product->get_id();
		$args = array(
			'post_type'      => 'wc_product_tab',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$custom_tabs = get_posts( $args );

		// Manual sort by priority
		usort( $custom_tabs, function( $a, $b ) {
			$pA = get_post_meta( $a->ID, '_wcpt_priority', true );
			$pB = get_post_meta( $b->ID, '_wcpt_priority', true );
			$pA = ( '' === $pA ) ? 10 : (int) $pA;
			$pB = ( '' === $pB ) ? 10 : (int) $pB;
			return $pA - $pB;
		} );

		$valid_tabs  = [];

		foreach ( $custom_tabs as $tab_post ) {
			$display_rule = get_post_meta( $tab_post->ID, '_wcpt_display_rule', true );
			$should_show  = false;

			if ( 'all' === $display_rule ) {
				$should_show = true;
			} elseif ( 'categories' === $display_rule ) {
				$categories = get_post_meta( $tab_post->ID, '_wcpt_categories', true );
				if ( is_array( $categories ) && has_term( $categories, 'product_cat', $product_id ) ) {
					$should_show = true;
				}
			} elseif ( 'products' === $display_rule ) {
				$products = get_post_meta( $tab_post->ID, '_wcpt_products', true );
				if ( is_array( $products ) && in_array( $product_id, $products ) ) {
					$should_show = true;
				}
			}

			if ( $should_show ) {
				$valid_tabs[] = [
					'id'      => $tab_post->ID,
					'title'   => apply_filters( 'the_title', $tab_post->post_title ),
					'content' => $tab_post->post_content,
					'styles'  => [
						'line_height'  => get_post_meta( $tab_post->ID, '_wcpt_line_height', true ),
						'border_color' => get_post_meta( $tab_post->ID, '_wcpt_border_color', true ),
						'border_width' => get_post_meta( $tab_post->ID, '_wcpt_border_width', true ),
						'padding'      => get_post_meta( $tab_post->ID, '_wcpt_padding', true ),
						'font_size'    => get_post_meta( $tab_post->ID, '_wcpt_font_size', true ),
						'margin'       => get_post_meta( $tab_post->ID, '_wcpt_margin', true ),
						'text_align'   => get_post_meta( $tab_post->ID, '_wcpt_text_align', true ),
					],
				];
			}
		}

		if ( empty( $valid_tabs ) ) {
			return;
		}

		if ( 'tabs' === $layout ) {
			$this->render_tabs( $valid_tabs );
		} else {
			$this->render_fields( $valid_tabs );
		}
	}

	protected function render_tabs( $tabs ) {
		?>
		<div class="woocommerce-tabs wc-tabs-wrapper">
			<ul class="tabs wc-tabs" role="tablist">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<li class="<?php echo $index === 0 ? 'active' : ''; ?>" id="tab-title-wcpt-<?php echo $tab['id']; ?>" role="tab">
						<a href="#tab-wcpt-<?php echo $tab['id']; ?>"><?php echo esc_html( $tab['title'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--wcpt-<?php echo $tab['id']; ?> panel entry-content wc-tab" id="tab-wcpt-<?php echo $tab['id']; ?>" role="tabpanel" style="<?php echo $index === 0 ? 'display: block;' : 'display: none;'; ?>">
					<?php wcpt_render_tab_content( 'wcpt_tab_' . $tab['id'], $tab ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function render_fields( $fields ) {
		foreach ( $fields as $field ) {
			echo '<div class="wcpt-stacked-field">';
			echo '<h3>' . esc_html( $field['title'] ) . '</h3>';
			wcpt_render_tab_content( 'wcpt_tab_' . $field['id'], $field );
			echo '</div>';
		}
	}
}
