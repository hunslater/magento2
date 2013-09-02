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
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Block_Role_Tab_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Block_Role_Tab_Edit
     */
    protected $_block;

    public function setUp()
    {
        $roleAdmin = Mage::getModel('Mage_User_Model_Role');
        $roleAdmin->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        Mage::app()->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->_block = Mage::getObjectManager()->create('Mage_User_Block_Role_Tab_Edit');
    }

    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains(
            'Mage_Adminhtml::all',
            $this->_block->getSelectedResources()
        );
    }

    public function testGetTree()
    {
        $encodedTree = $this->_block->getTree();
        $this->assertNotEmpty($encodedTree);
    }
}
