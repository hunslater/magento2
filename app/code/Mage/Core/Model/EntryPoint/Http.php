<?php
/**
 * Http entry point
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Core_Model_EntryPoint_Http extends Mage_Core_Model_EntryPointAbstract
{
    /**
     * Process http request, output html page or proper information about an exception (if any)
     */
    public function processRequest()
    {
        try {
            parent::processRequest();
        } catch (Mage_Core_Model_Session_Exception $e) {
            header('Location: ' . Mage::getBaseUrl());
        } catch (Mage_Core_Model_Store_Exception $e) {
            require Mage::getBaseDir(Mage_Core_Model_Dir::PUB) . DS . 'errors' . DS . '404.php';
        } catch (Magento_BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
        } catch (Exception $e) {
            Mage::printException($e);
        }
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        $request = $this->_objectManager->get('Mage_Core_Controller_Request_Http');
        $response = $this->_objectManager->get('Mage_Core_Controller_Response_Http');
        $handler = $this->_objectManager->get('Magento_Http_Handler_Composite');
        $handler->handle($request, $response);
    }
}
