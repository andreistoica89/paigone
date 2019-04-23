<?php
$ecommerceDir = __DIR__.'/ecommerce/';

if (!class_exists('FestiPluginChild')) {
    require_once __DIR__.'/FestiPluginWithOptionsFilter.php';
}

if (!class_exists('SettingsWooCartFacade')) {
    require_once __DIR__.'/SettingsWooCartFacade.php';
}

if (!class_exists("FestiTeamApiClient")) {
    require_once __DIR__.'/api/FestiTeamApiClient.php';
}

if (!class_exists("WooCartProEcommerceFactory")) {
    require_once $ecommerceDir.'WooCartProEcommerceFactory.php';
}

if (!interface_exists("IWooCartProEcommerceFacade")) {
    require_once $ecommerceDir.'IWooCartProEcommerceFacade.php';
}

if (!class_exists('WooCartProEcommerceFacade')) {
    require_once $ecommerceDir.'/WooCartProEcommerceFacade.php';
}

if (!class_exists('WooCommerceCartProFacade')) {
    require_once $ecommerceDir.'/woocommerce/WooCommerceCartProFacade.php';
}

if (!class_exists('UnsupportableWooCartFacadeMethod')) {
    require_once __DIR__.'/exceptions/UnsupportableWooCartFacadeMethod.php';
}