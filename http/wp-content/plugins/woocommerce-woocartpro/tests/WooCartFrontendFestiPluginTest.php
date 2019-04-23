<?php

require_once dirname(__FILE__).'/WooCartProTestCase.php';

class WooCartFrontendFestiPluginTest extends WooCartProTestCase
{
    /**
     * @ticket 2574
     */
    public function testDisableOptionDisplayCartOnAllPages()
    {
        $this->updateSetting('displayCartOnAllPages', "");
        $this->updateSetting('windowCart', 1);

        $frontend = $this->getFrontendInstance();

        $page = $this->createPage();
        $this->setMainPage($page);

        $wp_query = new WP_Query(
            array('posts_per_page' => -1)
        );

        $wp_query->get_posts();

        $footer = $this->doAction('wp_footer');

        $regExp = '#festi-cart-window-content#Umis';
        $this->assertFalse(
            (bool)preg_match($regExp, $footer),
            'Window cart active but we have expected cart disabled'
        );
    } // end testDisableOptionDisplayCartOnAllPages
    
    /**
     * @ticket 2591
     */
    public function testDisplayCartOnCustomChosenPage()
    {
        $this->updateSetting('displayCartOnAllPages', "");
        $this->updateSetting('windowCart', 1);
        
        $page = $this->createPage();
        $this->updateSetting('festiDisplayCartOnPage', array($page->ID));
        
        $frontend = $this->getFrontendInstance();
        
        $permalink = get_permalink($page->ID);

        $this->go_to($permalink);
        
        $query = $GLOBALS['wp_query'];        
        $query->get_posts();
        
        $footer = $this->doAction('wp_footer');

        $regExp = '#festi-cart-window-content#Umis';
        $this->assertTrue(
            (bool) preg_match($regExp, $footer),
            'Cart is not displayed on chosen page'
        );
    } // end testDisplayCartOnCustomChosenPage

    public function testDisplayBadgeQuantityType()
    {
        $this->updateSetting('LocationInCart', 'right');

        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        $this->assertNotFalse(WC()->cart->add_to_cart($idProduct, 1));

        $this->assertNotFalse(WC()->cart->get_cart_contents_count());

        $fragments = apply_filters("add_to_cart_fragments", array());

        $regExp = "~budgeCounter~";

        $this->assertTrue(
            (bool) preg_match(
                $regExp, $fragments['.festi-cart.festi-cart-widget']
            )
        );
    } // end testDisplayBadgeQuantityType

    public function testDisplayDefaultQuantityType()
    {
        $this->updateSetting('LocationInCart', false);

        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        $this->assertNotFalse(WC()->cart->add_to_cart($idProduct, 1));

        $this->assertNotFalse(WC()->cart->get_cart_contents_count());

        $fragments = apply_filters("add_to_cart_fragments", array());

        $regExp = "~budgeCounter~";

        $this->assertFalse(
            (bool) preg_match(
                $regExp, $fragments['.festi-cart.festi-cart-widget']
            )
        );

    } // end testDisplayDefaultQuantityType
    
    /**
     * Bug 3057
     * @link http://localhost.in.ua/issues/3057
     */
    public function testDisplayQuantitySpinnerInWidget()
    {
        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        $this->assertNotFalse(WC()->cart->add_to_cart($idProduct, 1));

        $this->assertNotFalse(WC()->cart->get_cart_contents_count());

        $fragments = apply_filters("add_to_cart_fragments", array());
        
        $selectorWidget = '.festi-cart-widget-products-content';
        
        $regExp = "#itemQuantity widgetSpinner#Umis";
        
        $isFind = (bool) preg_match($regExp, $fragments[$selectorWidget]);
        
        $this->assertTrue($isFind);
    } // end testDisplayQuantitySpinnerInWidget
    
    /**
     * Bug 3098
     * @link http://localhost.in.ua/issues/3098
     */
    public function testHideContinueText()
    {
        $this->updateSetting('displayPopupContinueButton', 0);
        $this->updateSetting('windowCart', 1);

        $frontend = $this->getFrontendInstance();

        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        WC()->cart->add_to_cart($idProduct, 1);

        WC()->cart->get_cart_contents_count();

        $footer = $this->doAction('wp_footer');
        
        $regExp = '#festi-cart-continue-shopping#Umis';
        $this->assertFalse(
            (bool)preg_match($regExp, $footer),
            'Button "Continue Shopping" shows '.
            'but we have expected button hidden'
        );       
    }
    
    /**
     * Bug 3099
     * @link http://localhost.in.ua/issues/3098
     */
    public function testDisableOptionDisplaySubtotalPrice()
    {
        $this->updateSetting('displayProductListTotal', 0);
        $this->updateSetting('windowCart', 1);

        $frontend = $this->getFrontendInstance();
        
        $page = $this->doCreateProduct();

        $idProduct = $this->getProductId('simple');

        WC()->cart->add_to_cart($idProduct, 1);

        WC()->cart->get_cart_contents_count();
        
        $footer = $this->doAction('wp_footer');
        
        $this->_testIsDisabledOptionDisplaySubtotal($footer);
        
        $fragments = apply_filters("add_to_cart_fragments", array());
        $content = $fragments['.festi-cart-widget-products-content'];
        
        $this->_testIsDisabledOptionDisplaySubtotal($content);
    }
    
    private function _testIsDisabledOptionDisplaySubtotal($content)
    {
        $regExp = '#festi-cart-total subtotal#Umis';
        
        $this->assertFalse(
            (bool) preg_match($regExp, $content),
            'Subtotal price display but we have expected subtotal price hide'
        );
    }

    /**
     * Bug 3139
     * @link http://localhost.in.ua/issues/3139
     */
    public function testIsActiveWoocommerceAdditionalFeesPluginMethod()
    {
        $flag = true;

        $frontend = $this->getFrontendInstance();

        $pluginFolder = 'woocommerce-additional-fees/';
        $pluginMainFile = 'woocommerce_additional_fees_plugin.php';
        $pluginName = $pluginFolder.$pluginMainFile;

        $activePlugins = get_option('active_plugins');
        $activePlugins[] = $pluginName;

        update_option('active_plugins', $activePlugins);

        if (!$frontend->isActiveWoocommerceAdditionalFeesPlugin())
        {
            $flag = false;
        }

        $this->assertTrue($flag);
    } // end testIsActiveWoocommerceAdditionalFeesPluginMethod
}