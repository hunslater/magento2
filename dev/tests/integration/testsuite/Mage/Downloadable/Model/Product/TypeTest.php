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
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for Mage_Downloadable_Model_Product_Type
 */
class Mage_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getObjectManager()->create('Mage_Downloadable_Model_Product_Type');
    }

    /**
     * @magentoDataFixture Mage/Downloadable/_files/product_with_files.php
     */
    public function testDeleteTypeSpecificData()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);
        Mage::app()->setCurrentStore(Mage_Core_Model_AppInterface::ADMIN_STORE_ID);
        $product->setOrigData();
        $downloadableData = array();

        $links = $this->_model->getLinks($product);
        $this->assertNotEmpty($links);
        $samples = $this->_model->getSamples($product);
        $this->assertNotEmpty($samples->getData());
        foreach ($links as $link) {
            $downloadableData['link'][] = $link->getData();
        }
        foreach ($samples as $sample) {
            $downloadableData['sample'][] = $sample->getData();
        }

        $product->setDownloadableData($downloadableData);
        $this->_model->deleteTypeSpecificData($product);
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1);

        $links = $this->_model->getLinks($product);
        $this->assertEmpty($links);
        $samples = $this->_model->getSamples($product);
        $this->assertEmpty($samples->getData());
    }
}
