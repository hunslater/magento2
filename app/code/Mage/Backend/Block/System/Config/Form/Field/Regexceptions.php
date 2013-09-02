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
 * @package     Mage_Backend
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend system config array field renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form_Field_Regexceptions
    extends Mage_Backend_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Initialise form fields
     */
    protected function _construct()
    {
        $this->addColumn('search', array(
            'label' => $this->helper('Mage_Backend_Helper_Data')->__('Search String'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => $this->helper('Mage_Backend_Helper_Data')->__('Design Theme'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add Exception');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'value' && isset($this->_columns[$columnName])) {
            /** @var $label Mage_Core_Model_Theme_Label */
            $label = Mage::getModel('Mage_Core_Model_Theme_Label');
            $options = $label->getLabelsCollection($this->__('-- No Theme --'));
            $element = new Varien_Data_Form_Element_Select();
            $element
                ->setForm($this->getForm())
                ->setName($this->_getCellInputElementName($columnName))
                ->setHtmlId($this->_getCellInputElementId('#{_id}', $columnName))
                ->setValues($options);
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

}
