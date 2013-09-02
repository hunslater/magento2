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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Directory_Model_Config_Source_Country implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Countries
     *
     * @var Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * Directory Helper
     *
     * @var Mage_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @param Mage_Directory_Model_Resource_Country_Collection $countryCollection
     * @param Mage_Directory_Helper_Data $directoryHelper
     */
    public function __construct(
        Mage_Directory_Model_Resource_Country_Collection $countryCollection,
        Mage_Directory_Helper_Data $directoryHelper
    ) {
        $this->_countryCollection = $countryCollection;
        $this->_directoryHelper = $directoryHelper;
    }

    /**
     * Options array
     *
     * @var type
     */
    protected $_options;

    /**
     * Return options array
     *
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     * @return array
     */
    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        if (!$this->_options) {
            $this->_options = $this->_countryCollection
                ->loadData()
                ->setForegroundCountries($foregroundCountries)
                ->toOptionArray(false);
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array(
                'value' => '',
                'label' => $this->_directoryHelper->__('--Please Select--'),
            ));
        }

        return $options;
    }
}
