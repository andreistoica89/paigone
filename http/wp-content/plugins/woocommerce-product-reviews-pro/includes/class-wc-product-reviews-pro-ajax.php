<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @package   WC-Product-Reviews-Pro/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * WC Product Reviews Pro AJAX class
 *
 * Handles all AJAX actions
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_AJAX {


	/**
	 * Adds required wp_ajax_* hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_wc_product_reviews_pro_vote',        array( $this, 'cast_vote' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_vote', array( $this, 'cast_vote' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_notify_replies',        array( $this, 'handle_contribution_replies_notifications' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_notify_replies', array( $this, 'handle_contribution_replies_notifications' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_flag',        array( $this, 'flag_contribution' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_flag', array( $this, 'flag_contribution' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_refresh_nonce',        array( $this, 'refresh_nonce' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_refresh_nonce', array( $this, 'refresh_nonce' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_contributions_list',        array( $this, 'contributions_list' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_contributions_list', array( $this, 'contributions_list' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_remove_contribution_attachment', array( $this, 'remove_contribution_attachment' ) );

		// handle AJAX actions for getting review and updating it
		add_action( 'wp_ajax_wc_product_reviews_pro_get_review',		array( $this, 'get_review' ) );
		add_action( 'wp_ajax_wc_product_reviews_pro_get_recent_review',	array( $this, 'get_recent_review' ) );
		add_action( 'wp_ajax_wc_product_reviews_pro_update_review',		array( $this, 'update_review' ) );

		// handle AJAX actions for checking review for guests and getting update confirmation
		add_action( 'wp_ajax_wc_product_reviews_pro_check_review',		  array( $this, 'check_review' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_check_review', array( $this, 'check_review' ) );

		add_action( 'wp_ajax_wc_product_reviews_pro_review_update_confirmation',		array( $this, 'review_update_confirmation' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_review_update_confirmation',	array( $this, 'review_update_confirmation' ) );

		// handle AJAX actions for watching reviews for guests
		add_action( 'wp_ajax_wc_product_reviews_pro_guest_watch',		 array( $this, 'guest_watch' ) );
		add_action( 'wp_ajax_nopriv_wc_product_reviews_pro_guest_watch', array( $this, 'guest_watch' ) );

		// Handle AJAX login & registration via WooCommerce
		add_filter( 'login_errors',                            array( $this, 'ajax_login_error' ), 9999 );
		add_filter( 'woocommerce_login_redirect',              array( $this, 'ajax_login_success' ), 9999 );
		add_filter( 'woocommerce_registration_redirect',       array( $this, 'ajax_registration_success' ), 9999 );
		add_filter( 'woocommerce_process_registration_errors', array( $this, 'record_ajax_registration_errors_start' ), 1 );
		add_filter( 'wp_loaded',                               array( $this, 'ajax_registration_error' ), 20 );
	}


	/**
	 * Verifies AJAX request is valid
	 *
	 * @since 1.0.0
	 * @param string $nonce
	 * @param string $action
	 * @return void|true
	 */
	private function verify_request( $nonce, $action ) {

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			wp_send_json_error( array(
				'message' => __( 'You have taken too long, please go back and try again.', 'woocommerce-product-reviews-pro' )
			) );
		}

		return true;
	}


	/**
	 * Vote for a contribution
	 *
	 * @since 1.0.0
	 */
	public function cast_vote() {

		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// Check that user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array(
				'message' => __( 'You need to be logged in to vote.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Check that the request is valid
		if ( ! isset( $_POST['comment_id'] ) || ! isset( $_POST['vote'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Get contribution
		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

		if ( ! $contribution ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Contribution not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Cast the vote
		$vote_count = $contribution->cast_vote( $_POST['vote'] );

		if ( $vote_count === false ) {
			$message = $contribution->get_failure_message();
			wp_send_json_error( array(
				'message' => $message ? $message : __( 'Could not cast your vote. Please try again later.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Respond with new vote count and message
		wp_send_json_success( array(
			'message'        => __( 'Vote has been cast. Thanks!', 'woocommerce-product-reviews-pro' ),
			'total_votes'    => $contribution->get_vote_count(),
			'positive_votes' => $contribution->get_positive_votes(),
			'negative_votes' => $contribution->get_negative_votes(),
		) );
	}


	/**
	 * Subscribe or unsubscribe to contributions replies notifications
	 *
	 * Callback when a user follows/unfollows a contribution thread
	 *
	 * @since 1.3.0
	 */
	public function handle_contribution_replies_notifications() {

		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		if ( ! isset( $_POST['comment_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Get contribution
		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

		if ( ! $contribution ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Contribution not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		if ( isset( $_POST['manage'] ) && isset( $_POST['user_id'] ) )  {

			$result = wc_product_reviews_pro_add_comment_notification_subscriber( $_POST['manage'], $_POST['user_id'], $contribution );

			if ( ! is_null( $result ) ) {

				if ( 'subscribe' == $_POST['manage'] ) {
					wp_send_json_success( array(
						'message' => __( "You'll be notified when there are replies.", 'woocommerce-product-reviews-pro' ),
					) );
				} elseif ( 'unsubscribe' == $_POST['manage'] ) {
					wp_send_json_success( array(
						'message' => __( 'You are no longer following replies.', 'woocommerce-product-reviews-pro' ),
					) );
				}

			}

		}

		wp_send_json_error( array(
			'message' => __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ),
		) );
	}


	/**
	 * Flag a contribution via AJAX
	 *
	 * @since 1.0.0
	 */
	public function flag_contribution() {

		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// Check that the request is valid
		if ( ! isset( $_POST['comment_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Get contribution
		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

		if ( ! $contribution ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Contribution not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Flag contribution
		$flagged = $contribution->flag( isset( $_POST['reason'] ) ? $_POST['reason'] : '', get_current_user_id() );

		if ( ! $flagged ) {
			$message = $contribution->get_failure_message();
			wp_send_json_error( array(
				'message' => $message ? $message : __( 'Could not flag contribution. Please try again later.', 'woocommerce-product-reviews-pro' )
			) );
		}

		wp_send_json_success( array(
			'message' => __( 'Contribution has been flagged. Thanks!', 'woocommerce-product-reviews-pro' ),
		) );
	}


	/**
	 * Return login success in JSON
	 *
	 * Intercepts WooCommerce login success in case of an AJAX
	 * login request and sends back results in JSON, skipping
	 * the loading of the rest of the page
	 *
	 * @since 1.0.0
	 * @param string $redirect The URI the user should be redirected to upon login.
	 * @return string The URI the user should be redirected to upon login.
	 */
	public function ajax_login_success( $redirect ) {

		if ( isset( $_POST['_wc_product_reviews_pro_ajax_login'] ) ) {
			wp_send_json_success();
		}

		return $redirect;
	}


	/**
	 * Return login error in JSON
	 *
	 * Intercepts WooCommerce login error in case of an AJAX
	 * login request and sends back results in JSON, skipping
	 * the loading of the rest of the page
	 *
	 * @since 1.0.0
	 * @param string $errors The WooCommerce login errors.
	 * @return string The WooCommerce login errors.
	 */
	public function ajax_login_error( $errors ) {

		if ( isset( $_POST['_wc_product_reviews_pro_ajax_login'] ) ) {

			// Format the error(s) for output
			ob_start();
			wc_print_notice( $errors, 'error' );
			$message = ob_get_clean();

			wp_send_json_error( array(
				'message' => $message
			) );

			// make sure no errors are shown on WC pages
			$errors = '';
		}

		return $errors;
	}


	/**
	 * Return registration success in JSON
	 *
	 * Intercepts WooCommerce registration success redirect in case of an AJAX
	 * registration request and sends back results in JSON, skipping
	 * the loading of the rest of the page
	 */
	public function ajax_registration_success( $redirect ) {

		if ( isset( $_POST['_wc_product_reviews_pro_ajax_register'] ) ) {
			wp_send_json_success();
		}

		return $redirect;
	}


	/**
	 * Add a special safety-net error notice before all other
	 * registration errors.
	 *
	 * This is a safety-net in place to handle situations where there
	 * may be error notices added to WC session before registration errors.
	 * The error notices array is later sliced based on the position of this
	 * error notice in `ajax_registration_error` method.
	 */
	public function record_ajax_registration_errors_start( $error ) {

		if ( isset( $_POST['_wc_product_reviews_pro_ajax_register'] ) ) {
			new WP_Error( 200, '_wc_product_reviews_pro_ajax_registration_errors_start' );
		}

		return $error;
	}


	/**
	 * Return login error in JSON
	 *
	 * Intercepts WooCommerce login error in case of an AJAX
	 * login request and sends back results in JSON, skipping
	 * the loading of the rest of the page
	 *
	 * @since 1.0.0
	 */
	public function ajax_registration_error() {

		if ( isset( $_POST['_wc_product_reviews_pro_ajax_register'] ) && wc_notice_count( 'error' ) > 0 ) {

			$all_notices   = WC()->session->get( 'wc_notices', array() );
			$error_notices = $all_notices['error'];

			// Safety net against unwanted error notices not related to registration
			$errors_start = array_search( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . '_wc_product_reviews_pro_ajax_registration_errors_start', $error_notices );

			if ( false !== $errors_start ) {
				$error_notices = array_slice( $error_notices, $errors_start + 1 );
			}

			// Format the error(s) for output
			ob_start();
			foreach ( $error_notices as $notice ) {
				wc_print_notice( $notice, 'error' );
			}
			$message = ob_get_clean();

			// Clear all notices so they don't show up on reload.
			wc_clear_notices();

			// Send JSON error
			wp_send_json_error( array(
				'message' => $message
			) );
		}
	}


	/**
	 * Render contributions list HTML
	 *
	 * @since 1.0.0
	 */
	public function contributions_list() {

		// Bail out if product ID is not provided
		if ( ! isset( $_REQUEST['product_id'] ) ) {
			return;
		}

		global $wp_query;

		query_posts( array(
			'p'            => $_REQUEST['product_id'],
			'post_type'    => 'product',
			'withcomments' => 1,
			'feed'         => 1,
		) );

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				ob_start();
				comments_template( '', true );
				ob_end_clean();

				$filters        = wc_product_reviews_pro_get_current_comment_filters();
				$current_type   = isset( $filters['comment_type'] ) ? $filters['comment_type'] : null;
				$current_rating = isset( $filters['rating'] ) ? $filters['rating'] : null;

				wc_get_template( 'single-product/contributions-list.php', array(
					'comments'       => $wp_query->comments,
					'current_type'   => $current_type,
					'current_rating' => $current_rating,
				) );
			}
		}

		exit;
	}


	/**
	 * Return nonce to an AJAX request
	 *
	 * @since 1.0.0
	 */
	public function refresh_nonce() {
		wp_send_json_success( array(
			'nonce'   => wp_create_nonce( 'wc-product-reviews-pro' ),
			'user_id' => get_current_user_id(),
		) );
	}


	/**
	 * Remove the contribution attachment
	 *
	 * @since 1.0.0
	 */
	public function remove_contribution_attachment() {

		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro-admin' );

		// Bail out if contribution/comment ID is not provided
		if ( ! isset( $_POST['comment_id'] ) || ! $_POST['comment_id'] ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Get contribution
		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

		if ( ! $contribution ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Contribution not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		if ( $contribution->has_attachment() ) {

			// don't use the getter, we need to return an exact URL
			// the getter will fetch the media lib URL if not external
			$attachment_url = $contribution->attachment_url;
			$attachment_id  = $contribution->get_attachment_id();

			if ( $attachment_url ) {
				delete_comment_meta( $contribution->id, 'attachment_url' );
			}

			if ( $attachment_id ) {
				delete_comment_meta( $contribution->id, 'attachment_id' );
				wp_delete_attachment( $attachment_id );
			}

			wp_send_json_success( array(
				'message' => __( 'Attachment successfully removed.', 'woocommerce-product-reviews-pro' )
			) );
		}
	}


	/**
	 * Get the review for editing.
	 *
	 * @since 1.8.0
	 */
	public function get_review() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// bail out if contribution/comment ID is not provided
		if ( empty( $_POST['comment_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if product ID is not provided
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Product not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$review = get_comment( $_POST['comment_id'], ARRAY_A );

		if ( empty( $review ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Review not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		if ( ( ! current_user_can( 'manage_options' ) ) && (int) get_current_user_id() !== (int) $review['user_id'] ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Review not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$review_meta = get_comment_meta( $_POST['comment_id'], '', true );

		$review_data = array(
			'title'			  => trim( $review_meta['title'][0] ),
			'content'		  => $review['comment_content'],
			'rating'		  => ! empty( $review_meta['rating'] ) ? $review_meta['rating'][0] : '',
			'attachment_type' => ! empty( $review_meta['attachment_type'] ) ? $review_meta['attachment_type'][0] : '',
			'attachment_url'  => ! empty( $review_meta['attachment_url'] ) ? $review_meta['attachment_url'][0] : '',
			'attachment_id'	  => ! empty( $review_meta['attachment_id'] ) ? $review_meta['attachment_id'][0] : '',
			'subscribed'	  => 'no',
		);

		if ( ! empty( $review_meta['attachment_id'] ) ) {
			$review_data['attachment_file_url'] = wp_get_attachment_url( $review_meta['attachment_id'][0] );
		}

		if ( ! empty( $review_meta['wc_product_reviews_pro_notify_users'] ) ) {

			$subscribed_users = maybe_unserialize( $review_meta['wc_product_reviews_pro_notify_users'][0] );

			if ( ! empty( $subscribed_users ) && in_array( (int) get_current_user_id(), $subscribed_users, true ) ) {
				$review_data['subscribed'] = 'yes';
			}
		}

		wp_send_json_success( array(
			'review' => $review_data
		) );
	}


	/**
	 * Get the recent review for editing.
	 *
	 * @since 1.8.0
	 */
	public function get_recent_review() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// bail out if product ID is not provided
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Product not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$args = array(
			'number'  => '1',
			'post_id' => $_POST['product_id'],
			'user_id' => get_current_user_id(),
			'type'	  => 'review'
		);

		$recent_reviews = get_comments( $args );

		if ( empty( $recent_reviews ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. No review found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		wp_send_json_success( array(
			'review' => $recent_reviews[0]->comment_ID,
		) );
	}


	/**
	 * Update the review.
	 *
	 * @since 1.8.0
	 */
	public function update_review() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// bail out if contribution/comment ID is not provided
		if ( empty( $_POST['comment_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if product ID is not provided
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Product not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$review = get_comment( $_POST['comment_id'], ARRAY_A );

		if ( empty( $review ) || ( ( ! current_user_can( 'manage_options' ) ) && (int) get_current_user_id() !== (int) $review['user_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Review not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if review content is not provided
		if ( empty( $_POST['review_content'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Review content can\'t be empty.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// checking if user wants to subscribe/unsubscribe
		if ( ! empty( $_POST['subscribed'] ) ) {

			$action	 = ( 'yes' === $_POST['subscribed'] ) ? 'subscribe' : 'unsubscribe';
			$user_id = $review['user_id'];
			$comment = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

			wc_product_reviews_pro_add_comment_notification_subscriber( $action, $user_id, $comment );
		}

		$review_data = $_POST;

		unset( $review_data['action'], $review_data['security'] );

		if ( ! empty( $_FILES ) ) {
			$review_data['files'] = $_FILES['files'];
		}

		$update = wc_product_reviews_pro_update_review_data( $review_data );

		if ( true === $update ) {
			wp_send_json_success( array(
				'message' => __( 'Review updated!', 'woocommerce-product-reviews-pro' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Something went wrong. Please try again!', 'woocommerce-product-reviews-pro' )
			) );
		}
	}


	/**
	 * Checking when Logged-out/Guest user tries to leave a review with an email tied to a registered user.
	 *
	 * @since 1.8.0
	 */
	public function check_review() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// bail out if Email is not provided
		if ( empty( $_POST['email'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if product ID is not provided
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Product not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// Get latest review.
		$args = array(
			'author_email' => $_POST['email'],
			'post_id'	   => $_POST['product_id'],
			'type'		   => 'review',
			'number'	   => '1'
		);

		$reviews = get_comments( $args );

		// checking if review update confirmation is disabled
		if ( count( $reviews ) > 0 && ! wc_product_reviews_pro_review_update_confirmation_enabled() ) {
			wp_send_json_error( array(
				'type'	  => 'mai_disabled',
				'message' => __( 'Whoops, looks like you\'ve already reviewed this product! Please contact the store if you’d like to update your review.', 'woocommerce-product-reviews-pro' )
			) );
		}

		wp_send_json_success( array(
			'count'	  => count( $reviews ),
			'reviews' => $reviews,
		) );
	}


	/**
	 * Sending review update confirmation.
	 *
	 * @since 1.8.0
	 */
	public function review_update_confirmation() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-reviews-pro' );

		// bail out if Email is not provided
		if ( empty( $_POST['email'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if product ID is not provided
		if ( empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request. Product not found.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$user = get_user_by( 'email', $_POST['email'] );

		// checking if user is already registered or a guest
		if ( ! empty( $user ) ) {

			$users	 = array( $user->ID );
			$user_id = $user->ID;
		} else {

			$users	 = array( $_POST['email'] );
			$user_id = $_POST['email'];
		}

		$product = wc_get_product( $_POST['product_id'] );

		$args = array(
			'number'	   => '1',
			'post_id'	   => $_POST['product_id'],
			'author_email' => $_POST['email'],
			'type'		   => 'review'
		);

		$recent_reviews = get_comments( $args );

		// get latest review
		$contribution = wc_product_reviews_pro_get_contribution( $recent_reviews[0] );

		// setting updated comment data in new variable
		$new_review_data = $_POST;

		// nnset unnecessary things
		unset( $new_review_data['action'], $new_review_data['security'] );

		// checking if user has uploaded any files
		if ( ! empty( $_FILES['files'] ) ) {

			// Uploading attachment and get attachment_id.
			$attachment_id = wc_product_reviews_pro_upload_review_attachment( $_FILES['files'] );

			if ( is_wp_error( $attachment_id ) ) {
				wp_send_json_error( array(
					'message' => __( 'Invalid request. File is not updated.', 'woocommerce-product-reviews-pro' )
				) );
			}

			$new_review_data['attachment_id'] = $attachment_id;
		}

		// checking if user has repeated steps multiple times as a guest to update the review
		$previous_review_data = get_comment_meta( $contribution->id, 'new_review_data', true );

		if ( ! empty( $previous_review_data ) && ! empty( $previous_review_data['attachment_id'] ) ) {
			wp_delete_attachment( $previous_review_data['attachment_id'] );
		}

		$new_review_data['comment_id'] = $contribution->id;
		$new_review_data['user_id']	   = $user_id;

		// updating new review data in comment meta
		update_comment_meta( $contribution->id, 'new_review_data', $new_review_data );

		// sending confirmation mail for updating review
		do_action( 'wc_product_reviews_pro_review_update_confirmation_email', $users, $product, $contribution );

		if ( 1 === did_action( 'wc_product_reviews_pro_review_update_confirmation_email' ) ) {
			wp_send_json_success( array(
				'message' => __( 'Confirmation mail sent!', 'woocommerce-product-reviews-pro' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'Something went wrong. Please try again.', 'woocommerce-product-reviews-pro' ),
			) );
		}
	}


	/**
	 * Add guest email to the subscribed list of a review.
	 *
	 * @since 1.8.0
	 */
	public function guest_watch() {

		// verifying security nonce
		$this->verify_request( $_POST['security'], 'wc-product-review-pro-guest' );

		// bail out if contribution/comment ID is not provided
		if ( empty( $_POST['comment_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid request.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// bail out if Email is not provided
		if ( empty( $_POST['email'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Email is required.', 'woocommerce-product-reviews-pro' )
			) );
		}

		$user = get_user_by( 'email', $_POST['email'] );

		// bail out if user is exists in the system.
		if ( ! empty( $user ) ) {
			wp_send_json_error( array(
				'message' => __( 'An account is already registered with your email address. Please login.', 'woocommerce-product-reviews-pro' )
			) );
		}

		// get contribution
		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );

		// subscribe guest for comment notification
		$result = wc_product_reviews_pro_add_comment_notification_subscriber( 'subscribe', $_POST['email'], $contribution, 'guest' );

		if ( ! is_null( $result ) ) {
			wp_send_json_success( array(
				'message' => __( "You'll be notified when there are replies.", 'woocommerce-product-reviews-pro' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ),
			) );
		}
	}


}
