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
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Mage/Core/_files/store.php
 */
class Mage_Newsletter_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Newsletter_Model_Template
     */
    protected  $_model = null;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Newsletter_Model_Template');
    }

    /**
     * @magentoConfigFixture                    install/design/theme/full_name   default/basic
     * @magentoConfigFixture                    adminhtml/design/theme/full_name default/basic
     * @magentoConfigFixture current_store      design/theme/full_name           default/iphone
     * @magentoConfigFixture fixturestore_store design/theme/full_name           default/blank
     * @magentoAppIsolation  enabled
     * @dataProvider         getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($area, $store, $design)
    {
        $this->markTestIncomplete('Test partially fails bc of MAGETWO-557.');
        $this->_model->setTemplateText('{{view url="Mage_Page::favicon.ico"}}');
        $this->assertStringEndsWith('theme/frontend/default/demo/en_US/Mage_Page/favicon.ico',
            $this->_model->getProcessedTemplate()
        );
        $this->_model->emulateDesign($store, $area);
        $expectedTemplateText = "theme/{$area}/{$design}/en_US/Mage_Page/favicon.ico";
        $this->assertStringEndsWith($expectedTemplateText, $this->_model->getProcessedTemplate());
        $this->_model->revertDesign();
    }

    /**
     * @return array
     */
    public function getProcessedTemplateDataProvider()
    {
        return array(
            'install'        => array('install',   'default',      'default/demo'),
            'backend'        => array('adminhtml', 'admin',        'default/basic'),
            'frontend'       => array('frontend',  'default',      'default/iphone'),
            'frontend store' => array('frontend',  'fixturestore', 'default/blank'),
        );
    }

    /**
     * @magentoConfigFixture current_store system/smtp/disable 0
     * @magentoAppIsolation enabled
     * @dataProvider isValidToSendDataProvider
     */
    public function testIsValidToSend($senderEmail, $senderName, $subject, $isValid)
    {
        $this->_model->setTemplateSenderEmail($senderEmail)
            ->setTemplateSenderName($senderName)
            ->setTemplateSubject($subject);
        $this->assertSame($isValid, $this->_model->isValidForSend());
    }

    /**
     * @return array
     */
    public function isValidToSendDataProvider()
    {
        return array(
            array('john.doe@example.com', 'john.doe', 'Test Subject', true),
            array('john.doe@example.com', 'john.doe', '', false),
            array('john.doe@example.com', '', 'Test Subject', false),
            array('john.doe@example.com', '', '', false),
            array('', 'john.doe', 'Test Subject', false),
            array('', '', 'Test Subject', false),
            array('', 'john.doe', '', false),
            array('', '', '', false),
        );
    }
}
