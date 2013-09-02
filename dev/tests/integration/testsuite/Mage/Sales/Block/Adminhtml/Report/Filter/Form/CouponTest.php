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
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for Mage_Index_Model_Lock_Storage
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form_CouponTest extends PHPUnit_Framework_TestCase
{
    /**
     * Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    protected function setUp()
    {
        parent::setUp();
        $this->_application = Mage::getObjectManager()->get('Mage_Core_Model_App');
    }

    /**
     * @covers Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon::_afterToHtml
     */
    public function testAfterToHtml()
    {
        /** @var $block Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon */
        $block = $this->_application->getLayout()->createBlock('Mage_Sales_Block_Adminhtml_Report_Filter_Form_Coupon');
        $block->setFilterData(new Varien_Object());
        $html = $block->toHtml();

        $expectedStrings = array(
            'FormElementDependenceController',
            'sales_report_rules_list',
            'sales_report_price_rule_type'
        );
        foreach ($expectedStrings as $expectedString) {
            $this->assertContains($expectedString, $html);
        }
    }
}
