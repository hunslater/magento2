<?php
/**
 * Factory class for Template Engine
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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Core_Model_TemplateEngine_Factory
{
    protected $_objectManager;

    /**
     * Template engine types
     */
    const ENGINE_TWIG = 'twig';
    const ENGINE_PHTML = 'phtml';

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Gets the singleton instance of the appropriate template engine
     *
     * @param string $name
     * @return Mage_Core_Model_TemplateEngine_EngineInterface
     * @throws InvalidArgumentException if template engine doesn't exist
     */
    public function get($name)
    {
        if (self::ENGINE_TWIG == $name) {
            return $this->_objectManager->get('Mage_Core_Model_TemplateEngine_Twig');
        } else if (self::ENGINE_PHTML == $name) {
            return $this->_objectManager->get('Mage_Core_Model_TemplateEngine_Php');
        }
        // unknown type, throw exception
        throw new InvalidArgumentException('Unknown template engine type: ' . $name);
    }
}
