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
 * @package     Mage_Index
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unit test for Mage_Index_Model_Process_FileFactory
 */
class Mage_Index_Model_Process_FileFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Bogus string to return from object manager's create() method
     */
    const CREATE_RESULT       = 'create_result';

    /**
     * Expected class name
     */
    const EXPECTED_CLASS_NAME = 'Mage_Index_Model_Process_File';

    /**
     * @var array
     */
    protected $_arguments = array(
        'key' => 'value'
    );

    public function testCreate()
    {
        $objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->with(self::EXPECTED_CLASS_NAME, $this->_arguments)
            ->will($this->returnValue(self::CREATE_RESULT));

        $factory = new Mage_Index_Model_Process_FileFactory($objectManagerMock);
        $this->assertEquals(self::CREATE_RESULT, $factory->create($this->_arguments));
    }
}
