<?php

require_once dirname(__FILE__).'/WooCartProTestCase.php';

class WooCartFestiWidgetTest extends WooCartProTestCase
{
    /**
     * @ticket 3011 http://localhost.in.ua/issues/3011
     */
    public function testWidget()
    {
        $isMethodExist = $this->_isMethodExist('WooCartFestiWidget', 'widget');
        $this->assertTrue($isMethodExist);
    }
    
    private function _isMethodExist($className, $method)
    {    
        $reflection = new ReflectionClass($className);
        
        $currentClassName = $reflection->getMethod($method)->class;
        
        return $currentClassName == $className;
    }
}