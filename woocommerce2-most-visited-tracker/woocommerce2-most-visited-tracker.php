<?php
/**
 * Plugin Name: WooCommerce2 Most Visited Product Tracker
 * Description: Tracks product views and displays the most visited products in the admin dashboard and via shortcode.
 * Version: 1.0
 * Author: Jules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Track product views.
 */
function w2mv_track_product_view() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}

	global $post;
	$product_id = $post->ID;
	$views = get_post_meta( $product_id, '_product_views_count', true );
	$views = $views ? (int) $views + 1 : 1;
	update_post_meta( $product_id, '_product_views_count', $views );
}
add_action( 'woocommerce_before_single_product', 'w2mv_track_product_view' );

/**
 * Register Admin Menu.
 */
function w2mv_admin_menu() {
	add_menu_page(
		'Most Visited Products',
		'Most Visited',
		'manage_options',
		'most-visited-products',
		'w2mv_admin_dashboard_callback',
		'dashicons-chart-bar',
		25
	);
}
add_action( 'admin_menu', 'w2mv_admin_menu' );

/**
 * Admin Dashboard Callback.
 */
function w2mv_admin_dashboard_callback() {
	$top_products = w2mv_get_top_products();
	?>
	<div class="wrap">
		<h1>Most Visited Products</h1>
		<div style="margin-top: 20px;">
			<?php if ( $top_products ) : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>Product Name</th>
							<th>Views</th>
							<th>Popularity</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$max_views = (int) $top_products[0]->views;
						foreach ( $top_products as $product ) :
							$percentage = $max_views > 0 ? ( (int) $product->views / $max_views ) * 100 : 0;
						?>
							<tr>
								<td><?php echo esc_html( $product->post_title ); ?></td>
								<td><?php echo (int) $product->views; ?></td>
								<td>
									<div style="background: #eee; width: 100%; height: 20px; border-radius: 3px;">
										<div style="background: #0073aa; width: <?php echo (float) $percentage; ?>%; height: 20px; border-radius: 3px;"></div>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top: 20px;">
					<input type="hidden" name="action" value="w2mv_send_report">
					<?php wp_nonce_field( 'w2mv_send_report_nonce' ); ?>
					<input type="submit" class="button button-primary" value="Email Results to Jules">
				</form>
			<?php else : ?>
				<p>No product views tracked yet.</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Get top 10 most visited products.
 */
function w2mv_get_top_products() {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'meta_key'       => '_product_views_count',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	);

	$query = new WP_Query( $args );
	$products = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$products[] = (object) array(
				'post_title' => get_the_title(),
				'views'      => get_post_meta( get_the_ID(), '_product_views_count', true ),
			);
		}
		wp_reset_postdata();
	}

	return $products;
}

/**
 * Shortcode to display top 10 products.
 */
function w2mv_most_visited_shortcode() {
	$top_products = w2mv_get_top_products();
	if ( ! $top_products ) {
		return '<p>No product views tracked yet.</p>';
	}

	$output = '<div class="w2mv-top-products"><h3>Top 10 Most Visited Products</h3><ul>';
	foreach ( $top_products as $product ) {
		$output .= sprintf( '<li>%s (%d views)</li>', esc_html( $product->post_title ), (int) $product->views );
	}
	$output .= '</ul></div>';

	return $output;
}
add_shortcode( 'most_visited_products', 'w2mv_most_visited_shortcode' );

/**
 * Send email report handler.
 */
function w2mv_handle_send_report() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized' );
	}

	check_admin_referer( 'w2mv_send_report_nonce' );

	$top_products = w2mv_get_top_products();
	if ( ! $top_products ) {
		wp_redirect( admin_url( 'admin.php?page=most-visited-products&status=empty' ) );
		exit;
	}

	$to = 'jules2@rayedgar.com';
	$subject = 'Top 10 Most Visited Products Report';
	$message = "Here are the top 10 most visited products:\n\n";

	foreach ( $top_products as $product ) {
		$message .= sprintf( "- %s: %d views\n", $product->post_title, (int) $product->views );
	}

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	wp_mail( $to, $subject, $message, $headers );

	wp_redirect( admin_url( 'admin.php?page=most-visited-products&status=sent' ) );
	exit;
}
add_action( 'admin_post_w2mv_send_report', 'w2mv_handle_send_report' );

/**
 * Activation Hook: Create a page with the shortcode.
 */
function w2mv_activate() {
	$page_title = 'Most Visited Products';
	$page_content = '[most_visited_products]';
	$page_check = get_page_by_title( $page_title );

	if ( ! isset( $page_check->ID ) ) {
		$new_page = array(
			'post_type'    => 'page',
			'post_title'   => $page_title,
			'post_content' => $page_content,
			'post_status'  => 'publish',
			'post_author'  => 1,
		);
		wp_insert_post( $new_page );
	}
}
register_activation_hook( __FILE__, 'w2mv_activate' );
