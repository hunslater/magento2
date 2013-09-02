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
 * Integration test of twig engine and classes it calls.
 *
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Core_Model_TemplateEngine_TwigTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Core_Model_TemplateEngine_Twig */
    protected $_twigEngine;

    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * Create a Twig template engine to test.
     */
    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_CONFIG);
        $this->_twigEngine = $this->_objectManager->create('Mage_Core_Model_TemplateEngine_Twig');
    }

    /**
     * Render a twig file using the Magento Twig Template Engine.
     *
     * @param Mage_Core_Block_Template $block
     * @param $fileName
     * @param array $dictionary
     * @return string
     */
    public function render(Mage_Core_Block_Template $block, $fileName, array $dictionary = array())
    {
        return $this->_twigEngine->render($block, $fileName, $dictionary);
    }

    /**
     * Test the render() function with a very simple .twig file.
     *
     * Template will include a title taken from the dictionary passed in.
     */
    public function testSimpleRender()
    {
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_DESIGN);
        $simpleTitle = 'This is the Title';
        $renderedOutput = '<html><head><title>' . $simpleTitle . '</title></head><body></body></html>';
        $path = __DIR__ . '/_files';
        $blockMock = $this->getMockBuilder('Mage_Core_Block_Template')
            ->disableOriginalConstructor()->getMock();

        $dictionary = array(
            'simple' => array(
                'title' => $simpleTitle
            )
        );

        $actualOutput = $this->render($blockMock, $path . '/simple.twig', $dictionary);
        $this->assertSame($renderedOutput, $actualOutput, 'Twig file did not render properly');
    }
}
