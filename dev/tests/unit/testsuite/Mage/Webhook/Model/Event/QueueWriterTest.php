<?php
/**
 * Mage_Webhook_Model_Event_QueueWriter
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
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Webhook_Model_Event_QueueWriterTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webhook_Model_Event_QueueWriter */
    protected $_eventQueue;

    /** @var Mage_Webhook_Model_Event_Factory  */
    protected $_eventFactory;

    public function setUp()
    {
        $this->_eventFactory = $this->_mockCollection = $this->getMockBuilder('Mage_Webhook_Model_Event_Factory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_eventQueue = new Mage_Webhook_Model_Event_QueueWriter($this->_eventFactory);
    }

    public function testOfferMagentoEvent()
    {
        $magentoEvent = $this->_mockCollection = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');
        $result = $this->_eventQueue->offer($magentoEvent);
        $this->assertEquals(null, $result);
    }

    public function testOfferNonMagentoEvent()
    {
        $magentoEvent = $this->getMockBuilder('Mage_Webhook_Model_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $magentoEvent->expects($this->once())
            ->method('save');

        $this->_eventFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($magentoEvent));


        $event = $this->getMockBuilder('Magento_PubSub_EventInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->_eventQueue->offer($event);
        $this->assertEquals(null, $result);
    }
}
