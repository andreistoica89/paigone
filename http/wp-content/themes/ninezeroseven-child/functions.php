<?php
// Shortcode to output product reviews
function review_stars( $atts ) {
    
    $a = shortcode_atts( array(
        'product_id' => '350',
    ), $atts );
    $product_id = $a['product_id'];
    $product = wc_get_product( $product_id );
    $rating_count = $product->get_rating_count();
    $review_count = $product->get_review_count();
    $average      = $product->get_average_rating();
    $url = get_permalink( $product_id );
    
    if ( $rating_count > 0 ) : 
    $output = wc_get_rating_html( $average, $rating_count );
    $output .= '<a href="'.get_permalink($product_id).'#reviews">Gebaseerd op '.$review_count.' beoordelingen van klanten</a>';
    $output .= "</a>";
    return $output;
    else :
    $output = '<a href="'.get_permalink($product_id).'#reviews">Wees de eerste die een commentaar achterlaat</a>';
    return $output;
    endif;
}
add_shortcode( 'review-stars-output', 'review_stars');

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab

    return $tabs;

}
// Désactive les Produits Apparentés / related products des fiches produits
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products',20);



?>
