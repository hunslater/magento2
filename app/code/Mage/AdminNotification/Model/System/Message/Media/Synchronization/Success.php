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
 * @copyright Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_AdminNotification_Model_System_Message_Media_Synchronization_Success
    extends Mage_AdminNotification_Model_System_Message_Media_SynchronizationAbstract
{
    /**
     * Message identity
     *
     * @var string
     */
    protected $_identity = 'MEDIA_SYNCHRONIZATION_SUCCESS';

    /**
     * Check whether
     *
     * @return bool
     */
    protected function _shouldBeDisplayed()
    {
        $state = $this->_syncFlag->getState();
        $data = $this->_syncFlag->getFlagData();
        $hasErrors = isset($data['has_errors']) && true == $data['has_errors'] ? true : false;
        return false == $hasErrors && Mage_Core_Model_File_Storage_Flag::STATE_FINISHED == $state;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('Synchronization of media storages has been completed.');
    }
}