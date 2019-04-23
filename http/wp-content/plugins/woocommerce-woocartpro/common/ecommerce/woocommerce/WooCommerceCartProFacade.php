<?php

if (!class_exists('WooCommerceCartProAdapter')) {
    require_once __DIR__.'/WooCommerceCartProAdapter.php';
}

class WooCommerceCartProFacade extends WooCartProEcommerceFacade
{
    private static $_instance = null;
    private $_adapter;
    
    public static function &getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    } // end &getInstance
    
    public function __construct()
    {
         if (isset(self::$_instance)) {
            $message = 'Instance already defined ';
            $message .= 'use WooCommerceFacade::getInstance';
            throw new Exception($message);
         }

         $this->_adapter = $this->_createAdapter();
    } // end __construct
    
    private function _createAdapter()
    {   
        $nameVersion = 'Butterfly';
        
        if ($this->_isDaringDassieVersion()) {
            $nameVersion = 'DaringDassie';
        }
        $className = 'WooCommerceCartPro'.$nameVersion.'Adapter';
        
        if (!class_exists($className)) {
            $path = __DIR__.'/'.$className.'.php';
            if (!include_once($path)) {
                throw new Exception(__('Class not found path: '.$path));
            }
        }

        return new $className();
    } // end _createAdapter
    
    private function _isDaringDassieVersion()
    {
        $wooVersion = $this->_getWooCommerceVersionNumber();
        
        return version_compare($wooVersion, '3.0.0', '<');
    } // end _isDaringDassieVersion
    
    private function _getWooCommerceVersionNumber()
    {
        if (!function_exists('get_plugins')) {
            require_once(ABSPATH.'wp-admin/includes/plugin.php');    
        }
        
        $pluginFolder = get_plugins('/'.'woocommerce');
        $pluginFile = 'woocommerce.php';
        
        if (isset($pluginFolder[$pluginFile]['Version'])) {
            return $pluginFolder[$pluginFile]['Version'];
        }
        
        return false;    
    } // end _getWooCommerceVersionNumber
    
    public function getCart()
    {
        return $this->_adapter->getCartInstance();  
    } // end getCart
    
    public function getCalculateTotals()
    {
        return $this->_adapter->getCalculateTotals();
    } // end getCalculateTotals
    
    public function getCartContentsCount()
    {
        return $this->_adapter->getCartContentsCount();
    } // end getCartContentsCount
    
    public function getCartContents()
    {
        return $this->_adapter->getCartContents();
    } // end getCartContents
    
    public function getProductTitle($product)
    {
        if (!array_key_exists('data', $product)) {
            throw new Exception(__("Not found data in product"));
        }
        return $this->_adapter->getProductTitle($product['data']);
    } // end getProductTitle
    
    public function getID($product)
    {
        return $this->_adapter->getID($product);
    } // end getID
    
    public function getParentID($product)
    {
        return $this->_adapter->getParentID($product);
    } // end getParentID
    
    public function getCartUrl()
    {
        return $this->_adapter->getCartUrl();
    } // end getCartUrl
    
    public function getCartSubtotal()
    {
        return $this->_adapter->getCartSubtotal();
    } // end getCartSubtotal
    
    public function getCartItemData($item, $flat = false)
    {
        return $this->_adapter->getCartItemData($item, $flat);
    } // end getCartItemData
    
    public function getCartProductPrice($product)
    {
        return $this->_adapter->getCartProductPrice($product);
    } // end getCartProductPrice
    
    public function getCheckoutUrl()
    {
        return $this->_adapter->getCheckoutUrl();
    } // end getCheckoutUrl
    
    public function setCartQuantity($item, $quantity = 1, $refresh = true)
    {
        return $this->_adapter->setCartQuantity($item, $quantity, $refresh);
    } // end setCartQuantity
    
    public function getProductName($product)
    {
        return $this->_adapter->getProductName($product);
    } // end getProductName
    
    public function getHookAddToCartFragments()
    {
        return $this->_adapter->getHookAddToCartFragments();
    } // end getHookAddToCartFragments
}
