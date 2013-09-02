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
 * Theme form, Css editor tab
 *
 * @method Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css setFiles(array $files)
 * @method array getFiles()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
    extends Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_TabAbstract
{
    /**
     * Uploader service
     *
     * @var Mage_Theme_Model_Uploader_Service
     */
    protected $_uploaderService;

    /**
     * Theme custom css file
     *
     * @var Mage_Core_Model_Theme_File
     */
    protected $_customCssFile;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Theme_Model_Uploader_Service $uploaderService
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Magento_ObjectManager $objectManager,
        Mage_Theme_Model_Uploader_Service $uploaderService,
        array $data = array()
    ) {
        parent::__construct($context, $objectManager, $data);
        $this->_uploaderService = $uploaderService;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->_addThemeCssFieldset();

        $customFiles = $this->_getCurrentTheme()->getCustomization()->getFilesByType(
            Mage_Theme_Model_Theme_Customization_File_CustomCss::TYPE
        );
        $this->_customCssFile = reset($customFiles);
        $this->_addCustomCssFieldset();

        $formData['custom_css_content'] = $this->_customCssFile ? $this->_customCssFile->getContent() : null;

        /** @var $session Mage_Backend_Model_Session */
        $session = $this->_objectManager->get('Mage_Backend_Model_Session');
        $cssFileContent = $session->getThemeCustomCssData();
        if ($cssFileContent) {
            $formData['custom_css_content'] = $cssFileContent;
            $session->unsThemeCustomCssData();
        }
        $form->addValues($formData);
        parent::_prepareForm();
        return $this;
    }

    /**
     * Set theme css fieldset
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _addThemeCssFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('theme_css', array(
            'legend' => $this->__('Theme CSS'),
            'class'  => 'fieldset-wide'
        ));
        $this->_addElementTypes($themeFieldset);
        foreach ($this->getFiles() as $groupName => $files) {
            foreach ($files as &$file) {
                $file = $this->_convertFileData($file);
            }
            $themeFieldset->addField('theme_css_view_' . $groupName, 'links', array(
                'label'       => $groupName,
                'title'       => $groupName,
                'name'        => 'links',
                'values'      => $files,
            ));
        }

        return $this;
    }

    /**
     * Prepare file items for output on page for download
     *
     * @param Mage_Core_Model_Theme_File $file
     * @return array
     */
    protected function _convertFileData($file)
    {
        return array(
            'href'      => $this->getDownloadUrl($file['id'], $this->_getCurrentTheme()->getId()),
            'label'     => $file['id'],
            'title'     => $file['safePath'],
            'delimiter' => '<br />'
        );
    }

    /**
     * Set custom css fieldset
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _addCustomCssFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('custom_css', array(
            'legend' => $this->__('Custom CSS'),
            'class'  => 'fieldset-wide'
        ));
        $this->_addElementTypes($themeFieldset);

        $themeFieldset->addField('css_file_uploader', 'css_file', array(
            'name'     => 'css_file_uploader',
            'label'    => $this->__('Select CSS File to Upload'),
            'title'    => $this->__('Select CSS File to Upload'),
            'accept'   => 'text/css',
            'note'     => $this->_getUploadCssFileNote()
        ));

        $themeFieldset->addField('css_uploader_button', 'button', array(
            'name'     => 'css_uploader_button',
            'value'    => $this->__('Upload CSS File'),
            'disabled' => 'disabled',
        ));

        $downloadButtonConfig = array(
            'name'  => 'css_download_button',
            'value' => $this->__('Download CSS File'),
            'onclick' => "setLocation('" . $this->getUrl('*/*/downloadCustomCss', array(
                'theme_id' => $this->_getCurrentTheme()->getId())) . "');"
        );
        if (!$this->_customCssFile) {
            $downloadButtonConfig['disabled'] = 'disabled';
        }
        $themeFieldset->addField('css_download_button', 'button', $downloadButtonConfig);

        /** @var $imageButton Mage_Backend_Block_Widget_Button */
        $imageButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
            'id'        => 'css_images_manager',
            'label'     => $this->__('Manage'),
            'class'     => 'button',
            'onclick'   => "MediabrowserUtility.openDialog('"
                . $this->getUrl('*/system_design_wysiwyg_files/index', array(
                    'target_element_id'                           => 'custom_css_content',
                    Mage_Theme_Helper_Storage::PARAM_THEME_ID     => $this->_getCurrentTheme()->getId(),
                    Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE => Mage_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE
                ))
                . "', null, null,'"
                . $this->quoteEscape(
                    $this->__('Upload Images'), true
                )
                . "');"
        ));

        $themeFieldset->addField('css_browse_image_button', 'note', array(
            'label' => $this->__("Images Assets"),
            'text'  => $imageButton->toHtml()
        ));

        /** @var $fontButton Mage_Backend_Block_Widget_Button */
        $fontButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
            'id'        => 'css_fonts_manager',
            'label'     => $this->__('Manage'),
            'class'     => 'button',
            'onclick'   => "MediabrowserUtility.openDialog('"
                . $this->getUrl('*/system_design_wysiwyg_files/index', array(
                    'target_element_id'                           => 'custom_css_content',
                    Mage_Theme_Helper_Storage::PARAM_THEME_ID     => $this->_getCurrentTheme()->getId(),
                    Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE => Mage_Theme_Model_Wysiwyg_Storage::TYPE_FONT
                ))
                . "', null, null,'"
                . $this->quoteEscape(
                    $this->__('Upload Fonts'), true
                )
                . "');",
        ));

        $themeFieldset->addField('css_browse_font_button', 'note', array(
            'label' => $this->__("Fonts Assets"),
            'text'  => $fontButton->toHtml()
        ));

        $themeFieldset->addField('custom_css_content', 'textarea', array(
            'label'  => $this->__('Edit custom.css'),
            'title'  => $this->__('Edit custom.css'),
            'name'   => 'custom_css_content',
        ));

        return $this;
    }

    /**
     * Get note string for css file to Upload
     *
     * @return string
     */
    protected function _getUploadCssFileNote()
    {
        $messages = array(
            $this->__('Allowed file types *.css.'),
            $this->__('This file will replace the current custom.css file and can\'t be more than 2 MB.')
        );
        $maxFileSize = $this->_objectManager->get('Magento_File_Size')->getMaxFileSizeInMb();
        if ($maxFileSize) {
            $messages[] = $this->__('Max file size to upload %sM', $maxFileSize);
        } else {
            $messages[] = $this->__('Something is wrong with the file upload settings.');
        }

        return implode('<br />', $messages);
    }

    /**
     * Set additional form field type for theme preview image
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $linksElement = 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links';
        $fileElement = 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File';
        return array('links' => $linksElement, 'css_file' => $fileElement);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('CSS Editor');
    }

    /**
     * Get url to downlaod CSS file
     *
     * @param string $fileId
     * @param int $themeId
     * @return string
     */
    public function getDownloadUrl($fileId, $themeId)
    {
        return $this->getUrl('*/*/downloadCss', array(
            'theme_id' => $themeId,
            'file'     => $this->_helperFactory->get('Mage_Core_Helper_Data')->urlEncode($fileId)
        ));
    }
}
