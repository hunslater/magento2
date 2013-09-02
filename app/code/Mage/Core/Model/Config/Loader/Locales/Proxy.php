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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Model_Config_Loader_Locales_Proxy implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * @var Mage_Core_Model_Config_Loader_Locales
     */
    protected $_loader;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get loader instance
     *
     * @return Mage_Core_Model_Config_Loader_Locales
     */
    protected function _getLoader()
    {
        if (null === $this->_loader) {
            $this->_loader = $this->_objectManager->get('Mage_Core_Model_Config_Loader_Locales');
        }
        return $this->_loader;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     * @param bool $useCache
     * @return void
     */
    public function load(Mage_Core_Model_Config_Base $config, $useCache = true)
    {
        $this->_getLoader()->load($config, $useCache);
    }
}
