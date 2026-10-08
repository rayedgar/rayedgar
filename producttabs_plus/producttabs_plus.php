<?php
/**
 * Plugin Name: ProductTabs Plus
 * Version: 1.0.2
 * Author: Jules
 * Text Domain: producttabs_plus
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PTP_VERSION', '1.0.2' );
define( 'PTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function ptp_init_elementor_widget() {
	if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Widget_Base' ) ) {
		require_once PTP_PLUGIN_DIR . 'includes/class-ptp-elementor-widget.php';
	}
}
add_action( 'plugins_loaded', 'ptp_init_elementor_widget' );

function ptp_register_post_type() {
	register_post_type( 'ptp_custom_tab', array(
		'labels' => array(
			'name' => 'Custom Product Tabs',
			'singular_name' => 'Custom Product Tab',
			'add_new_item' => 'Add New Custom Tab',
		),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => 'woocommerce',
		'supports' => array( 'title', 'editor' ),
	) );
}
add_action( 'init', 'ptp_register_post_type' );

function ptp_add_meta_boxes() {
	add_meta_box( 'ptp_tab_settings', 'Custom Product Tab Settings', 'ptp_meta_box_callback', 'ptp_custom_tab', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ptp_add_meta_boxes' );

function ptp_meta_box_callback( $post ) {
	wp_nonce_field( 'ptp_save_meta_box', 'ptp_meta_box_nonce' );
	$priority = get_post_meta( $post->ID, '_ptp_priority', true );
	$categories = get_post_meta( $post->ID, '_ptp_categories', true );
	$products = get_post_meta( $post->ID, '_ptp_products', true );
	$display_layout = get_post_meta( $post->ID, '_ptp_display_layout', true );
	$priority = ( '' === $priority ) ? '30' : $priority;
	$display_layout = ( '' === $display_layout ) ? 'standard' : $display_layout;
	?>
	<div style="border:2px solid #2271b1;padding:15px;background:#fff;">
		<h3 style="margin-top:0;">DISPLAY LAYOUT SETTING</h3>
		<table class="form-table">
			<tr>
				<th><label for="ptp_priority">Tab Priority / Order</label></th>
				<td><input type="number" id="ptp_priority" name="ptp_priority" value="<?php echo esc_attr( $priority ); ?>" class="small-text" /></td>
			</tr>
			<tr>
				<th><label for="ptp_display_layout">Display Layout</label></th>
				<td>
					<select id="ptp_display_layout" name="ptp_display_layout">
						<option value="standard" <?php selected( $display_layout, 'standard' ); ?>>Standard Tab</option>
						<option value="stacked" <?php selected( $display_layout, 'stacked' ); ?>>Stacked Field</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="ptp_categories">Target Product Categories</label></th>
				<td><input type="text" id="ptp_categories" name="ptp_categories" value="<?php echo esc_attr( $categories ); ?>" class="large-text" placeholder="e.g. 12, clothing" /></td>
			</tr>
			<tr>
				<th><label for="ptp_products">Target Specific Products</label></th>
				<td><input type="text" id="ptp_products" name="ptp_products" value="<?php echo esc_attr( $products ); ?>" class="large-text" placeholder="e.g. 101, SKU123" /></td>
			</tr>
		</table>
	</div>
	<?php
}

function ptp_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['ptp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ptp_meta_box_nonce'], 'ptp_save_meta_box' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	if ( isset( $_POST['ptp_priority'] ) ) { update_post_meta( $post_id, '_ptp_priority', (int) $_POST['ptp_priority'] ); }
	if ( isset( $_POST['ptp_display_layout'] ) ) { update_post_meta( $post_id, '_ptp_display_layout', sanitize_text_field( $_POST['ptp_display_layout'] ) ); }
	if ( isset( $_POST['ptp_categories'] ) ) {
		$cats = array_filter( array_map( 'trim', explode( ',', sanitize_text_field( $_POST['ptp_categories'] ) ) ) );
		update_post_meta( $post_id, '_ptp_categories', implode( ', ', $cats ) );
	}
	if ( isset( $_POST['ptp_products'] ) ) {
		$prods = array_filter( array_map( 'trim', explode( ',', sanitize_text_field( $_POST['ptp_products'] ) ) ) );
		update_post_meta( $post_id, '_ptp_products', implode( ', ', $prods ) );
	}
}
add_action( 'save_post_ptp_custom_tab', 'ptp_save_meta_box' );

function ptp_is_tab_matching_product( $tab_id, $product_id ) {
	$cats_str = get_post_meta( $tab_id, '_ptp_categories', true );
	$prods_str = get_post_meta( $tab_id, '_ptp_products', true );
	if ( ! empty( $cats_str ) ) {
		$cats = array_filter( array_map( 'trim', explode( ',', $cats_str ) ) );
		if ( ! empty( $cats ) ) {
			$match = false;
			foreach ( $cats as $cat ) {
				if ( has_term( is_numeric( $cat ) ? (int) $cat : $cat, 'product_cat', $product_id ) ) { $match = true; break; }
			}
			if ( ! $match ) { return false; }
		}
	}
	if ( ! empty( $prods_str ) ) {
		$prods = array_filter( array_map( 'trim', explode( ',', $prods_str ) ) );
		if ( ! empty( $prods ) ) {
			$match = false;
			foreach ( $prods as $p ) {
				if ( (string) $product_id === (string) $p || ( function_exists( 'wc_get_product_id_by_sku' ) && (int) wc_get_product_id_by_sku( $p ) === (int) $product_id ) ) {
					$match = true; break;
				}
			}
			if ( ! $match ) { return false; }
		}
	}
	return true;
}

function ptp_add_custom_product_tabs( $tabs ) {
	global $product, $post;
	if ( isset( $tabs['additional_information'] ) ) { $tabs['additional_information']['title'] = __( 'Specs', 'producttabs_plus' ); }
	$product_id = ( is_object( $product ) && method_exists( $product, 'get_id' ) ) ? $product->get_id() : ( isset( $post->ID ) ? $post->ID : 0 );
	if ( ! $product_id ) { return $tabs; }
	$custom_tabs = get_posts( array( 'post_type' => 'ptp_custom_tab', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
	if ( empty( $custom_tabs ) ) { return $tabs; }
	foreach ( $custom_tabs as $tab_post ) {
		if ( ! ptp_is_tab_matching_product( $tab_post->ID, $product_id ) ) { continue; }
		$priority = get_post_meta( $tab_post->ID, '_ptp_priority', true );
		$priority = ( '' === $priority || false === $priority ) ? 30 : (int) $priority;
		$tabs['ptp_tab_' . $tab_post->ID] = array(
			'title' => get_the_title( $tab_post->ID ),
			'priority' => $priority,
			'callback' => 'ptp_render_custom_tab_content',
			'post_id' => $tab_post->ID,
			'content' => apply_filters( 'the_content', $tab_post->post_content ),
		);
	}
	uasort( $tabs, function( $a, $b ) { return ( (int) ( $a['priority'] ?? 30 ) ) - ( (int) ( $b['priority'] ?? 30 ) ); } );
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'ptp_add_custom_product_tabs', 98 );

function ptp_render_custom_tab_content( $key, $tab ) {
	$content = ! empty( $tab['content'] ) ? $tab['content'] : ( ! empty( $tab['post_id'] ) ? apply_filters( 'the_content', get_post( $tab['post_id'] )->post_content ?? '' ) : '' );
	echo '<div class="ptp-tab-content">' . $content . '</div>';
}

function ptp_filter_product_attributes( $attrs, $product ) {
	foreach ( $attrs as $k => $attr ) {
		if ( isset( $attr['value'] ) ) { $attrs[$k]['value'] = wp_strip_all_tags( $attr['value'] ); }
	}
	return $attrs;
}
add_filter( 'woocommerce_display_product_attributes', 'ptp_filter_product_attributes', 10, 2 );

function ptp_inject_global_styles() {
	$align = get_option( 'ptp_attribute_alignment', 'left' );
	?>
	<style id="ptp-global-styles">
		.woocommerce-product-attributes,table.woocommerce-product-attributes,.woocommerce-product-attributes th,.woocommerce-product-attributes td,.woocommerce-product-attributes-item__label,.woocommerce-product-attributes-item__value,.woocommerce-product-attributes-item__value p{text-align: <?php echo esc_attr($align); ?> !important;}
		table.woocommerce-product-attributes,table.woocommerce-product-attributes tr,table.woocommerce-product-attributes th,table.woocommerce-product-attributes td,.woocommerce-product-attributes-item{background:transparent!important;background-color:transparent!important;border:none!important;box-shadow:none!important;outline:none!important;}
		table.woocommerce-product-attributes{width:100%!important;table-layout:fixed!important;border-collapse:collapse!important;box-sizing:border-box!important;}
		.woocommerce-product-attributes th.woocommerce-product-attributes-item__label{width:35%!important;word-break:break-word!important;overflow-wrap:break-word!important;box-sizing:border-box!important;padding:8px 12px!important;}
		.woocommerce-product-attributes td.woocommerce-product-attributes-item__value{width:65%!important;word-break:break-word!important;overflow-wrap:break-word!important;box-sizing:border-box!important;padding:8px 12px!important;}
		.woocommerce-product-attributes-item{align-items:flex-start!important;}
		.ptp-tab-content{white-space:normal!important;word-wrap:break-word!important;}
	</style>
	<?php
}
add_action( 'wp_head', 'ptp_inject_global_styles' );
