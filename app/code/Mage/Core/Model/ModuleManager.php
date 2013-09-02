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

/**
 * Module statuses manager
 */
class Mage_Core_Model_ModuleManager
{
    /**#@+
     * XPath in the configuration where module statuses are stored
     */
    const XML_PATH_MODULE_STATUS        = 'modules/%s/active';
    const XML_PATH_MODULE_OUTPUT_STATUS = 'advanced/modules_disable_output/%s';
    /**#@-*/

    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    private $_config;

    /**
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    private $_storeConfig;

    /**
     * @var array
     */
    private $_outputConfigPaths;

    /**
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Store_ConfigInterface $storeConfig
     * @param array $outputConfigPaths Format: array('<Module_Name>' => '<store_config_path>', ...)
     */
    public function __construct(
        Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Store_ConfigInterface $storeConfig,
        array $outputConfigPaths = array()
    ) {
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
        $this->_outputConfigPaths = $outputConfigPaths;
    }

    /**
     * Whether a module is enabled in the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isEnabled($moduleName)
    {
        $moduleStatus = $this->_config->getNode(sprintf(self::XML_PATH_MODULE_STATUS, $moduleName));
        return ($moduleStatus && in_array((string)$moduleStatus, array('true', '1')));
    }

    /**
     * Whether a module output is permitted by the configuration or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        if (!$this->isEnabled($moduleName)) {
            return false;
        }
        if (!$this->_isCustomOutputConfigEnabled($moduleName)) {
            return false;
        }
        if ($this->_storeConfig->getConfigFlag(sprintf(self::XML_PATH_MODULE_OUTPUT_STATUS, $moduleName))) {
            return false;
        }
        return true;
    }

    /**
     * Whether a configuration switch for a module output permits output or not
     *
     * @param string $moduleName Fully-qualified module name
     * @return boolean
     */
    protected function _isCustomOutputConfigEnabled($moduleName)
    {
        if (isset($this->_outputConfigPaths[$moduleName])) {
            $configPath = $this->_outputConfigPaths[$moduleName];
            if (defined($configPath)) {
                $configPath = constant($configPath);
            }
            return $this->_storeConfig->getConfigFlag($configPath);
        }
        return true;
    }
}
