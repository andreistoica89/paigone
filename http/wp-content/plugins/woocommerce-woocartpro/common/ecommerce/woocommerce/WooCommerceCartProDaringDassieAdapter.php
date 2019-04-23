<?php

class WooCommerceCartProDaringDassieAdapter extends WooCommerceCartProAdapter
{
    public function getProductTitle($product)
    {
        return $product->post->post_title;
    } // end getProductTitle
    
    public function getID($product)
    {
        return $product->id;
    } // end getID
    
    public function getParentID($product)
    {
        return $product->ID;
    } // end getParentID
    
    public function getProductName($product)
    {
        return $product->get_title();
    } // end getProductName
    
    public function getHookAddToCartFragments()
    {
        return 'add_to_cart_fragments';
    } // end getHookAddToCartFragments
}
