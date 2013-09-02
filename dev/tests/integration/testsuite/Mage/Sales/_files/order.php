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
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require __DIR__ . '/../../Catalog/_files/product_simple.php';
/** @var Mage_Catalog_Model_Product $product */

$addressData = include(__DIR__ . '/address_data.php');
$billingAddress = Mage::getModel('Mage_Sales_Model_Order_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

/** @var Mage_Sales_Model_Order_Item $orderItem */
$orderItem = Mage::getModel('Mage_Sales_Model_Order_Item');
$orderItem->setProductId($product->getId())->setQtyOrdered(2);

/** @var Mage_Sales_Model_Order $order */
$order = Mage::getModel('Mage_Sales_Model_Order');
$order->setIncrementId('100000001')
    ->setState(Mage_Sales_Model_Order::STATE_PROCESSING)
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setCustomerEmail('customer@null.com')
    ->setStoreId(Mage::app()->getStore()->getId())
    ->addItem($orderItem)
    ->setPayment($payment);
$order->save();
