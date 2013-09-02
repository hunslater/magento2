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
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Edit
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getAttributes'), array(), '', false);
        $product->expects($this->any())->method('getAttributes')->will($this->returnValue(array()));
        $product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        Mage::register('current_product', $product);
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit');
    }

    public function testGetTypeSwitcherData()
    {
        $data = json_decode($this->_block->getTypeSwitcherData(), true);
        $this->assertEquals('simple', $data['current_type']);
        $this->assertEquals(array(), $data['attributes']);
    }
}
