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
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for obsolete methods in Product Type instances
 */
class Legacy_Mage_Catalog_Model_Product_TypeTest extends Legacy_Mage_Catalog_Model_Product_AbstractTypeTest
{
    /**
     * @var array
     */
    protected $_productTypeFiles = array(
        '/app/code/Mage/Catalog/Model/Product/Type/Abstract.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Configurable.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Grouped.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Simple.php',
        '/app/code/Mage/Catalog/Model/Product/Type/Virtual.php',
    );
}
