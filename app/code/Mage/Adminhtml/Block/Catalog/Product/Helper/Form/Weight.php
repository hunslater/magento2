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
 * Product form weight field helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight extends Varien_Data_Form_Element_Text
{
    const VIRTUAL_FIELD_HTML_ID = 'weight_and_type_switcher';

    /**
     * Is virtual checkbox element
     *
     * @var Varien_Data_Form_Element_Checkbox
     */
    protected $_virtual;

    /**
     * Catalog helper
     *
     * @var Mage_Catalog_Helper_Product
     */
    protected $_helper;

    public function __construct(array $data = array())
    {
        $this->_helper = isset($data['helper']) ? $data['helper'] : Mage::helper('Mage_Catalog_Helper_Product');
        $this->_virtual = isset($data['element'])
            ? $data['element']
            : Mage::getModel('Varien_Data_Form_Element_Checkbox');
        $this->_virtual->setId(self::VIRTUAL_FIELD_HTML_ID)->setName('is_virtual')
            ->setLabel($this->_helper->getTypeSwitcherControlLabel());
        $data['class'] = 'validate-number validate-zero-or-greater validate-number-range number-range-0-99999999.9999';
        parent::__construct($data);
    }

    /**
     * Add Is Virtual checkbox html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (!$this->getForm()->getDataObject()->getTypeInstance()->hasWeight()) {
            $this->_virtual->setChecked('checked');
        }
        return '<div class="fields-group-2"><div class="field"><div class="addon"><div class="control">'
            . parent::getElementHtml()
            . '<label class="addafter" for="'
            . $this->getHtmlId()
            . '"><strong>'. Mage::helper('Mage_Adminhtml_Helper_Data')->__('lbs') .'</strong></label>'
            . '</div></div></div><div class="field choice">'
            . $this->_virtual->getElementHtml() . $this->_virtual->getLabelHtml()
            . '</div></div>';
    }

    /**
     * Set form for both fields
     *
     * @param Varien_Data_Form $form
     * @return Varien_Data_Form
     */
    public function setForm($form)
    {
        $this->_virtual->setForm($form);
        return parent::setForm($form);
    }
}
