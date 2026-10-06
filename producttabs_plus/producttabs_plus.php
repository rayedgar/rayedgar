<?php
/**
 * Plugin Name: ProductTabs Plus
 * Plugin URI: https://example.com/producttabs-plus
 * Description: Custom Product Tabs for WooCommerce with Elementor integration, priority ordering, and targeting rules.
 * Version: 1.0.2
 * Author: Jules
 * Text Domain: producttabs_plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PTP_VERSION', '1.0.2' );
define( 'PTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Init Elementor Widget Integration.
 */
function ptp_init_elementor_widget() {
	if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Widget_Base' ) ) {
		require_once PTP_PLUGIN_DIR . 'includes/class-ptp-elementor-widget.php';
	}
}
add_action( 'plugins_loaded', 'ptp_init_elementor_widget' );

/**
 * Register Custom Post Type for Custom Product Tabs.
 */
function ptp_register_post_type() {
	$labels = array(
		'name'               => _x( 'Custom Product Tabs', 'post type general name', 'producttabs_plus' ),
		'singular_name'      => _x( 'Custom Product Tab', 'post type singular name', 'producttabs_plus' ),
		'menu_name'          => _x( 'Custom Product Tabs', 'admin menu', 'producttabs_plus' ),
		'add_new'            => _x( 'Add New', 'tab', 'producttabs_plus' ),
		'add_new_item'       => __( 'Add New Custom Tab', 'producttabs_plus' ),
		'edit_item'          => __( 'Edit Custom Tab', 'producttabs_plus' ),
		'new_item'           => __( 'New Custom Tab', 'producttabs_plus' ),
		'view_item'          => __( 'View Custom Tab', 'producttabs_plus' ),
		'search_items'       => __( 'Search Custom Tabs', 'producttabs_plus' ),
		'not_found'          => __( 'No custom tabs found', 'producttabs_plus' ),
		'not_found_in_trash' => __( 'No custom tabs found in Trash', 'producttabs_plus' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => 'woocommerce',
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 56,
		'supports'           => array( 'title', 'editor' ),
	);

	register_post_type( 'ptp_custom_tab', $args );
}
add_action( 'init', 'ptp_register_post_type' );

/**
 * Add Meta Box for Custom Tab Settings.
 */
function ptp_add_meta_boxes() {
	add_meta_box(
		'ptp_tab_settings',
		__( 'Custom Product Tab Settings', 'producttabs_plus' ),
		'ptp_meta_box_callback',
		'ptp_custom_tab',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'ptp_add_meta_boxes' );

/**
 * Callback to render the meta box in the admin post editor.
 */
function ptp_meta_box_callback( $post ) {
	wp_nonce_field( 'ptp_save_meta_box', 'ptp_meta_box_nonce' );

	$priority          = get_post_meta( $post->ID, '_ptp_priority', true );
	$categories        = get_post_meta( $post->ID, '_ptp_categories', true );
	$products          = get_post_meta( $post->ID, '_ptp_products', true );
	$display_layout    = get_post_meta( $post->ID, '_ptp_display_layout', true );

	if ( '' === $priority ) {
		$priority = '30';
	}
	if ( '' === $display_layout ) {
		$display_layout = 'standard';
	}

	?>
	<div class="ptp-meta-box-wrapper" style="border: 2px solid #2271b1; padding: 15px; border-radius: 4px; background: #fff;">
		<h3 style="font-weight: bold; color: #1d2327; margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
			<?php esc_html_e( 'DISPLAY LAYOUT SETTING', 'producttabs_plus' ); ?>
		</h3>

		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="ptp_priority" style="font-weight: bold;"><?php esc_html_e( 'Tab Priority / Order', 'producttabs_plus' ); ?></label>
				</th>
				<td>
					<input type="number" id="ptp_priority" name="ptp_priority" value="<?php echo esc_attr( $priority ); ?>" class="small-text" />
					<p class="description"><?php esc_html_e( 'Lower numbers appear first. Description: 10, Additional Info: 20, Reviews: 30.', 'producttabs_plus' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ptp_display_layout" style="font-weight: bold;"><?php esc_html_e( 'Display Layout', 'producttabs_plus' ); ?></label>
				</th>
				<td>
					<select id="ptp_display_layout" name="ptp_display_layout">
						<option value="standard" <?php selected( $display_layout, 'standard' ); ?>><?php esc_html_e( 'Standard Tab', 'producttabs_plus' ); ?></option>
						<option value="stacked" <?php selected( $display_layout, 'stacked' ); ?>><?php esc_html_e( 'Stacked Field', 'producttabs_plus' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select whether to display this as a standard tab header or a stacked content field.', 'producttabs_plus' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ptp_categories" style="font-weight: bold;"><?php esc_html_e( 'Target Product Categories', 'producttabs_plus' ); ?></label>
				</th>
				<td>
					<input type="text" id="ptp_categories" name="ptp_categories" value="<?php echo esc_attr( $categories ); ?>" class="large-text" placeholder="e.g. 12, 15, clothing, shoes" />
					<p class="description"><?php esc_html_e( 'Comma-separated category IDs or slugs. Leave empty to target all products.', 'producttabs_plus' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ptp_products" style="font-weight: bold;"><?php esc_html_e( 'Target Specific Products', 'producttabs_plus' ); ?></label>
				</th>
				<td>
					<input type="text" id="ptp_products" name="ptp_products" value="<?php echo esc_attr( $products ); ?>" class="large-text" placeholder="e.g. 101, 102, SKU123, SKU456" />
					<p class="description"><?php esc_html_e( 'Comma-separated product IDs or SKUs. Leave empty to target all products.', 'producttabs_plus' ); ?></p>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

/**
 * Save Meta Box Data.
 */
function ptp_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['ptp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ptp_meta_box_nonce'], 'ptp_save_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['ptp_priority'] ) ) {
		update_post_meta( $post_id, '_ptp_priority', (int) $_POST['ptp_priority'] );
	}

	if ( isset( $_POST['ptp_display_layout'] ) ) {
		update_post_meta( $post_id, '_ptp_display_layout', sanitize_text_field( $_POST['ptp_display_layout'] ) );
	}

	if ( isset( $_POST['ptp_categories'] ) ) {
		$raw_cats = sanitize_text_field( $_POST['ptp_categories'] );
		$cats_array = array_filter( array_map( 'trim', explode( ',', $raw_cats ) ) );
		update_post_meta( $post_id, '_ptp_categories', implode( ', ', $cats_array ) );
	}

	if ( isset( $_POST['ptp_products'] ) ) {
		$raw_prods = sanitize_text_field( $_POST['ptp_products'] );
		$prods_array = array_filter( array_map( 'trim', explode( ',', $raw_prods ) ) );
		update_post_meta( $post_id, '_ptp_products', implode( ', ', $prods_array ) );
	}
}
add_action( 'save_post_ptp_custom_tab', 'ptp_save_meta_box' );

/**
 * Check if a custom tab matches a product.
 */
function ptp_is_tab_matching_product( $tab_id, $product_id ) {
	$categories_str = get_post_meta( $tab_id, '_ptp_categories', true );
	$products_str   = get_post_meta( $tab_id, '_ptp_products', true );

	// Category check
	if ( ! empty( $categories_str ) ) {
		$categories = array_filter( array_map( 'trim', explode( ',', $categories_str ) ) );
		if ( ! empty( $categories ) ) {
			$match_cat = false;
			foreach ( $categories as $cat ) {
				if ( is_numeric( $cat ) ) {
					if ( has_term( (int) $cat, 'product_cat', $product_id ) ) {
						$match_cat = true;
						break;
					}
				} else {
					if ( has_term( $cat, 'product_cat', $product_id ) ) {
						$match_cat = true;
						break;
					}
				}
			}
			if ( ! $match_cat ) {
				return false;
			}
		}
	}

	// Product check
	if ( ! empty( $products_str ) ) {
		$products = array_filter( array_map( 'trim', explode( ',', $products_str ) ) );
		if ( ! empty( $products ) ) {
			$match_prod = false;
			foreach ( $products as $prod ) {
				if ( (string) $product_id === (string) $prod ) {
					$match_prod = true;
					break;
				}
				if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
					$sku_id = wc_get_product_id_by_sku( $prod );
					if ( $sku_id && (int) $sku_id === (int) $product_id ) {
						$match_prod = true;
						break;
					}
				}
			}
			if ( ! $match_prod ) {
				return false;
			}
		}
	}

	return true;
}

/**
 * Inject Custom Product Tabs into WooCommerce product tabs array.
 */
function ptp_add_custom_product_tabs( $tabs ) {
	global $product, $post;

	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Specs', 'producttabs_plus' );
	}

	$product_id = 0;
	if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) {
		$product_id = $product->get_id();
	} elseif ( isset( $post->ID ) ) {
		$product_id = $post->ID;
	}

	if ( ! $product_id ) {
		return $tabs;
	}

	// Retrieve all custom tab posts manually to prevent excluding items missing meta keys
	$custom_tabs = get_posts( array(
		'post_type'      => 'ptp_custom_tab',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	) );

	if ( empty( $custom_tabs ) ) {
		return $tabs;
	}

	foreach ( $custom_tabs as $tab_post ) {
		if ( ! ptp_is_tab_matching_product( $tab_post->ID, $product_id ) ) {
			continue;
		}

		$priority = get_post_meta( $tab_post->ID, '_ptp_priority', true );
		if ( '' === $priority || false === $priority ) {
			$priority = 30;
		} else {
			$priority = (int) $priority;
		}

		$tab_key = 'ptp_tab_' . $tab_post->ID;

		$tabs[ $tab_key ] = array(
			'title'    => get_the_title( $tab_post->ID ),
			'priority' => $priority,
			'callback' => 'ptp_render_custom_tab_content',
			'post_id'  => $tab_post->ID,
			'content'  => apply_filters( 'the_content', $tab_post->post_content ),
		);
	}

	// Manual PHP sorting using usort or uasort by priority
	uasort( $tabs, function( $a, $b ) {
		$pA = isset( $a['priority'] ) ? (int) $a['priority'] : 30;
		$pB = isset( $b['priority'] ) ? (int) $b['priority'] : 30;
		return $pA - $pB;
	} );

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'ptp_add_custom_product_tabs', 98 );

/**
 * Render Custom Tab Content callback on frontend.
 */
function ptp_render_custom_tab_content( $key, $tab ) {
	$content = '';
	if ( ! empty( $tab['content'] ) ) {
		$content = $tab['content'];
	} elseif ( ! empty( $tab['post_id'] ) ) {
		$post_obj = get_post( $tab['post_id'] );
		if ( $post_obj ) {
			$content = apply_filters( 'the_content', $post_obj->post_content );
		}
	}

	echo '<div class="ptp-tab-content">' . $content . '</div>';
}

/**
 * Convert product attributes to plain text if needed.
 */
function ptp_filter_product_attributes( $product_attributes, $product ) {
	foreach ( $product_attributes as $key => $attribute ) {
		if ( isset( $attribute['value'] ) ) {
			$product_attributes[ $key ]['value'] = wp_strip_all_tags( $attribute['value'] );
		}
	}
	return $product_attributes;
}
add_filter( 'woocommerce_display_product_attributes', 'ptp_filter_product_attributes', 10, 2 );

/**
 * Inject Global CSS for Attribute Alignment and Tab Styling.
 */
function ptp_inject_global_styles() {
	$alignment = get_option( 'ptp_attribute_alignment', 'left' );
	?>
	<style id="ptp-global-styles">
		.woocommerce-product-attributes,
		table.woocommerce-product-attributes,
		.woocommerce-product-attributes th,
		.woocommerce-product-attributes td,
		.woocommerce-product-attributes-item__label,
		.woocommerce-product-attributes-item__value,
		.woocommerce-product-attributes-item__value p {
			text-align: <?php echo esc_attr( $alignment ); ?> !important;
		}
		table.woocommerce-product-attributes,
		table.woocommerce-product-attributes tr,
		table.woocommerce-product-attributes th,
		table.woocommerce-product-attributes td,
		.woocommerce-product-attributes-item {
			background: transparent !important;
			background-color: transparent !important;
			border: none !important;
			border-top: none !important;
			border-bottom: none !important;
			border-left: none !important;
			border-right: none !important;
			box-shadow: none !important;
			outline: none !important;
		}
		table.woocommerce-product-attributes {
			width: 100% !important;
			table-layout: fixed !important;
			border-collapse: collapse !important;
			box-sizing: border-box !important;
		}
		.woocommerce-product-attributes th.woocommerce-product-attributes-item__label {
			width: 35% !important;
			word-break: break-word !important;
			overflow-wrap: break-word !important;
			box-sizing: border-box !important;
			padding: 8px 12px !important;
		}
		.woocommerce-product-attributes td.woocommerce-product-attributes-item__value {
			width: 65% !important;
			word-break: break-word !important;
			overflow-wrap: break-word !important;
			box-sizing: border-box !important;
			padding: 8px 12px !important;
		}
		.woocommerce-product-attributes-item {
			align-items: flex-start !important;
		}
		.ptp-tab-content {
			white-space: normal !important;
			word-wrap: break-word !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'ptp_inject_global_styles' );
