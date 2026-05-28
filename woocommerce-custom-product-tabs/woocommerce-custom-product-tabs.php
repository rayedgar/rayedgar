<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs
 * Description: Add custom tabs to your WooCommerce product pages based on display rules.
 * Version: 1.0
 * Author: Jules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
		'show_in_menu'       => 'edit.php?post_type=product',
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
	<p>
		<label for="wcpt_display_rule"><?php _e( 'Display Rule', 'wcpt' ); ?></label>
		<select name="wcpt_display_rule" id="wcpt_display_rule" class="widefat">
			<option value="all" <?php selected( $display_rule, 'all' ); ?>><?php _e( 'All Products', 'wcpt' ); ?></option>
			<option value="categories" <?php selected( $display_rule, 'categories' ); ?>><?php _e( 'Specific Categories', 'wcpt' ); ?></option>
			<option value="products" <?php selected( $display_rule, 'products' ); ?>><?php _e( 'Specific Products', 'wcpt' ); ?></option>
		</select>
	</p>

	<p id="wcpt_categories_field">
		<label for="wcpt_categories"><?php _e( 'Categories (IDs, comma separated)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_categories" id="wcpt_categories" value="<?php echo esc_attr( implode( ',', $categories ) ); ?>" class="widefat">
	</p>

	<p id="wcpt_products_field">
		<label for="wcpt_products"><?php _e( 'Products (IDs, comma separated)', 'wcpt' ); ?></label>
		<input type="text" name="wcpt_products" id="wcpt_products" value="<?php echo esc_attr( implode( ',', $products ) ); ?>" class="widefat">
	</p>

	<p>
		<label for="wcpt_priority"><?php _e( 'Priority', 'wcpt' ); ?></label>
		<input type="number" name="wcpt_priority" id="wcpt_priority" value="<?php echo esc_attr( $priority ); ?>" class="widefat">
	</p>

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
		$cats = array_filter( array_map( 'intval', explode( ',', $_POST['wcpt_categories'] ) ) );
		update_post_meta( $post_id, '_wcpt_categories', $cats );
	}

	if ( isset( $_POST['wcpt_products'] ) ) {
		$prods = array_filter( array_map( 'intval', explode( ',', $_POST['wcpt_products'] ) ) );
		update_post_meta( $post_id, '_wcpt_products', $prods );
	}

	if ( isset( $_POST['wcpt_priority'] ) ) {
		update_post_meta( $post_id, '_wcpt_priority', intval( $_POST['wcpt_priority'] ) );
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

	foreach ( $custom_tabs as $tab_post ) {
		$display_rule = get_post_meta( $tab_post->ID, '_wcpt_display_rule', true );
		$priority     = get_post_meta( $tab_post->ID, '_wcpt_priority', true );
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
			$tabs[ 'wcpt_tab_' . $tab_post->ID ] = array(
				'title'    => apply_filters( 'the_title', $tab_post->post_title ),
				'priority' => (int) $priority,
				'callback' => 'wcpt_render_tab_content',
				'content'  => $tab_post->post_content, // Pass content for callback
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
	echo apply_filters( 'the_content', $tab['content'] );
}
