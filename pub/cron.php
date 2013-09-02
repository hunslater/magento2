<?php
/**
 * Scheduled jobs entry point
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
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require dirname(__DIR__) . '/app/bootstrap.php';
Magento_Profiler::start('mage');
Mage::register('custom_entry_point', true);
umask(0);

try {
    $params = array(Mage::PARAM_RUN_CODE => 'admin');
    $config = new Mage_Core_Model_Config_Primary(BP, $params);
    $entryPoint = new Mage_Core_Model_EntryPoint_Cron($config);
    $entryPoint->processRequest();
} catch (Exception $e) {
    Mage::printException($e);
}
Magento_Profiler::stop('mage');
