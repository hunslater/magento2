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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/edit.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_edit');
        $this->setUseContainer(true);
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add elements in layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit
     */
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                'onclick' => 'setLocation(\''
                    . $this->getUrl('*/*/', array('store' => $this->getRequest()->getParam('store', 0))) . '\')',
                'class' => 'action-back'
            ));
        } else {
            $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                'onclick' => 'window.close()',
                'class' => 'cancel'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('reset_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('_current' => true)) . '\')'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('save-split-button', 'Mage_Backend_Block_Widget_Button_Split', array(
                'id' => 'save-split-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save'),
                'class_name' => 'Mage_Backend_Block_Widget_Button_Split',
                'button_class' => 'widget-button-save',
                'options' => $this->_getSaveSplitButtonOptions()
            ));
        }

        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    /**
     * Get Save Split Button html
     *
     * @return string
     */
    public function getSaveSplitButtonHtml()
    {
        return $this->getChildHtml('save-split-button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }

    public function getIsGrouped()
    {
        return $this->getProduct()->isGrouped();
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }

    public function getHeader()
    {
        if ($this->getProduct()->getId()) {
            $header = $this->escapeHtml($this->getProduct()->getName());
        } else {
            $header = Mage::helper('Mage_Catalog_Helper_Data')->__('New Product');
        }
        return $header;
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    public function getIsConfigured()
    {
        $result = true;

        $product = $this->getProduct();
        if ($product->isConfigurable()) {
            $superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds($product);
            $result = !empty($superAttributes);
        }

        return $result;
    }

    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }

    /**
     * Get fields masks from config
     *
     * @return array
     */
    public function getFieldsAutogenerationMasks()
    {
        return $this->helper('Mage_Catalog_Helper_Product')->getFieldsAutogenerationMasks();
    }

    /**
     * Retrieve available placeholders
     *
     * @return array
     */
    public function getAttributesAllowedForAutogeneration()
    {
        return $this->helper('Mage_Catalog_Helper_Product')->getAttributesAllowedForAutogeneration();
    }

    /**
     * Get data for JS (product type transition)
     *
     * @return string
     */
    public function getTypeSwitcherData()
    {
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
            'tab_id' => 'product_info_tabs_downloadable_items',
            'is_virtual_id' => Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight::VIRTUAL_FIELD_HTML_ID,
            'weight_id' => 'weight',
            'current_type' => $this->getProduct()->getTypeId(),
            'attributes' => $this->_getAttributes(),
        ));
    }

    /**
     * Get formed array with attribute codes and Apply To property
     *
     * @return array
     */
    protected function _getAttributes()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $attributes = array();

        foreach ($product->getAttributes() as $key => $attribute) {
            $attributes[$key] = $attribute->getApplyTo();
        }
        return $attributes;
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {
        $options = array();
        if (!$this->getRequest()->getParam('popup')) {
            $options[] = array(
                'id' => 'edit-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Edit'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '[data-form=edit-product]'),
                    ),
                ),
                'default' => true,
            );
        }

        $options[] = array(
            'id' => 'new-button',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & New'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndNew', 'target' => '[data-form=edit-product]'),
                ),
            ),
        );
        if (!$this->getRequest()->getParam('popup') && $this->getProduct()->isDuplicable()) {
            $options[] = array(
                'id' => 'duplicate-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Duplicate'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndDuplicate', 'target' => '[data-form=edit-product]'),
                    ),
                ),
            );
        }
        $options[] = array(
            'id' => 'close-button',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Close'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '[data-form=edit-product]'),
                ),
            ),
        );
        return $options;
    }

    /**
     * Check whether new product is being created
     *
     * @return bool
     */
    protected function _isProductNew()
    {
        $product = $this->getProduct();
        return !$product || !$product->getId();
    }
}
