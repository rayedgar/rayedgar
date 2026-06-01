<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs
 * Description: Add custom tabs to your WooCommerce product pages based on display rules.
 * Version: 1.4.7
 * Author: Jules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCPT_VERSION', '1.4.7' );

/**
 * Register Custom Post Type for Product Tabs.
 */
function wcpt_register_post_type() {
	$labels = array(
		'name'               => _x( 'Product Tabs', 'post type general name', 'wcpt' ),
		'singular_name'      => _x( 'Product Tab', 'post type singular name', 'wcpt' ),
		'menu_name'          => _x( 'Product Tabs', 'admin menu', 'wcpt' ),
		'name_admin_bar'     => _x( 'Product Tab', 'add new on admin bar', 'wcpt' ),
		'add_new'            => _x( 'Add New', 'tab', 'wcpt' ),
		'add_new_item'       => __( 'Add New Product Tab', 'wcpt' ),
		'new_item'           => __( 'New Product Tab', 'wcpt' ),
		'edit_item'          => __( 'Edit Product Tab', 'wcpt' ),
		'view_item'          => __( 'View Product Tab', 'wcpt' ),
		'all_items'          => __( 'All Product Tabs', 'wcpt' ),
		'search_items'       => __( 'Search Product Tabs', 'wcpt' ),
		'not_found'          => __( 'No product tabs found.', 'wcpt' ),
		'not_found_in_trash' => __( 'No product tabs found in Trash.', 'wcpt' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => 'woocommerce',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'wc-product-tab' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor' ),
	);

	register_post_type( 'wc_product_tab', $args );
}
add_action( 'init', 'wcpt_register_post_type' );

/**
 * Enqueue Responsive Styles.
 */
function wcpt_enqueue_styles() {
	$global_align = get_option( 'wcpt_attribute_alignment', 'left' );
	?>
	<style type="text/css">
		/* WCPT Version: <?php echo WCPT_VERSION; ?> */
		.wcpt-tab-content-wrapper {
			box-sizing: border-box;
			max-width: 100%;
			overflow-wrap: break-word;
			word-wrap: break-word;
		}
		@media (max-width: 768px) {
			.wcpt-tab-content-wrapper {
				border-left: none !important;
				border-right: none !important;
				padding-left: 10px !important;
				padding-right: 10px !important;
				width: 100% !important;
				display: block !important;
			}
			.wcpt-stacked-field {
				margin-bottom: 20px;
			}
			.woocommerce-product-attributes-item {
				display: flex !important;
				flex-wrap: nowrap !important;
				justify-content: space-between !important;
				align-items: flex-start !important;
				border-bottom: 1px solid #eee !important;
				margin-bottom: 0 !important;
				width: 100% !important;
			}
			.woocommerce-product-attributes-item__label,
			.woocommerce-product-attributes-item__value {
				display: block !important;
				padding: 10px 5px !important;
				background: none !important;
				border: none !important;
				white-space: normal !important;
				margin: 0 !important;
				box-sizing: border-box !important;
				line-height: 1.5 !important;
			}
			.woocommerce-product-attributes-item__label {
				font-weight: bold !important;
				flex-shrink: 0 !important;
				max-width: 50% !important;
			}
			.woocommerce-product-attributes-item__value {
				text-align: right !important;
				flex-grow: 1 !important;
				overflow-wrap: break-word !important;
				min-width: 0 !important;
			}
		}
		/* Global Attribute Alignment */
		.woocommerce-product-attributes.shop_attributes {
			text-align: <?php echo esc_attr( $global_align ); ?>;
		}
		.woocommerce-product-attributes.shop_attributes .woocommerce-product-attributes-item__value {
			text-align: <?php echo esc_attr( $global_align ); ?>;
		}

		/* Global Tab Link Wrapping */
		.woocommerce-tabs ul.tabs li a {
			white-space: normal !important;
			word-wrap: break-word !important;
		}

		/* Responsive Layout Toggles for Elementor Widget */
		.wcpt-tabs-layout, .wcpt-fields-layout { display: none; }

		/* Desktop Default */
		.wcpt-layout-tabs .wcpt-tabs-layout { display: block !important; }
		.wcpt-layout-fields .wcpt-fields-layout { display: block !important; }

		/* Tablet */
		@media (max-width: 1024px) {
			.wcpt-layout-tablet-tabs .wcpt-tabs-layout { display: block !important; }
			.wcpt-layout-tablet-tabs .wcpt-fields-layout { display: none !important; }
			.wcpt-layout-tablet-fields .wcpt-fields-layout { display: block !important; }
			.wcpt-layout-tablet-fields .wcpt-tabs-layout { display: none !important; }
		}

		/* Mobile */
		@media (max-width: 767px) {
			.wcpt-layout-mobile-tabs .wcpt-tabs-layout { display: block !important; }
			.wcpt-layout-mobile-tabs .wcpt-fields-layout { display: none !important; }
			.wcpt-layout-mobile-fields .wcpt-fields-layout { display: block !important; }
			.wcpt-layout-mobile-fields .wcpt-tabs-layout { display: none !important; }
		}

		/* Tab Switching Animations */
		.wcpt-animation-fade .woocommerce-Tabs-panel.wc-tab.wcpt-animate {
			animation: wcptFadeIn 0.4s ease-in-out forwards;
		}
		.wcpt-animation-slide .woocommerce-Tabs-panel.wc-tab.wcpt-animate {
			animation: wcptSlideUp 0.4s ease-in-out forwards;
		}

		@keyframes wcptFadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}
		@keyframes wcptSlideUp {
			from { opacity: 0; transform: translateY(10px); }
			to { opacity: 1; transform: translateY(0); }
		}
	</style>
	<?php
}
add_action( 'wp_head', 'wcpt_enqueue_styles' );

