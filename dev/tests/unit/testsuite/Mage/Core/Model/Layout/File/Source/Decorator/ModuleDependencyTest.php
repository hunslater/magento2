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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Model_Layout_File_Source_Decorator_ModuleDependencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_File_Source_Decorator_ModuleDependency
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileSource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_config;

    protected function setUp()
    {
        $configXml = new SimpleXMLElement('<?xml version="1.0"?>
            <config>
                <modules>
                    <Fixture_ModuleB/>
                    <Fixture_ModuleA>
                        <depends>
                            <Fixture_ModuleB/>
                        </depends>
                    </Fixture_ModuleA>
                </modules>
            </config>
        ');
        $this->_fileSource = $this->getMockForAbstractClass('Mage_Core_Model_Layout_File_SourceInterface');
        $this->_config = $this->getMock('Mage_Core_Model_Config_Modules', array(), array(), '', false);
        $this->_config->expects($this->any())->method('getModuleConfig')->will($this->returnValue($configXml->modules));
        $this->_model = new Mage_Core_Model_Layout_File_Source_Decorator_ModuleDependency(
            $this->_fileSource, $this->_config
        );
    }

    /**
     * @param array $fixtureFiles
     * @param array $expectedFiles
     * @param string $message
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles(array $fixtureFiles, array $expectedFiles, $message)
    {
        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $this->_fileSource
            ->expects($this->once())
            ->method('getFiles')
            ->with($theme)
            ->will($this->returnValue($fixtureFiles))
        ;
        $this->assertSame($expectedFiles, $this->_model->getFiles($theme), $message);
    }

    public function getFilesDataProvider()
    {
        $fileOne = new Mage_Core_Model_Layout_File('b.xml', 'Fixture_ModuleB');
        $fileTwo = new Mage_Core_Model_Layout_File('a.xml', 'Fixture_ModuleA');
        $fileThree = new Mage_Core_Model_Layout_File('b.xml', 'Fixture_ModuleA');

        $unknownFileOne = new Mage_Core_Model_Layout_File('b.xml', 'Unknown_ModuleA');
        $unknownFileTwo = new Mage_Core_Model_Layout_File('a.xml', 'Unknown_ModuleB');
        return array(
            'same module' => array(
                array($fileThree, $fileTwo),
                array($fileTwo, $fileThree),
                'Files belonging to the same module are expected to be sorted by file names',
            ),
            'different modules' => array(
                array($fileTwo, $fileOne),
                array($fileOne, $fileTwo),
                'Files belonging to different modules are expected to be sorted by module dependencies',
            ),
            'different unknown modules' => array(
                array($unknownFileTwo, $unknownFileOne),
                array($unknownFileOne, $unknownFileTwo),
                'Files belonging to different unknown modules are expected to be sorted by module names',
            ),
            'known and unknown modules' => array(
                array($fileTwo, $unknownFileOne),
                array($unknownFileOne, $fileTwo),
                'Files belonging to unknown modules are expected to go before ones of known modules',
            ),
        );
    }
}
