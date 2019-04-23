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
 * Product Reviews Pro review update confirmation email
 *
 * Email notifications are sent when Logged-out/Guest user tries to leave a review with an email tied to a registered user
 *
 * @since 1.8.0
 */
class WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation extends WC_Email {


	/** @var WC_Product Product being reviewed */
	private $product = null;

	/** @var WC_Contribution Contribution being updated */
	private $contribution = null;


	/**
	 * Set properties.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		$this->id          = 'wc_product_reviews_pro_review_update_confirmation_email';
		$this->title       = __( 'Review Update Confirmation', 'woocommerce-product-reviews-pro' );
		$this->description = __( 'Email users when they try to leave a review with an email tied to a registered user.', 'woocommerce-product-reviews-pro' );

		$this->template_html  = 'emails/review-update-confirmation.php';
		$this->template_plain = 'emails/plain/review-update-confirmation.php';

		$this->subject = __( 'Review update confirmation needed on a {product_name}', 'woocommerce-product-reviews-pro' );
		$this->heading = __( 'A review update confirmation needed on a {product_name} on {site_title}', 'woocommerce-product-reviews-pro' );

		// find/replace
		$this->find    = array( '{blogname}', '{site_title}' );
		$this->replace = array( $this->get_blogname(), $this->get_blogname() );

		// triggers
		add_action( "{$this->id}_notification", array( $this, 'trigger' ), 10, 3 );

		// call parent constructor
		parent::__construct();
	}


	/**
	 * Is customer email.
	 *
	 * @since 1.8.0
	 * @return true
	 */
	public function is_customer_email() {
		return true;
	}


	/**
	 * Trigger the review update confirmation email.
	 *
	 * @since 1.8.0
	 * @param array $users An array of users IDs
	 * @param \WC_Product $product Product contributed to
	 * @param \WC_Contribution $contribution Original contribution comment
	 */
	public function trigger( $users, $product, $contribution ) {

		if ( ! empty( $users ) && is_array( $users ) && $this->is_enabled() ) {

			foreach ( $users as $user_id ) {

				// flag to send mail for comment/replies
				$send_mail = false;

				// checking if user_id is numeric or an email address
				if ( is_numeric( $user_id ) ) {

					$this->object = get_user_by( 'id', $user_id );

					if ( $this->object instanceof WP_User ) {
						$send_mail = true;
					}

				} elseif ( is_email( $user_id ) ) {

					$send_mail = true;

					$this->object = new stdClass();

					$this->object->user_email   = $user_id;
					$this->object->display_name = $user_id;
					$this->object->ID           = $user_id;
				}

				if ( true === $send_mail ) {

					$this->recipient    = $this->object->user_email;
					$this->product      = $product;
					$this->contribution = $contribution;

					if ( ! $this->contribution instanceof WC_Contribution || ! $this->get_recipient() ) {
						continue;
					}

					$this->find['product-name']	   = '{product_name}';
					$this->replace['product-name'] = $this->product->get_title();

					$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}
	}


	/**
	 * Get email HTML content.
	 *
	 * @since 1.8.0
	 * @return string HTML content
	 */
	public function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
		) );

		return ob_get_clean();
	}


	/**
	 * Get email plain text content.
	 *
	 * @since 1.8.0
	 * @return string Plain text content
	 */
	public function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
		) );

		return ob_get_clean();
	}


}


return new WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation();
