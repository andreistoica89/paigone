<?php 

interface IWooCartProEcommerceFacade
{
    public function getCart();
    public function getCalculateTotals();
    public function getCartContentsCount();
    public function getCartContents();
    public function getProductTitle($product);
    public function getID($product);
    public function getParentID($product);
    public function getCartUrl();
    public function getCartSubtotal();
    public function getCartItemData($item, $flat = false);
    public function getCartProductPrice($product);
    public function getCheckoutUrl();
    public function setCartQuantity($item, $quantity = 1, $refresh = true);
}
