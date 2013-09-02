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
 * @package     Mage_Tax
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Tax_Model_Class_Type_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder('Mage_Core_Model_Resource_Db_Collection_Abstract')
            ->setMethods(array(
                'addAttributeToFilter'
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($this->equalTo('tax_class_id'), $this->equalTo(1))
            ->will($this->returnSelf());

        $productMock = $this->getMockBuilder('Mage_Catalog_Model_Product')
            ->setMethods(array('getCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $model Mage_Tax_Model_Class_Type_Product */
        $model = $objectManagerHelper->getObject(
            'Mage_Tax_Model_Class_Type_Product',
            array(
                'modelProduct' => $productMock,
                'helper' => $this->getMock('Mage_Tax_Helper_Data', array(), array(), '', false),
                'data' => array('id' => 1)
            )
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }

}
