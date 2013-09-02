<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * JavaScript helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Js extends Mage_Core_Helper_Abstract
{
    /**
     * Key for cache
     */
    const JAVASCRIPT_TRANSLATE_CONFIG_KEY = 'javascript_translate_config';

    /**
     * Translate file name
     */
    const JAVASCRIPT_TRANSLATE_CONFIG_FILENAME = 'jstranslator.xml';

    /**
     * Array of senteces of JS translations
     *
     * @var array
     */
    protected $_translateData = null;

    /**
     * Translate config
     *
     * @var Varien_Simplexml_Config
     */
    protected $_config = null;

    /**
     * Modules configuration reader
     *
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_configReader;

    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @var Mage_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config_Modules_Reader $configReader
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_View_Url $viewUrl
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config_Modules_Reader $configReader,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_View_Url $viewUrl
    ) {
        parent::__construct($context);
        $this->_configReader = $configReader;
        $this->_configCacheType = $configCacheType;
        $this->_viewUrl = $viewUrl;
    }

    /**
     * Retrieve JSON of JS sentences translation
     *
     * @return string
     */
    public function getTranslateJson()
    {
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($this->_getTranslateData());
    }

    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = '(function($) {$.mage.translate.add(' . $this->getTranslateJson() . ')})(jQuery);';
        return $this->getScript($script);
    }

    /**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  script
     */
    public function getScript($script)
    {
        return '<script type="text/javascript">//<![CDATA[' . "\n{$script}\n" . '//]]></script>';
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script type="text/javascript" src="' . $this->_viewUrl->getViewFileUrl($file) . '"></script>' . "\n";
    }

    /**
     * Retrieve JS translation array
     *
     * @return array
     */
    protected function _getTranslateData()
    {
        if ($this->_translateData === null) {
            $this->_translateData = array();
            $messages = $this->_getXmlConfig()->getXpath('*/message');
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    $messageText = (string)$message;
                    $module = $message->getParent()->getAttribute("module");
                    $this->_translateData[$messageText] = Mage::helper(
                        empty($module) ? 'Mage_Core' : $module
                    )->__($messageText);
                }
            }

            foreach ($this->_translateData as $key => $value) {
                if ($key == $value) {
                    unset($this->_translateData[$key]);
                }
            }
        }
        return $this->_translateData;
    }

    /**
     * Load config from files and try to cache it
     *
     * @return Varien_Simplexml_Config
     */
    protected function _getXmlConfig()
    {
        if (is_null($this->_config)) {
            $cachedXml = $this->_configCacheType->load(self::JAVASCRIPT_TRANSLATE_CONFIG_KEY);
            if ($cachedXml) {
                $xmlConfig = new Varien_Simplexml_Config($cachedXml);
            } else {
                $xmlConfig = new Varien_Simplexml_Config();
                $xmlConfig->loadString('<?xml version="1.0"?><jstranslator></jstranslator>');
                $this->_configReader->loadModulesConfiguration(self::JAVASCRIPT_TRANSLATE_CONFIG_FILENAME, $xmlConfig);
                $this->_configCacheType->save($xmlConfig->getXmlString(), self::JAVASCRIPT_TRANSLATE_CONFIG_KEY);
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }
}