/**
 * Add Meta Box for Tab Settings.
 */
function wcpt_add_meta_boxes() {
	add_meta_box(
		'wcpt_tab_settings',
		__( 'Tab Settings', 'wcpt' ),
		'wcpt_render_meta_box',
		'wc_product_tab',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'wcpt_add_meta_boxes' );

/**
 * Render Meta Box content.
 */
function wcpt_render_meta_box( $post ) {
	wp_nonce_field( 'wcpt_save_meta_box_data', 'wcpt_meta_box_nonce' );

	$display_rule = get_post_meta( $post->ID, '_wcpt_display_rule', true );
	$categories   = get_post_meta( $post->ID, '_wcpt_categories', true );
	$products     = get_post_meta( $post->ID, '_wcpt_products', true );
	$priority     = get_post_meta( $post->ID, '_wcpt_priority', true );

	$line_height  = get_post_meta( $post->ID, '_wcpt_line_height', true );
	$border_color = get_post_meta( $post->ID, '_wcpt_border_color', true );
	$border_width = get_post_meta( $post->ID, '_wcpt_border_width', true );
	$padding      = get_post_meta( $post->ID, '_wcpt_padding', true );

	$display_as   = get_post_meta( $post->ID, '_wcpt_display_as', true );
	$font_size    = get_post_meta( $post->ID, '_wcpt_font_size', true );
	$margin       = get_post_meta( $post->ID, '_wcpt_margin', true );
	$text_align   = get_post_meta( $post->ID, '_wcpt_text_align', true );

	$global_attr_align = get_option( 'wcpt_attribute_alignment', 'left' );

	if ( '' === $priority ) {
		$priority = 10;
	}

	if ( ! is_array( $categories ) ) {
		$categories = array();
	}

	if ( ! is_array( $products ) ) {
		$products = array();
	}

	?>
	<div style="background: #f0f0f1; padding: 15px; border: 1px solid #2271b1; border-left-width: 5px; margin-bottom: 25px; border-radius: 4px;">
		<h3 style="margin: 0 0 10px; color: #2271b1;"><?php _e( 'Display Layout Setting', 'wcpt' ); ?></h3>
		<label for="wcpt_display_as" style="font-weight: bold; display: block; margin-bottom: 8px;"><?php _e( 'How should this item appear?', 'wcpt' ); ?></label>
		<select name="wcpt_display_as" id="wcpt_display_as" class="widefat" style="border-color: #2271b1; font-weight: bold; height: 40px; font-size: 14px;">
			<option value="tab" <?php selected( $display_as, 'tab' ); ?>><?php _e( 'Standard WooCommerce Tab (Horizontal Bar)', 'wcpt' ); ?></option>
			<option value="field" <?php selected( $display_as, 'field' ); ?>><?php _e( 'Stacked Field (Vertical List Underneath)', 'wcpt' ); ?></option>
		</select>
		<p class="description" style="margin-top: 10px; font-style: italic;">
			<?php _e( '<strong>Note:</strong> Standard tabs appear in the WooCommerce tab bar. Stacked fields appear one after another below the main product summary.', 'wcpt' ); ?>
		</p>
	</div>

	<p>
		<label for="wcpt_display_rule"><?php _e( 'Display Rule', 'wcpt' ); ?></label>
		<select name="wcpt_display_rule" id="wcpt_display_rule" class="widefat">
			<option value="all" <?php selected( $display_rule, 'all' ); ?>><?php _e( 'All Products', 'wcpt' ); ?></option>
			<option value="categories" <?php selected( $display_rule, 'categories' ); ?>><?php _e( 'Specific Categories', 'wcpt' ); ?></option>
			<option value="products" <?php selected( $display_rule, 'products' ); ?>><?php _e( 'Specific Products', 'wcpt' ); ?></option>
		</select>
	</p>

	<p id="wcpt_categories_field">
		<label for="wcpt_categories"><?php _e( 'Categories (IDs or Slugs, comma separated)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_categories" id="wcpt_categories" value="<?php echo esc_attr( implode( ',', $categories ) ); ?>" class="widefat">
		<span class="description"><?php _e( 'Example: <code>clothing,15,shoes</code>', 'wcpt' ); ?></span>
	</p>

	<p id="wcpt_products_field">
		<label for="wcpt_products"><?php _e( 'Products (IDs or SKUs, comma separated)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_products" id="wcpt_products" value="<?php echo esc_attr( implode( ',', $products ) ); ?>" class="widefat">
		<span class="description"><?php _e( 'Example: <code>SKU123,101,PROD-ABC</code>', 'wcpt' ); ?></span>
	</p>

	<p>
		<label for="wcpt_priority"><?php _e( 'Priority', 'wcpt' ); ?></label>
		<input type="number" name="wcpt_priority" id="wcpt_priority" value="<?php echo esc_attr( $priority ); ?>" class="widefat">
	</p>

	<hr>
	<h3><?php _e( 'Appearance Settings', 'wcpt' ); ?></h3>

	<p>
		<label for="wcpt_line_height"><?php _e( 'Line Height (e.g. 1.6)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_line_height" id="wcpt_line_height" value="<?php echo esc_attr( $line_height ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_border_width"><?php _e( 'Side Border Thickness (px)', 'wcpt' ); ?></label>
		<input type="number" name="wcpt_border_width" id="wcpt_border_width" value="<?php echo esc_attr( $border_width ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_border_color"><?php _e( 'Side Border Color (hex)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_border_color" id="wcpt_border_color" value="<?php echo esc_attr( $border_color ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_padding"><?php _e( 'Padding (px)', 'wcpt' ); ?></label>
		<input type="number" name="wcpt_padding" id="wcpt_padding" value="<?php echo esc_attr( $padding ); ?>" class="widefat">
	</p>


	<p>
		<label for="wcpt_font_size"><?php _e( 'Font Size (e.g. 16px or 1.2em)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_font_size" id="wcpt_font_size" value="<?php echo esc_attr( $font_size ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_margin"><?php _e( 'Margin (px)', 'wcpt' ); ?></label>
		<input type="number" name="wcpt_margin" id="wcpt_margin" value="<?php echo esc_attr( $margin ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_text_align"><?php _e( 'Text Alignment', 'wcpt' ); ?></label>
		<select name="wcpt_text_align" id="wcpt_text_align" class="widefat">
			<option value="left" <?php selected( $text_align, 'left' ); ?>><?php _e( 'Left', 'wcpt' ); ?></option>
			<option value="center" <?php selected( $text_align, 'center' ); ?>><?php _e( 'Center', 'wcpt' ); ?></option>
			<option value="right" <?php selected( $text_align, 'right' ); ?>><?php _e( 'Right', 'wcpt' ); ?></option>
		</select>
	</p>

	<div style="margin-top: 20px; padding: 10px; border: 1px dashed #ccc;">
		<label for="wcpt_global_attr_align"><?php _e( 'Global Product Info Alignment (Applies to Info Tab)', 'wcpt' ); ?></label>
		<select name="wcpt_global_attr_align" id="wcpt_global_attr_align" class="widefat">
			<option value="left" <?php selected( $global_attr_align, 'left' ); ?>><?php _e( 'Left', 'wcpt' ); ?></option>
			<option value="center" <?php selected( $global_attr_align, 'center' ); ?>><?php _e( 'Center', 'wcpt' ); ?></option>
			<option value="right" <?php selected( $global_attr_align, 'right' ); ?>><?php _e( 'Right', 'wcpt' ); ?></option>
		</select>
		<p class="description"><?php _e( 'This setting applies to the standard WooCommerce Additional Information tab.', 'wcpt' ); ?></p>
	</div>

	<script type="text/javascript">
		(function($) {
			function toggleFields() {
				var rule = $('#wcpt_display_rule').val();
				$('#wcpt_categories_field').toggle(rule === 'categories');
				$('#wcpt_products_field').toggle(rule === 'products');
			}
			$('#wcpt_display_rule').on('change', toggleFields);
			toggleFields();
		})(jQuery);
	</script>
	<?php
}

/**
 * Save Meta Box data.
 */
function wcpt_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['wcpt_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['wcpt_meta_box_nonce'], 'wcpt_save_meta_box_data' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['wcpt_display_rule'] ) ) {
		update_post_meta( $post_id, '_wcpt_display_rule', sanitize_text_field( $_POST['wcpt_display_rule'] ) );
	}

	if ( isset( $_POST['wcpt_categories'] ) ) {
		$cats = array_filter( array_map( 'trim', explode( ',', $_POST['wcpt_categories'] ) ) );
		$cats = array_map( 'sanitize_text_field', $cats );
		update_post_meta( $post_id, '_wcpt_categories', $cats );
	}

	if ( isset( $_POST['wcpt_products'] ) ) {
		$prods = array_filter( array_map( 'trim', explode( ',', $_POST['wcpt_products'] ) ) );
		$prods = array_map( 'sanitize_text_field', $prods );
		update_post_meta( $post_id, '_wcpt_products', $prods );
	}

	if ( isset( $_POST['wcpt_priority'] ) ) {
		update_post_meta( $post_id, '_wcpt_priority', intval( $_POST['wcpt_priority'] ) );
	}

	if ( isset( $_POST['wcpt_line_height'] ) ) {
		update_post_meta( $post_id, '_wcpt_line_height', sanitize_text_field( $_POST['wcpt_line_height'] ) );
	}

	if ( isset( $_POST['wcpt_border_width'] ) ) {
		update_post_meta( $post_id, '_wcpt_border_width', intval( $_POST['wcpt_border_width'] ) );
	}

	if ( isset( $_POST['wcpt_border_color'] ) ) {
		update_post_meta( $post_id, '_wcpt_border_color', sanitize_text_field( $_POST['wcpt_border_color'] ) );
	}

	if ( isset( $_POST['wcpt_padding'] ) ) {
		update_post_meta( $post_id, '_wcpt_padding', intval( $_POST['wcpt_padding'] ) );
	}

	if ( isset( $_POST['wcpt_display_as'] ) ) {
		update_post_meta( $post_id, '_wcpt_display_as', sanitize_text_field( $_POST['wcpt_display_as'] ) );
	}

	if ( isset( $_POST['wcpt_font_size'] ) ) {
		update_post_meta( $post_id, '_wcpt_font_size', sanitize_text_field( $_POST['wcpt_font_size'] ) );
	}

	if ( isset( $_POST['wcpt_margin'] ) ) {
		update_post_meta( $post_id, '_wcpt_margin', intval( $_POST['wcpt_margin'] ) );
	}

	if ( isset( $_POST['wcpt_text_align'] ) ) {
		update_post_meta( $post_id, '_wcpt_text_align', sanitize_text_field( $_POST['wcpt_text_align'] ) );
	}

	if ( isset( $_POST['wcpt_global_attr_align'] ) ) {
		update_option( 'wcpt_attribute_alignment', sanitize_text_field( $_POST['wcpt_global_attr_align'] ) );
	}
}
add_action( 'save_post', 'wcpt_save_meta_box_data' );

/**
 * Filter WooCommerce Product Tabs.
 */
function wcpt_product_tabs( $tabs ) {
	global $product;

	if ( ! $product ) {
		return $tabs;
	}

	$product_id = $product->get_id();
	$args = array(
		'post_type'      => 'wc_product_tab',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$custom_tabs = get_posts( $args );

	// Manual sort by priority to avoid missing posts without priority meta
	usort( $custom_tabs, function( $a, $b ) {
		$pA = get_post_meta( $a->ID, '_wcpt_priority', true );
		$pB = get_post_meta( $b->ID, '_wcpt_priority', true );
		$pA = ( '' === $pA ) ? 10 : (int) $pA;
		$pB = ( '' === $pB ) ? 10 : (int) $pB;
		return $pA - $pB;
	} );

	foreach ( $custom_tabs as $tab_post ) {
		$display_as = get_post_meta( $tab_post->ID, '_wcpt_display_as', true );
		if ( 'field' === $display_as ) {
			continue;
		}

		$display_rule = get_post_meta( $tab_post->ID, '_wcpt_display_rule', true );
		$priority     = get_post_meta( $tab_post->ID, '_wcpt_priority', true );
		$should_show  = false;

		if ( 'all' === $display_rule ) {
			$should_show = true;
		} elseif ( 'categories' === $display_rule ) {
			$categories = get_post_meta( $tab_post->ID, '_wcpt_categories', true );
			if ( is_array( $categories ) ) {
				foreach ( $categories as $cat ) {
					if ( has_term( $cat, 'product_cat', $product_id ) ) {
						$should_show = true;
						break;
					}
				}
			}
		} elseif ( 'products' === $display_rule ) {
			$products = get_post_meta( $tab_post->ID, '_wcpt_products', true );
			if ( is_array( $products ) ) {
				if ( in_array( (string) $product_id, $products ) || in_array( $product->get_sku(), $products ) ) {
					$should_show = true;
				}
			}
		}

		if ( $should_show ) {
			$tabs[ 'wcpt_tab_' . $tab_post->ID ] = array(
				'title'    => apply_filters( 'the_title', $tab_post->post_title ),
				'priority' => (int) $priority,
				'callback' => 'wcpt_render_tab_content',
				'content'  => $tab_post->post_content, // Pass content for callback
				'styles'   => array(
					'line_height'  => get_post_meta( $tab_post->ID, '_wcpt_line_height', true ),
					'border_color' => get_post_meta( $tab_post->ID, '_wcpt_border_color', true ),
					'border_width' => get_post_meta( $tab_post->ID, '_wcpt_border_width', true ),
					'padding'      => get_post_meta( $tab_post->ID, '_wcpt_padding', true ),
					'font_size'    => get_post_meta( $tab_post->ID, '_wcpt_font_size', true ),
					'margin'       => get_post_meta( $tab_post->ID, '_wcpt_margin', true ),
					'text_align'   => get_post_meta( $tab_post->ID, '_wcpt_text_align', true ),
				),
			);
		}
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'wcpt_product_tabs' );

/**
 * Render Tab Content.
 */
function wcpt_render_tab_content( $key, $tab ) {
	$style_attr = '';
	if ( ! empty( $tab['styles'] ) ) {
		$styles = $tab['styles'];
		$css    = array();

		if ( ! empty( $styles['line_height'] ) ) {
			$css[] = 'line-height: ' . esc_attr( $styles['line_height'] ) . ';';
		}

		if ( ! empty( $styles['border_width'] ) ) {
			$width = intval( $styles['border_width'] ) . 'px';
			$color = ! empty( $styles['border_color'] ) ? esc_attr( $styles['border_color'] ) : '#ccc';
			$css[] = "border-left: $width solid $color;";
			$css[] = "border-right: $width solid $color;";
		}

		if ( ! empty( $styles['padding'] ) ) {
			$css[] = 'padding: ' . intval( $styles['padding'] ) . 'px;';
		}

		if ( ! empty( $styles['font_size'] ) ) {
			$css[] = 'font-size: ' . esc_attr( $styles['font_size'] ) . ';';
		}

		if ( ! empty( $styles['margin'] ) ) {
			$css[] = 'margin: ' . intval( $styles['margin'] ) . 'px 0;';
		}

		if ( ! empty( $styles['text_align'] ) ) {
			$css[] = 'text-align: ' . esc_attr( $styles['text_align'] ) . ';';
		}

		if ( ! empty( $css ) ) {
			$style_attr = ' style="' . implode( ' ', $css ) . '"';
		}
	}

	echo '<div class="wcpt-tab-content-wrapper"' . $style_attr . '>';
	echo apply_filters( 'the_content', $tab['content'] );
	echo '</div>';
}

/**
 * Remove links from product attributes in the "Additional Information" tab.
 */
function wcpt_remove_attribute_links( $product_attributes, $product ) {
	foreach ( $product_attributes as &$attribute ) {
		if ( isset( $attribute['value'] ) ) {
			$attribute['value'] = wp_strip_all_tags( $attribute['value'] );
		}
	}
	return $product_attributes;
}
add_filter( 'woocommerce_display_product_attributes', 'wcpt_remove_attribute_links', 10, 2 );

/**
 * Render stacked fields underneath the product summary.
 */
function wcpt_render_stacked_fields() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$product_id = $product->get_id();
	$args = array(
		'post_type'      => 'wc_product_tab',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	$custom_tabs = get_posts( $args );

	// Manual sort by priority to avoid missing posts without priority meta
	usort( $custom_tabs, function( $a, $b ) {
		$pA = get_post_meta( $a->ID, '_wcpt_priority', true );
		$pB = get_post_meta( $b->ID, '_wcpt_priority', true );
		$pA = ( '' === $pA ) ? 10 : (int) $pA;
		$pB = ( '' === $pB ) ? 10 : (int) $pB;
		return $pA - $pB;
	} );

	foreach ( $custom_tabs as $tab_post ) {
		$display_as = get_post_meta( $tab_post->ID, '_wcpt_display_as', true );
		if ( 'field' !== $display_as ) {
			continue;
		}

		$display_rule = get_post_meta( $tab_post->ID, '_wcpt_display_rule', true );
		$should_show  = false;

		if ( 'all' === $display_rule ) {
			$should_show = true;
		} elseif ( 'categories' === $display_rule ) {
			$categories = get_post_meta( $tab_post->ID, '_wcpt_categories', true );
			if ( is_array( $categories ) ) {
				foreach ( $categories as $cat ) {
					if ( has_term( $cat, 'product_cat', $product_id ) ) {
						$should_show = true;
						break;
					}
				}
			}
		} elseif ( 'products' === $display_rule ) {
			$products = get_post_meta( $tab_post->ID, '_wcpt_products', true );
			if ( is_array( $products ) ) {
				if ( in_array( (string) $product_id, $products ) || in_array( $product->get_sku(), $products ) ) {
					$should_show = true;
				}
			}
		}

		if ( $should_show ) {
			$styles = array(
				'line_height'  => get_post_meta( $tab_post->ID, '_wcpt_line_height', true ),
				'border_color' => get_post_meta( $tab_post->ID, '_wcpt_border_color', true ),
				'border_width' => get_post_meta( $tab_post->ID, '_wcpt_border_width', true ),
				'padding'      => get_post_meta( $tab_post->ID, '_wcpt_padding', true ),
				'font_size'    => get_post_meta( $tab_post->ID, '_wcpt_font_size', true ),
				'margin'       => get_post_meta( $tab_post->ID, '_wcpt_margin', true ),
				'text_align'   => get_post_meta( $tab_post->ID, '_wcpt_text_align', true ),
			);

			$tab_data = array(
				'content' => $tab_post->post_content,
				'styles'  => $styles,
			);

			echo '<div class="wcpt-stacked-field">';
			echo '<h3>' . apply_filters( 'the_title', $tab_post->post_title ) . '</h3>';
			wcpt_render_tab_content( 'wcpt_tab_' . $tab_post->ID, $tab_data );
			echo '</div>';
		}
	}
}
add_action( 'woocommerce_after_single_product_summary', 'wcpt_render_stacked_fields', 15 );

/**
 * Register Elementor Widget.
 */
function wcpt_register_elementor_widget( $widgets_manager ) {
	require_once __DIR__ . '/elementor-widget.php';
	$widgets_manager->register( new \WCPT_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'wcpt_register_elementor_widget' );
