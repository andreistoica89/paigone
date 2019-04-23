<?php
require_once dirname(__FILE__).'/WooCartProTestCase.php';

class WooCartBackendFestiPluginTest extends WooCartProTestCase
{
    private $_backend;
    
    public function setUp()
    {
        parent::setUp();
       
        $this->_backend = $this->getBackendInstance();
        
    } // end setUp
    
    public function testGetSettings()
    {
        $backend = $this->getBackendInstance();
        $settings = $backend->getSettings();

        $this->assertTrue(!empty($settings));
    } // end testGetSettings

    /**
     * @ticket 2586
     * @ticket 3068
     * @link http://localhost.in.ua/issues/3068
     */
    public function testDoUpdateIconSize()
    {
        $this->_testCheckPremissionFolder();
        $this->_testCheckOnEmptyFile();
        $this->_testCheckOnDisallowedFileType();
        
    } // end testDoUpdateIconSize
    
    private function _testCheckPremissionFolder()
    {
        $userIconPath = $this->_getUserIconPath();
        $defaultIconPath = $this->_getDefaultIconPath();
        $file = 'test.png';

        if (!is_dir($userIconPath)) {
            mkdir($userIconPath);
        }
        chmod($userIconPath, 0444);

        $vars = array(
            'defaultIconPath' => $defaultIconPath . 'icon1.png',
            'userIconPath'    => $userIconPath . $file,
        );
        
        $code = WooCartBackendFestiPlugin::WOOCARTPRO_ERROR_CODE_PERMISSION;

        $this->_doUpdateIconSize($vars, $code);

        chmod($userIconPath, 0777);

        $this->_backend->doUpdateIconSize($vars);

        $isExistsFile = file_exists($userIconPath.$file);

        $this->assertTrue($isExistsFile);

        unlink($userIconPath.$file);
    }
    
    public function _testCheckOnEmptyFile()
    {
        $userIconPath = $this->_getUserIconPath();
        $emptyFile = 'test.png';
         
        $defaultIconPath = $this->_getDefaultIconPath();
        
        $emptyFileFullPath = $defaultIconPath . $emptyFile;
        
        file_put_contents($emptyFileFullPath, "");
         
        $vars = array(
            'defaultIconPath' => $emptyFileFullPath,
            'userIconPath'    => $userIconPath . $emptyFile,
        );
        
        $code = WooCartBackendFestiPlugin::
                WOOCARTPRO_ERROR_CODE_INCORRECT_ICONS;
        
        $this->_doUpdateIconSize($vars, $code);
        
        unlink($emptyFileFullPath);
    }

    public function _testCheckOnDisallowedFileType()
    {
        $userIconPath = $this->_getUserIconPath();
        $defaultIconPath = $this->_getDefaultIconPath();
        
        $disallowedFile = 'test.bmp';
        
        $DisallowedFileFullPath = $defaultIconPath . $disallowedFile;
        
        $im = imagecreatetruecolor(120, 20);
        imagewbmp($im, $DisallowedFileFullPath);
        
        $vars = array(
            'defaultIconPath' => $DisallowedFileFullPath,
            'userIconPath'    => $userIconPath . $disallowedFile,
        );
        
        $code = WooCartBackendFestiPlugin::
                WOOCARTPRO_ERROR_CODE_INCORRECT_ICONS_TYPE;
        
        $this->_doUpdateIconSize($vars, $code);
        
        unlink($DisallowedFileFullPath);
    }

    private function _doUpdateIconSize($vars, $code)
    {
        try {
            $this->_backend->doUpdateIconSize($vars);
            $this->fail("Expected exception ".$code." not thrown");
        } catch (Exception $exp) {
            $this->assertTrue($exp->getCode() == $code);
        }
    }
    
    private function _getUserIconPath()
    {
        return $this->getPluginPath('static/images/icons/user/');
    }
    
    private function _getDefaultIconPath()
    {
        return $this->getPluginPath('/static/images/icons/default/');
    }
     
