<?php
defined( 'ABSPATH' ) || exit;

$xlwcty_fb_gb_event_fire = get_post_meta( $order_id, "xlwcty_fb_gb_event_fire", true );
if ( $xlwcty_fb_gb_event_fire == "yes" ) {
	return "";
}

$pixel_id            = $this->facebook_pixel_enabled();
$google_analytics_id = $this->google_analytics_enabled();

$facebook_tracking_event = XLWCTY_Core()->data->get_option( 'enable_fb_pageview_event' );
$facebook_purchase_event = XLWCTY_Core()->data->get_option( 'enable_fb_purchase_event' );

$facebook_purchase_event_conversion        = XLWCTY_Core()->data->get_option( 'enable_fb_purchase_event_conversion_val' );
$facebook_purchase_advanced_matching_event = XLWCTY_Core()->data->get_option( 'enable_fb_advanced_matching_event' );
if ( $pixel_id > 0 || $google_analytics_id != "" ) {
	$order     = wc_get_order( $order_id );
	$items     = $order->get_items( 'line_item' );
	$products  = array();
	$gproducts = array();
	$total     = 0;
	if ( XLWCTY_Compatibility::is_wc_version_gte_3_0() ) {
		foreach ( $items as $item ) {
			$pid        = $item->get_product_id();
			$product    = wc_get_product( $pid );
			$item_price = 0;
			if ( $product instanceof WC_product ) {
				$item_price    = $product->get_price();
				$category      = $product->get_category_ids();
				$category_name = "";
				if ( is_array( $category ) && count( $category ) > 0 ) {
					$category_id = $category[0];
					if ( is_numeric( $category_id ) && $category_id > 0 ) {
						$cat_tearm = get_term_by( "id", $category_id, "product_cat" );
						if ( $cat_tearm ) {
							$category_name = $cat_tearm->name;
						}
					}
				}
				$products[ $pid ]  = array(
					"name"       => $product->get_title(),
					"category"   => $category_name,
					"id"         => $pid,
					"quantity"   => $item->get_quantity(),
					"item_price" => $item_price
				);
				$gproducts[ $pid ] = array(
					"id"       => $pid,
					"sku"      => $product->get_sku(),
					"category" => $category_name,
					"name"     => $product->get_title(),
					"quantity" => $item->get_quantity(),
					"price"    => $item_price
				);
			}
		}
	} else {
		foreach ( $items as $item ) {
			$pid = $item['product_id'];

			$product    = wc_get_product( $pid );
			$item_price = 0;
			if ( $product instanceof WC_product ) {
				$item_price = $product->get_price();
				$category   = wp_get_object_terms( $pid, 'product_cat' );

				$category_name = "";
				if ( is_array( $category ) && count( $category ) > 0 ) {
					$category      = current( $category );
					$category_name = $category->name;

				}
				$products[ $pid ]  = array(
					"name"       => $product->get_title(),
					"category"   => $category_name,
					"id"         => $pid,
					"quantity"   => $item['qty'],
					"item_price" => $item_price
				);
				$gproducts[ $pid ] = array(
					"id"       => $pid,
					"sku"      => $product->get_sku(),
					"category" => $category_name,
					"name"     => $product->get_title(),
					"quantity" => $item['qty'],
					"price"    => $item_price
				);
			}
		}
	}

	if ( count( $products ) == 0 ) {
		return "";
	}

	if ( $pixel_id > 0 ) {
		?>
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq)
                    return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq)
                    f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script', '//connect.facebook.net/en_US/fbevents.js');
			<?php
			if ($facebook_purchase_advanced_matching_event == "on") {
			$fb_pa = array();

			$billing_email = XLWCTY_Compatibility::get_order_data( $order, 'billing_email' );
			if ( ! empty( $billing_email ) ) {
				$fb_pa["em"] = $billing_email;
			}

			$billing_phone = XLWCTY_Compatibility::get_order_data( $order, 'billing_phone' );
			if ( ! empty( $billing_phone ) ) {
				$fb_pa["ph"] = $billing_phone;
			}

			$shipping_first_name = XLWCTY_Compatibility::get_order_data( $order, 'shipping_first_name' );
			if ( ! empty( $shipping_first_name ) ) {
				$fb_pa["fn"] = $shipping_first_name;
			}

			$shipping_last_name = XLWCTY_Compatibility::get_order_data( $order, 'shipping_last_name' );
			if ( ! empty( $shipping_last_name ) ) {
				$fb_pa["ln"] = $shipping_last_name;
			}

			$shipping_city = XLWCTY_Compatibility::get_order_data( $order, 'shipping_city' );
			if ( ! empty( $shipping_city ) ) {
				$fb_pa["ct"] = $shipping_city;
			}

			$shipping_state = XLWCTY_Compatibility::get_order_data( $order, 'shipping_state' );
			if ( ! empty( $shipping_state ) ) {
				$fb_pa["st"] = $shipping_state;
			}

			$shipping_postcode = XLWCTY_Compatibility::get_order_data( $order, 'shipping_postcode' );
			if ( ! empty( $shipping_postcode ) ) {
				$fb_pa["zp"] = $shipping_postcode;
			}
			if (count( $fb_pa ) > 0) {
			?>
            fbq('init', '<?php echo $pixel_id; ?>', <?php echo wp_json_encode( $fb_pa ); ?>);
			<?php
			}
			} else {
			?>
            fbq('init', '<?php echo $pixel_id; ?>');
			<?php
			}
			if ($facebook_tracking_event === 'on') {
			?>
            fbq('track', 'PageView');
			<?php
			}

			if ($facebook_purchase_event === 'on') {
			?>
            fbq('track', 'Purchase', {
                contents: <?php echo wp_json_encode( array_values( $products ) ); ?>,
                content_type: 'product',
                value: <?php echo $order->get_total() ?>,
                currency: '<?php echo get_woocommerce_currency(); ?>'
            });
			<?php
			}
			if ($facebook_purchase_event_conversion == "on") {
			?>
            fbq('track', 'Purchase', {'value': '<?php echo $order->get_total() ?>', 'currency': '<?php echo get_woocommerce_currency(); ?>'});
			<?php
			}
			?>

        </script>
		<?php
	}
	if ( $google_analytics_id != "" ) {
		?>
        <script>
            function xlwcty_google_ecommerce() {


                ga('require', 'ecommerce');
                ga('ecommerce:addTransaction', {
                    'id': '<?php echo $order_id ?>',
                    'affiliation': '<?php echo esc_attr( bloginfo( "name" ) ) ?>',
                    'revenue': '<?php echo $order->get_total() ?>',
                    'shipping': '<?php echo XLWCTY_Compatibility::get_order_shipping_total( $order ); ?>',
                    'tax': '<?php echo $order->get_total_tax(); ?>'
                });
				<?php
				foreach ($gproducts as $pro) {
				?>
                ga('ecommerce:addItem', {
                    'id': '<?php echo $pro["id"] ?>',
                    'name': '<?php echo esc_attr( $pro["name"] ); ?>',
                    'sku': '<?php echo esc_attr( $pro["sku"] ) ?>',
                    'category': "<?php echo esc_attr( $pro["category"] ) ?>",
                    'price': '<?php echo $pro["price"] ?>',
                    'quantity': '<?php echo $pro["quantity"] ?>'
                });
				<?php
				}
				?>
                ga('ecommerce:send');
            }

            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');


            ga('create', '<?php echo $google_analytics_id; ?>', 'auto');

            xlwcty_google_ecommerce();


        </script>
		<?php
	}
	update_post_meta( $order_id, "xlwcty_fb_gb_event_fire", "yes" );
}