<?php

class WooCartProEcommerceFactory
{
    public static function &getInstance()
    {
        return WooCommerceCartProFacade::getInstance();
    } 
}