    /**
     * @ticket 2820
     */
    public function testUpdateIconColor()
    {
        $backend = $this->getBackendInstance();

        $oldOptions = $backend->getSettings();

        $randomColor = '#' . strtoupper(dechex(rand(0,10000000)));

        $this->updateSetting('iconColor', $randomColor);

        $type ='iconColor';
        $options = array('iconColor' => '#ffffff');
        $newSettings = array('iconColor' => $randomColor);

        $backend->updateColorOfDefaultIcons(
            $type,
            $options,
            $newSettings
        );

        $newOptions = $backend->getSettings();

        $colors = array(
            'from' => $oldOptions['iconColor'],
            'to' => $newOptions['iconColor']
        );

        $this->assertFalse(
            $backend->isIconColorChanged($colors),
            'icon color unchanged!'
            );
    } // end testUpdateIconColor

    public function testCustomCssFilesWritable()
    {
        $backend = $this->getBackendInstance();

        $backend->onInstall();

        $customCssPath = $this->getCustomCssPath();
        $files = $this->getFileNamesListOfCustomizeCart();
        $expectFiles = array();

        foreach ($files as $file) {
            $file = $customCssPath.$file.'.css';
            chmod($file, 0777);
            $expectFiles[] = $file;
        }

        $writableFiles = $backend->getWritableCustomCssFiles($files, $customCssPath);

        $this->assertEquals($expectFiles, $writableFiles);
    } // end testCustomCssFilesWritable

    /**
     * @ticket 3058
     */

    public function testPermissionFileDirectory()
    {
        $userIconPath = $this->getPluginPath(
            '/static/images/icons/default/'
        );
        $file = 'test.png';

        if (!is_dir($userIconPath)) {
            mkdir($userIconPath);
        }

        chmod($userIconPath, 0444);

        $vars = array(
            'defaultIconPath' => $this->getPluginPath(
                '/static/images/icons/default/icon1.png'
            ),
            'userIconPath'    => $userIconPath . $file
        );

        $code = WooCartBackendFestiPlugin::WOOCARTPRO_ERROR_CODE_PERMISSION;

        try {
            $this->getBackendInstance()->doUpdateIconSize($vars);
            $this->fail("Expected exception ".$code." not thrown");
        } catch (Exception $exp) {
            $this->assertTrue($exp->getCode() == $code);
        }

        chmod($userIconPath, 0777);

        $this->getBackendInstance()->doUpdateIconSize($vars);

        $isExistsFile = file_exists($userIconPath.$file);

        $this->assertTrue($isExistsFile);

        unlink($userIconPath.$file);
    } // testPermissionFileDirectory
    
    /**
     * @ticket 3116
     * @link http://localhost.in.ua/issues/3116
     */
    public function testCheckPremissionCustomCssFiles()
    {    	
        $fileSystem = $this->_backend->getFileSystemInstance();
    	$this->_setValueReflectionProperty('_fileSystem', $fileSystem);
    	
    	$customStylePath = $this->getPluginPath(
    	    '/static/styles/frontend/customize/'
    	);
        $testFilePath = $customStylePath.'popup_customize_style.css';

        $defaultSettingsPath = $this->getPluginPath(
            '/static/default_options/settings.txt'
        );

        if (!file_exists($defaultSettingsPath)) {
			throw new Exception('File Settings Not Found');
        }
        $code = WooCartBackendFestiPlugin::
                WOOCARTPRO_ERROR_CODE_PERMISSION_CUSTOM_STYLE;
        $defaultSettingsJson = file_get_contents($defaultSettingsPath);
        chmod($testFilePath, 0444);
        try {
        	
            $this->_backend->doImportSettingsFromJson($defaultSettingsJson);
           
        	$this->fail("Expected exception ".$code." not thrown");
        } catch (Exception $exp) {
            $this->assertTrue($exp->getCode() == $code);
        }
        chmod($testFilePath, 0777);
    }
    
    private function _setValueReflectionProperty($property, $value)
    {
    	$reflectionClass = new ReflectionClass($this->_backend);
    	$reflectionProperty = $reflectionClass->getProperty($property);
    	$reflectionProperty->setAccessible(true);
    	$reflectionProperty->setValue($this->_backend, $value);
    }
}