<?php
if (! defined ( 'ABSPATH' ))
	exit ();

/**
 * Plugin Name: Europabank MPI
 * Plugin URI: https://www.europabank.be/
 * Description: Europabank MPI payment gateway for WooCommerce
 * Version: 1.0
 * Author: Europabank NV.
 * Author URI: https://www.europabank.be/
 * Text Domain: woocommerce-europabankmpi
 * Domain Path: /lang
 */

/**
 * Check if WooCommerce is active
 */
if (in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) )) {
	add_action ( 'plugins_loaded', 'init_europabankmpi_gateway' );
	function init_europabankmpi_gateway() {
		add_filter ( 'woocommerce_payment_gateways', 'add_europabankmpi_gateway' );
		function add_europabankmpi_gateway($methods) {
			$methods [] = 'WC_Gateway_EuropabankMpi';
			return $methods;
		}
		
		/**
		 * Europabank MPI Gateway
		 *
		 * Provides a Europabank MPI Payment Gateway.
		 *
		 * @class WC_Gateway_EuropabankMpi
		 * @extends		WC_Payment_Gateway
		 *
		 * @version 1.0
		 * @author Europabank NV.
		 */
		class WC_Gateway_EuropabankMpi extends WC_Payment_Gateway {
			/**
			 * Constructor for the gateway.
			 */
			public function __construct() {
				load_plugin_textdomain ( 'woocommerce-europabankmpi', false, dirname ( plugin_basename ( __FILE__ ) ) . '/lang/' );
				
				$this->id = 'europabankmpi';
				$this->icon = apply_filters ( 'woocommerce_europabankmpi_icon', plugins_url ( 'images/europabank.png', __FILE__ ) );
				$this->method_title = __ ( 'Europabank MPI', 'woocommerce-europabankmpi' );
				$this->method_description = __ ( 'Have your customers pay with Visa, MasterCard, Maestro or iDEAL using the Europabank MPI.', 'woocommerce-europabankmpi' );
				$this->has_fields = false;
				
				$this->init_form_fields ();
				$this->init_settings ();
				
				$this->title = $this->get_option ( 'title' );
				$this->description = $this->get_option ( 'description' );
				$this->brand = $this->get_option ( 'brand' );
				$this->account = $this->get_option ( 'account' );
				$this->uid = $this->get_option ( 'uid' );
				$this->serversecret = $this->get_option ( 'serversecret' );
				$this->feedbacktype = $this->get_option ( 'feedbacktype' );
				$this->redirecttype = $this->get_option ( 'redirecttype' );
				$this->emailfeedback = $this->get_option ( 'emailfeedback' );
				$this->emailcustomer = $this->get_option ( 'emailcustomer' );
				$this->secondchance = $this->get_option ( 'secondchance' );
				$this->css = $this->get_option ( 'css' );
				$this->template = $this->get_option ( 'template' );
				
				add_action ( 'woocommerce_update_options_payment_gateways_europabankmpi', array (
						$this,
						'process_admin_options' 
				) );
				add_action ( 'woocommerce_api_wc_gateway_europabankmpi', array (
						$this,
						'europabankmpi_callback' 
				) );
			}
			
			/**
			 * Initialise Gateway Settings Form Fields
			 */
			public function init_form_fields() {
				$this->form_fields = array (
						'enabled' => array (
								'title' => __ ( 'Enable Europabank MPI', 'woocommerce-europabankmpi' ),
								'type' => 'checkbox',
								'label' => __ ( 'Yes', 'woocommerce-europabankmpi' ),
								'description' => __ ( 'Enable the Europabank MPI payment gateway?', 'woocommerce-europabankmpi' ),
								'default' => 'no',
								'desc_tip' => true 
						),
						'title' => array (
								'title' => __ ( 'Title', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'Payment title that the customer will see on the checkout page.', 'woocommerce-europabankmpi' ),
								'default' => __ ( 'Europabank MPI', 'woocommerce-europabankmpi' ),
								'desc_tip' => true 
						),
						'description' => array (
								'title' => __ ( 'Description', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'Payment description that the customer will see on the checkout page.', 'woocommerce-europabankmpi' ),
								'default' => __ ( 'Pay with Maestro, MasterCard, Visa or iDEAL using the Europabank MPI.', 'woocommerce-europabankmpi' ),
								'desc_tip' => true 
						),
						'brand' => array (
								'title' => __ ( 'Brand', 'woocommerce-europabankmpi' ),
								'type' => 'select',
								'class' => 'wc-enhanced-select',
								'options' => array (
										' ' => __ ( 'All', 'woocommerce-europabankmpi' ),
										'A' => __ ( 'Maestro', 'woocommerce-europabankmpi' ),
										'M' => __ ( 'MasterCard', 'woocommerce-europabankmpi' ),
										'V' => __ ( 'Visa', 'woocommerce-europabankmpi' ),
										'I' => __ ( 'iDEAL', 'woocommerce-europabankmpi' ) 
								),
								'description' => __ ( 'Which brand do you want as default?', 'woocommerce-europabankmpi' ),
								'default' => ' ',
								'desc_tip' => true 
						),
						'account' => array (
								'title' => __ ( 'Account type', 'woocommerce-europabankmpi' ),
								'type' => 'select',
								'class' => 'wc-enhanced-select',
								'options' => array (
										'test' => __ ( 'Test account', 'woocommerce-europabankmpi' ),
										'live' => __ ( 'Live account', 'woocommerce-europabankmpi' ) 
								),
								'description' => __ ( 'Are you using a test- or a live MPI account?', 'woocommerce-europabankmpi' ),
								'default' => 'test',
								'desc_tip' => true 
						),
						'uid' => array (
								'title' => __ ( 'Uid', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'The UID of your MPI account.', 'woocommerce-europabankmpi' ),
								'desc_tip' => true,
								'placeholder' => '9876543210' 
						),
						'serversecret' => array (
								'title' => __ ( 'Server secret', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'The server secret of your MPI account.', 'woocommerce-europabankmpi' ),
								'desc_tip' => true,
								'placeholder' => 'S123456789' 
						),
						'feedbacktype' => array (
								'title' => __ ( 'Feedback type', 'woocommerce-europabankmpi' ),
								'type' => 'select',
								'class' => 'wc-enhanced-select',
								'options' => array (
										'NOFEEDBACK' => __ ( 'No feedback', 'woocommerce-europabankmpi' ),
										'ONLINE' => __ ( 'Online', 'woocommerce-europabankmpi' ),
										'SEMIONLINE' => __ ( 'Semi-online', 'woocommerce-europabankmpi' ),
										'OFFLINE' => __ ( 'Offline', 'woocommerce-europabankmpi' ) 
								),
								'description' => __ ( 'The type of feedback you want to use.', 'woocommerce-europabankmpi' ),
								'default' => 'ONLINE',
								'desc_tip' => true 
						),
						'redirecttype' => array (
								'title' => __ ( 'Redirect type', 'woocommerce-europabankmpi' ),
								'type' => 'select',
								'class' => 'wc-enhanced-select',
								'options' => array (
										'NOREDIRECT' => __ ( 'No redirect', 'woocommerce-europabankmpi' ),
										'DIRECTGET' => __ ( 'Direct', 'woocommerce-europabankmpi' ),
										'INDIRECTGET' => __ ( 'Indirect', 'woocommerce-europabankmpi' ) 
								),
								'description' => __ ( 'The type of redirect you want to use.', 'woocommerce-europabankmpi' ),
								'default' => 'DIRECTGET',
								'desc_tip' => true 
						),
						'emailfeedback' => array (
								'title' => __ ( 'Email feedback', 'woocommerce-europabankmpi' ),
								'type' => 'email',
								'label' => __ ( '', 'woocommerce-europabankmpi' ),
								'description' => __ ( 'To send you an email with the outcome of a transaction', 'woocommerce-europabankmpi' ),
								'default' => '',
								'placeholder' => __ ( 'Optional', 'woocommerce-europabankmpi' ), 
								'desc_tip' => true 
						),
						'emailcustomer' => array (
								'title' => __ ( 'Email customer', 'woocommerce-europabankmpi' ),
								'type' => 'checkbox',
								'label' => __ ( 'Yes', 'woocommerce-europabankmpi' ),
								'description' => __ ( 'Do you want us to send a payment confirmation email to your customer?', 'woocommerce-europabankmpi' ),
								'default' => 'no',
								'desc_tip' => true 
						),
						'secondchance' => array (
								'title' => __ ( 'Second chance', 'woocommerce-europabankmpi' ),
								'type' => 'checkbox',
								'label' => __ ( 'Yes', 'woocommerce-europabankmpi' ),
								'description' => __ ( 'In case of a failed payment, do you want us to send an email to your customer with a link to retry the payment?', 'woocommerce-europabankmpi' ),
								'default' => 'no',
								'desc_tip' => true 
						),
						'css' => array (
								'title' => __ ( 'CSS', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'The url of a CSS file for the payment page.', 'woocommerce-europabankmpi' ),
								'desc_tip' => true,
								'placeholder' => __ ( 'Optional', 'woocommerce-europabankmpi' ) 
						),
						'template' => array (
								'title' => __ ( 'Template', 'woocommerce-europabankmpi' ),
								'type' => 'text',
								'description' => __ ( 'The url of a template file for the payment page.', 'woocommerce-europabankmpi' ),
								'desc_tip' => true,
								'placeholder' => __ ( 'Optional', 'woocommerce-europabankmpi' ) 
						) 
				);
			}
			
			/**
			 * Process the payment and return the result
			 *
			 * @param int $order_id        	
			 * @return array
			 */
			public function process_payment($order_id) {
				require_once ('includes/EuropabankMpiApi.php');
				$order = new WC_Order ( $order_id );
				$parameters = array (
						'mpi_url' => ($this->account === 'live' ? 'https://www.ebonline.be/mpi/authenticate' : 'https://www.ebonline.be/test/mpi/authenticate'),
						'uid' => $this->uid,
						'css' => $this->css,
						'template' => $this->template,
						'beneficiary' => get_bloginfo ( 'name' ),
						'feedbacktype' => $this->feedbacktype,
						'feedbackurl' => WC ()->api_request_url ( 'WC_Gateway_EuropabankMpi' ),
						'feedbackemail' => $this->emailfeedback,
						'redirecttype' => $this->redirecttype,
						'redirecturl' => WC ()->api_request_url ( 'WC_Gateway_EuropabankMpi' ),
						'merchantemail' => ($this->emailcustomer === 'yes' ? get_bloginfo ( 'admin_email' ) : ''),
						'plugin' => 'WooComm',
						'customername' => $order->billing_first_name . ' ' . $order->billing_last_name,
						'country' => $order->billing_country,
						'customeremail' => ($this->emailcustomer === 'yes' || $this->secondchance === 'yes' ? $order->billing_email : ''),
						'language' => substr ( get_bloginfo ( 'language' ), 0, 2 ),
						'brand' => $this->brand,
						'orderid' => $order_id,
						'amount' => round ( $order->get_total () * 100 ),
						'description' => sprintf ( __ ( 'Order %s at %s.', 'woocommerce-europabankmpi' ), $order_id, get_bloginfo ( 'name' ) ),
						'serversecret' => $this->serversecret,
						'emailtype' => ($this->secondchance === 'yes' ? 'SC' : ''),
						'emailfrom' => ($this->secondchance === 'yes' ? get_bloginfo ( 'admin_email' ) : ''),
						'erroremail' => get_bloginfo ( 'admin_email' ) 
				);
				$recurring = false;
				$nonRecurring = false;
				foreach ( $order->get_items () as $item ) {
					$product = $order->get_product_from_item ( $item );
					$recurringProduct = false;
					foreach ( $product->get_attributes () as $attribute ) {
						if ($attribute ['name'] === 'EubRecurring') {
							$recurringProduct = true;
							$values = explode ( ';', $attribute ['value'] );
							if (! isset ( $parameters ['recurringFrequency'] ) || $values [0] < $parameters ['recurringFrequency'])
								$parameters ['recurringFrequency'] = $values [0];
							$date = new DateTime ();
							date_add ( $date, new DateInterval ( 'P' . $values [1] . 'M' ) );
							$dateString = date_format ( $date, 'Ymd' );
							if (! isset ( $parameters ['recurringExpiry'] ) || $dateString > $parameters ['recurringExpiry'])
								$parameters ['recurringExpiry'] = $dateString;
						}
					}
					if ($recurringProduct)
						$recurring = true;
					else
						$nonRecurring = true;
				}
				if ($recurring && $nonRecurring) {
					wc_add_notice ( __ ( 'Your shopping cart contains both recurring and non-recurring products. Please order only 1 type of products at a time.', 'woocommerce-europabankmpi' ), 'error' );
					return array (
							'result' => 'success',
							'redirect' => $order->get_checkout_payment_url () 
					);
				}
				$europabankMpiApi = new EuropabankMpiApi ( $parameters );
				$xmlString = $europabankMpiApi->buildXml ();
				if ($europabankMpiApi->postXml ( $xmlString )) {
					$order->add_order_note ( __ ( 'Customer redirected to Europabank MPI.', 'woocommerce-europabankmpi' ) );
					return array (
							'result' => 'success',
							'redirect' => $europabankMpiApi->getResponseUrl () 
					);
				} else {
					$order->update_status ( 'failed', $europabankMpiApi->getErrorMessage () );
					wc_add_notice ( __ ( 'Europabank MPI error:', 'woocommerce-europabankmpi' ) . $europabankMpiApi->getErrorMessage (), 'error' );
					return array (
							'result' => 'success',
							'redirect' => $order->get_checkout_payment_url () 
					);
				}
			}
			
			/**
			 * Handle feedback and redirect
			 *
			 * @access public
			 * @return void
			 */
			public function europabankmpi_callback() {
				if ($_SERVER ['REQUEST_METHOD'] === 'POST') {
					if (isset ( $_POST ['Status'] ) && isset ( $_POST ['Hash'] ) && isset ( $_POST ['Id'] ) && isset ( $_POST ['Orderid'] )) {
						$order = new WC_Order ( $_POST ['Orderid'] );
						if (isset ( $order->id )) {
							$hash = sha1 ( $_POST ['Id'] . $_POST ['Orderid'] . $this->serversecret );
							if (strtoupper ( $hash ) === strtoupper ( $_POST ['Hash'] )) {
								$message = $this->get_message ();
								if ($_POST ['Status'] === 'AU') {
									$order->add_order_note ( $message );
									$order->payment_complete ();
								} else {
									if($order->status != 'processing') {
										$order->update_status ( 'failed', $message );
									}
								}
								echo ($message . '<br><br>');
								exit ();
							}
						}
					}
				} else if ($_SERVER ['REQUEST_METHOD'] === 'GET') {
					if (isset ( $_GET ['Status'] )) {
						$order = new WC_Order ( $_GET ['Orderid'] );
						if ($_GET ['Status'] === 'AU') {
							wp_safe_redirect ( $this->get_return_url ( $order ) );
						} else {
							wc_add_notice ( sprintf ( __ ( 'Payment not successful: %s', 'woocommerce-europabankmpi' ), $this->get_message () ), 'error' );
							wp_safe_redirect ( $order->get_checkout_payment_url () );
						}
					}
				}
			}
			private function get_message() {
				switch ($_REQUEST ['Status']) {
					case 'AU' :
						switch ($_REQUEST ['Brand']) {
							case 'A' :
								$brand = __ ( 'Maestro', 'woocommerce-europabankmpi' );
								break;
							case 'I' :
								$brand = __ ( 'iDEAL', 'woocommerce-europabankmpi' );
								break;
							case 'M' :
								$brand = __ ( 'MasterCard', 'woocommerce-europabankmpi' );
								break;
							case 'V' :
								$brand = __ ( 'Visa', 'woocommerce-europabankmpi' );
								break;
							default :
								$brand = __ ( 'Unknown brand', 'woocommerce-europabankmpi' );
								break;
						}
						return sprintf ( __ ( '%s payment accepted, refnr: %s.', 'woocommerce-europabankmpi' ), $brand, $_REQUEST ['Refnr'] );
					case 'CA' :
						return __ ( 'Payment cancelled.', 'woocommerce-europabankmpi' );
					case 'DE' :
						return __ ( 'Payment denied.', 'woocommerce-europabankmpi' );
					case 'EX' :
						return __ ( 'Payment encountered exception.', 'woocommerce-europabankmpi' );
					case 'TI' :
						return __ ( 'Payment timed out.', 'woocommerce-europabankmpi' );
					default :
						return __ ( 'Unknown payment status.', 'woocommerce-europabankmpi' );
				}
			}
		}
	}
}