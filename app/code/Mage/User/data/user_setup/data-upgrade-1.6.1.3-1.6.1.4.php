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
 * @category    Mage
 * @package     Mage_User
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var Varien_Db_Adapter_Interface $connection */
    $connection = $installer->getConnection();
    $remove = array(
        'Mage_Catalog::catalog_attributes',
        'Mage_Cms::cms',
        'Mage_Newsletter::admin_newsletter',
        'Mage_Review::pending',
        'Mage_Review::reviews',
        'Mage_Review::reviews_ratings',
        'Mage_Tag::tag',
        'Mage_Tag::tag_pending',
    );
    $connection->delete($tableName, array('resource_id IN (?)' => $remove));
}

$installer->endSetup();