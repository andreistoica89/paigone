<?php
defined( 'ABSPATH' ) || exit;

$show_purchase_note = $order_data->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) ); ?>
    <div class="xlwcty_Box xlwcty_order_details_2_col">
		<?php
		echo $this->data->heading ? '<div class="xlwcty_title">' . XLWCTY_Common::maype_parse_merge_tags( $this->data->heading ) . '</div>' : __( 'Order details', 'woocommerce' );
		echo $heading_desc;
		foreach ( $order_data->get_items() as $item_id => $item ) {
			$product = apply_filters( 'woocommerce_order_item_product', XLWCTY_Compatibility::get_product_from_item( $order_data, $item ), $item );

			$is_visible        = $product && $product->is_visible();
			$purchase_note     = XLWCTY_Compatibility::get_purchase_note( $product );
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order_data );

			$image_enable = false;
			if ( $this->data->display_images == "yes" ) {
				$image_enable = true;
			}
			?>
            <div class="xlwcty_pro_list <?php echo ( $image_enable == false ) ? "xlwcty_without_img" : '' ?> xlwcty_clearfix">
                <div class="xlwcty_leftDiv xlwcty_clearfix">
					<?php
					if ( $image_enable ) {
						?>
                        <div class="xlwcty_p_img">
							<?php
							$thumbnail = ( $product ) ? $product->get_image( 'shop_thumbnail' ) : "";
							if ( ! $product_permalink ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
							}
							?>
                            <span class="xlwcty_qty"><?php echo XLWCTY_Compatibility::get_qty_from_item( $order_data, $item ); ?></span>
                        </div>
						<?php
					}
					?>
                    <div class="xlwcty_p_name">
						<?php
						$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order_data );
						if ( $image_enable ) {
							echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s"><span class="xlwcty_t">%s</span></a>', $product_permalink, XLWCTY_Compatibility::get_productname_from_item( $order_data, $item ) ) : XLWCTY_Compatibility::get_productname_from_item( $order_data, $item ), $item, $is_visible );
						} else {
							echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s"><span class="xlwcty_t">%s <span class="xlwcty_inner_qty">&times; <strong>%s</strong></span></span></a>', $product_permalink, XLWCTY_Compatibility::get_productname_from_item( $order_data, $item ), XLWCTY_Compatibility::get_qty_from_item( $order_data, $item ) ) : XLWCTY_Compatibility::get_productname_from_item( $order_data, $item ), $item, $is_visible );
						}

						echo '<div class="xlwcty_info">';
						do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order_data );
						XLWCTY_Compatibility::get_display_item_meta( $order_data, $item );
						XLWCTY_Compatibility::get_display_item_downloads( $order_data, $item );
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order_data );
						echo '</div>';
						?>
                    </div>
                </div>
                <div class="xlwcty_rightDiv"><?php echo $order_data->get_formatted_line_subtotal( $item ); ?></div>
            </div>
			<?php if ( $show_purchase_note && $purchase_note ) : ?>
                <div class="xlwcty_leftDiv xlwcty_clearfix">
                    <div class="xlwcty_p_name"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></div>
                </div>
			<?php
			endif;
		}
		do_action( 'woocommerce_order_items_table', $order_data );
		?>
        <table>
            <tfoot>
			<?php
			foreach ( $order_data->get_order_item_totals() as $key => $total ) {
				?>
                <tr>
                    <th scope="row"><?php echo $total['label']; ?></th>
                    <td><?php echo $total['value']; ?></td>
                </tr>
				<?php
			}
			?>
            </tfoot>
        </table>
		<?php
		do_action( 'woocommerce_before_order_items_below_desc', $order_data );

		echo $after_desc ? $after_desc : '';

		do_action( 'woocommerce_after_order_items_below_desc', $order_data );
		?>
    </div>
<?php
do_action( 'xlwcty_woocommerce_order_details_after_order_table', $order_data );
