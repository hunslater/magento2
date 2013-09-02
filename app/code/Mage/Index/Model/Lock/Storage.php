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
 * @package     Mage_Index
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Lock file storage for index processes
 */
class Mage_Index_Model_Lock_Storage
{
    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * @var Mage_Index_Model_Process_FileFactory
     */
    protected $_fileFactory;

    /**
     * File handlers by process IDs
     *
     * @var array
     */
    protected $_fileHandlers = array();

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Index_Model_Process_FileFactory $fileFactory
     */
    public function __construct(
        Mage_Core_Model_Dir $dirs,
        Mage_Index_Model_Process_FileFactory $fileFactory
    ) {
        $this->_dirs = $dirs;
        $this->_fileFactory   = $fileFactory;
    }

    /**
     * Get file handler by process ID
     *
     * @param $processId
     * @return Mage_Index_Model_Process_File
     */
    public function getFile($processId)
    {
        if (!isset($this->_fileHandlers[$processId])) {
            $file = $this->_fileFactory->create();
            $varDirectory = $this->_dirs->getDir(Mage_Core_Model_Dir::VAR_DIR) . DIRECTORY_SEPARATOR . 'locks';
            $file->setAllowCreateFolders(true);

            $file->open(array('path' => $varDirectory));
            $fileName = 'index_process_' . $processId . '.lock';
            $file->streamOpen($fileName);
            $file->streamWrite(date('r'));
            $this->_fileHandlers[$processId] = $file;
        }
        return $this->_fileHandlers[$processId];
    }
}
