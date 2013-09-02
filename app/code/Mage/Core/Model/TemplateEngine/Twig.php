<?php
/**
 * Template engine that enables Twig templates to be used for rendering.
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
class Mage_Core_Model_TemplateEngine_Twig implements Mage_Core_Model_TemplateEngine_EngineInterface,
    Mage_Core_Model_TemplateEngine_BlockTrackerInterface
{
    /**
     * @var Mage_Core_Block_Template
     */
    protected $_currentBlock;

    /**
     * @var Mage_Core_Model_TemplateEngine_Twig_EnvironmentFactory
     */
    protected $_factory;

    /**
     * @var Mage_Core_Model_TemplateEngine_Twig_Extension
     */
    protected $_extension;

    /**
     * @var Twig_Environment
     */
    protected $_environment;

    /**
     *  Populates the environment based on the environment builder provided.
     *
     * @param Mage_Core_Model_TemplateEngine_Twig_EnvironmentFactory $factory
     * @param Mage_Core_Model_TemplateEngine_Twig_Extension $extension
     */
    public function __construct(
        Mage_Core_Model_TemplateEngine_Twig_EnvironmentFactory $factory,
        Mage_Core_Model_TemplateEngine_Twig_Extension $extension
    ) {
        $this->_factory = $factory;
        $this->_extension = $extension;

        $this->_extension->setBlockTracker($this);
    }

    /**
     * Render the named Twig template using the given block as the context of the Twig helper functions/filters.
     *
     * @param Mage_Core_Block_Template $block
     * @param string $fileName
     * @param array $dictionary
     * @throws Exception 
     * @return string
     */
    public function render(Mage_Core_Block_Template $block, $fileName, array $dictionary = array())
    {
        if ($this->_environment === null) {
            $this->_environment = $this->_factory->create();
        }
        $dictionary['block'] = $block;
        // save state from previous block
        $previousBlock = $this->_currentBlock;
        $this->_currentBlock = $block;
        try {
            $output = $this->_environment->render($fileName, $dictionary);
        } catch (Exception $renderException) {
            // restore state for previous block
            $this->_currentBlock = $previousBlock;
            throw $renderException;     
        }
        // restore state for previous block
        $this->_currentBlock = $previousBlock;
        return $output;
    }

    /**
     * Get the current block
     *
     * @return Mage_Core_Block_Template
     */
    public function getCurrentBlock()
    {
        return $this->_currentBlock;
    }
}
