<?php
/**
 * Plugin Name: Tanja Star Rating
 * Description: A plugin where the text Tanja moves and jumps, and you can give her a star rating.
 * Version: 1.1
 * Author: Jules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle form submission on template_redirect hook.
 */
function tsr_handle_submission() {
	if ( isset( $_POST['submit_tanja_rating'] ) ) {
		// Verify Nonce
		if ( ! isset( $_POST['tsr_nonce'] ) || ! wp_verify_nonce( $_POST['tsr_nonce'], 'tsr_submit_rating' ) ) {
			wp_die( 'Security check failed' );
		}

		if ( isset( $_POST['tanja_rating'] ) ) {
			$rating = intval( $_POST['tanja_rating'] );
			$to = 'tanja@rayedgar.com';
			$subject = 'New Rating for Tanja';
			$message = "Tanja received a rating of $rating out of 5 stars.";
			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

			wp_mail( $to, $subject, $message, $headers );

			// Redirect to prevent form resubmission
			$redirect_url = add_query_arg( 'tanja_rated', 'success', wp_get_referer() );
			if ( ! $redirect_url ) {
				$redirect_url = home_url( '/' ); // Fallback
			}
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}
}
add_action( 'template_redirect', 'tsr_handle_submission' );

/**
 * Shortcode [tanja_rating]
 */
function tsr_tanja_rating_shortcode() {
	ob_start();
	?>
	<style>
		.tanja-container {
			height: 200px;
			position: relative;
			overflow: hidden;
			background: #f9f9f9;
			border: 1px solid #ddd;
			margin-bottom: 20px;
		}
		.tanja-mover {
			position: absolute;
			animation: tanja-move 10s infinite linear;
		}
		.tanja-jumper {
			display: inline-block;
			animation: tanja-jump 0.6s infinite ease-in-out;
			font-size: 32px;
			font-weight: bold;
			color: #e91e63;
		}
		@keyframes tanja-move {
			0% { left: 0%; top: 40%; }
			25% { left: 45%; top: 10%; }
			50% { left: 85%; top: 40%; }
			75% { left: 45%; top: 70%; }
			100% { left: 0%; top: 40%; }
		}
		@keyframes tanja-jump {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-40px); }
		}
		.tanja-form {
			padding: 20px;
			border: 1px solid #ccc;
			background: #fff;
			max-width: 300px;
		}
		.star-rating {
			direction: rtl;
			display: inline-block;
			padding: 20px 0;
		}
		.star-rating input {
			display: none;
		}
		.star-rating label {
			color: #bbb;
			font-size: 30px;
			padding: 0;
			cursor: pointer;
			-webkit-transition: all .3s ease-in-out;
			transition: all .3s ease-in-out;
		}
		.star-rating label:hover,
		.star-rating label:hover ~ label,
		.star-rating input:checked ~ label {
			color: #f2b01e;
		}
	</style>

	<div class="tanja-container">
		<div class="tanja-mover">
			<span class="tanja-jumper">Tanja</span>
		</div>
	</div>

	<?php if ( isset( $_GET['tanja_rated'] ) && 'success' === $_GET['tanja_rated'] ) : ?>
		<div class="tanja-success" style="color: green; font-weight: bold; margin-bottom: 20px;">
			Thank you! Your rating has been sent to Tanja.
		</div>
	<?php endif; ?>

	<div class="tanja-form">
		<form method="post" action="">
			<p>Give 1 out of 5 stars to Tanja:</p>
			<?php wp_nonce_field( 'tsr_submit_rating', 'tsr_nonce' ); ?>
			<div class="star-rating">
				<input type="radio" name="tanja_rating" value="5" id="star5"><label for="star5">★</label>
				<input type="radio" name="tanja_rating" value="4" id="star4"><label for="star4">★</label>
				<input type="radio" name="tanja_rating" value="3" id="star3"><label for="star3">★</label>
				<input type="radio" name="tanja_rating" value="2" id="star2"><label for="star2">★</label>
				<input type="radio" name="tanja_rating" value="1" id="star1"><label for="star1">★</label>
			</div>
			<br>
			<input type="submit" name="submit_tanja_rating" value="Send to Tanja" style="background: #e91e63; color: #fff; border: none; padding: 10px 20px; cursor: pointer;">
		</form>
	</div>

	<?php
	return ob_get_clean();
}
add_shortcode( 'tanja_rating', 'tsr_tanja_rating_shortcode' );
