<?php

class WooCartProductBundlesFacade
{
    private $_instance;    
        
    public function __construct()
    {
        $this->_instance = $this->_getInstance();
    } // end __construct
    
    private function _getInstance()
    {
        $name = "woocommerce_bundles";
            
        if (!$this->_isInstanceExistInGlobals($name)) {
            $message = 'woocommerce product bundles instance is not initilized';
            throw new Exception($message);
        }
        
        return $GLOBALS[$name];
    } // end _getInstance
    
    private function _isInstanceExistInGlobals($name)
    {
        return array_key_exists($name, $GLOBALS);
    } // end _isInstanceExistInGlobals
    
    public function isBundledCartItem($cartItem)
    {
        return !empty($cartItem['bundled_by']) && 
               !empty($cartItem['bundled_item_id']);
    } // end isBundledCartItem
}
