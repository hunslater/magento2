<?php
/**
 * Test Rest router route.
 *
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
class Mage_Webapi_Controller_Router_Route_RestTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Router_Route_Rest */
    protected $_restRoute;

    protected function setUp()
    {
        /** Init SUT. */
        $this->_restRoute = new Mage_Webapi_Controller_Router_Route_Rest('route');
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_restRoute);
        parent::tearDown();
    }

    /**
     * Test setResourceName and getResourceName methods.
     */
    public function testResourceName()
    {
        /** Assert that new object has no Resource name set. */
        $this->assertNull($this->_restRoute->getResourceName(), 'New object has a set Resource name.');
        /** Set Resource name. */
        $resourceName = 'Resource name';
        $this->_restRoute->setResourceName($resourceName);
        /** Assert that Resource name was set. */
        $this->assertEquals($resourceName, $this->_restRoute->getResourceName(), 'Resource name is wrong.');
    }

    /**
     * Test setResourceType and getResourceType methods.
     */
    public function testResourceType()
    {
        /** Assert that new object has no Resource type set. */
        $this->assertNull($this->_restRoute->getResourceType(), 'New object has a set Resource type.');
        /** Set Resource type. */
        $resourceType = 'Resource type';
        $this->_restRoute->setResourceType($resourceType);
        /** Assert that Resource type was set. */
        $this->assertEquals($resourceType, $this->_restRoute->getResourceType(), 'Resource type is wrong.');
    }
}
