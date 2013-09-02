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
 * @package     Mage_Theme
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Service model to upload single file in customizations
 */
class Mage_Theme_Model_Theme_SingleFile
{
    /**
     * @var Mage_Core_Model_Theme_Customization_FileInterface
     */
    protected $_fileService;

    /**
     * @param Mage_Core_Model_Theme_Customization_FileInterface $fileService
     */
    public function __construct(Mage_Core_Model_Theme_Customization_FileInterface $fileService)
    {
        $this->_fileService = $fileService;
    }

    /**
     * Creates or updates custom single file which belong to a selected theme
     *
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $fileContent
     * @return Mage_Core_Model_Theme_FileInterface
     */
    public function update(Mage_Core_Model_Theme $themeModel, $fileContent)
    {
        $customFiles = $themeModel->getCustomization()->getFilesByType($this->_fileService->getType());
        $customCss = reset($customFiles);
        if (empty($fileContent) && $customCss) {
            $customCss->delete();
            return $customCss;
        }
        if (!$customCss) {
            $customCss = $this->_fileService->create();
        }
        $customCss->setData('content', $fileContent);
        $customCss->setTheme($themeModel);
        $customCss->save();
        return $customCss;
    }
}
