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
 * Locale validator model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Locale_Validator
{
    /**
     * @var Mage_Core_Model_Locale_Config
     */
    protected $_localeConfig;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Locale_Config $localeConfig
     */
    public function __construct(Mage_Core_Model_Locale_Config $localeConfig)
    {
        $this->_localeConfig = $localeConfig;
    }

    /**
     * Validate locale code
     *
     * @param string $localeCode
     * @return boolean
     */
    public function isValid($localeCode)
    {
        $isValid = true;
        $allowedLocaleCodes = $this->_localeConfig->getAllowedLocales();

        if (!$localeCode || !in_array($localeCode, $allowedLocaleCodes)) {
            $isValid = false;
        }

        return $isValid;
    }
}
