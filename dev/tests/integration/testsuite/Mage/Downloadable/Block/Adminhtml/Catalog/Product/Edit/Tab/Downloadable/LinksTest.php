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
 * @package     Mage_Downloadable
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = Mage::app()->getLayout()->createBlock(
            'Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links'
        );
        self::performUploadButtonTest($block);
    }

    /**
     * Reuse code for testing getUploadButtonHtml()
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public static function performUploadButtonTest(Mage_Core_Block_Abstract $block)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text', '',
            array('data' => array('text' => $expected))
        );
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetLinkData()
    {
        Mage::register('product', new Varien_Object(array('type_id' => 'simple')));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links');
        $this->assertEmpty($block->getLinkData());
    }

    /**
     * Get Links Title for simple/virtual/downloadable product
     *
     * @magentoConfigFixture current_store catalog/downloadable/links_title Links Title Test
     * @magentoAppIsolation enabled
     * @dataProvider productLinksTitleDataProvider
     *
     * @param string $productType
     * @param string $linksTitle
     * @param string $expectedResult
     */
    public function testGetLinksTitle($productType, $linksTitle, $expectedResult)
    {
        Mage::register('product', new Varien_Object(array(
            'type_id' => $productType,
            'id' => '1',
            'links_title' => $linksTitle
        )));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links');
        $this->assertEquals($expectedResult, $block->getLinksTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productLinksTitleDataProvider()
    {
        return array (
            array('simple', null, 'Links Title Test'),
            array('simple', 'Links Title', 'Links Title Test'),
            array('virtual', null, 'Links Title Test'),
            array('virtual', 'Links Title', 'Links Title Test'),
            array('downloadable', null, null),
            array('downloadable', 'Links Title', 'Links Title')
        );
    }
}
