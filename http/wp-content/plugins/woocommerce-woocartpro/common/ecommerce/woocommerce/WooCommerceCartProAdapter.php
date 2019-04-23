<?php

abstract class WooCommerceCartProAdapter
{
    protected $wooInstance = null;
    
    public function &getCartInstance()
    {
        return $GLOBALS['woocommerce']->cart;
    } // end &getCartInstance
    
    public function getCartContentsCount()
    {   
        return $this->getCartInstance()->get_cart_contents_count();
    } // end getCartContentsCount
    
    public function getCalculateTotals()
    {   
        return $this->getCartInstance()->calculate_totals();
    } // end getCalculateTotals
    
    public function getCartContents()
    {
        return $this->getCartInstance()->get_cart();
    } // getCartContents
    
    public function getCartUrl()
    {
        return $this->getCartInstance()->get_cart_url();
    } // end getCartUrl
    
    public function getCartSubtotal()
    {
        return $this->getCartInstance()->get_cart_subtotal();
    } // end getCartSubtotal
    
    public function getCartItemData($item, $flat)
    {
        return $this->getCartInstance()->get_item_data($item, $flat);
    } // end getCartItemData
    
    public function getCartProductPrice($product)
    {
        return $this->getCartInstance()->get_product_price($product);
    } // end getCartProductPrice
    
    public function getCheckoutUrl()
    {
        return $this->getCartInstance()->get_checkout_url();
    } // end getCheckoutUrl
    
    public function setCartQuantity($item, $quantity, $refresh)
    {
        $cart = $this->getCartInstance();
        return $cart->set_quantity($item, $quantity, $refresh);
    } // end setCartQuantity
    
    public function getHookAddToCartFragments()
    {
        throw new UnsupportableWooCartFacadeMethod();
    } // getHookAddToCartFragments
}
