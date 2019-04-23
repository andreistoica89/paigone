<?php

class WooCommerceCartProButterflyAdapter extends WooCommerceCartProAdapter
{
    public function getProductTitle($product)
    {
        return $product->get_title();
    } // end getProductTitle
    
    public function getID($product)
    {
        return $product->get_id();
    } // end getID
    
    public function getParentID($product)
    {
        return $product->get_id();
    } // end getParentID
    
    public function getProductName($product)
    {
        return $product->get_name();
    } // end getProductName
    
    public function getHookAddToCartFragments()
    {
        return 'woocommerce_add_to_cart_fragments';
    } // end getHookAddToCartFragments
}
