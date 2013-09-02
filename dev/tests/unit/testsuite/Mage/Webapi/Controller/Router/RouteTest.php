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
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Webapi_Controller_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $areaName = 'webapi';
        $route = new Mage_Webapi_Controller_Router_Route(
            $areaName . '/:' . Mage_Webapi_Controller_Request::PARAM_API_TYPE
        );

        $testApiType = 'test_api';
        $testUri = "$areaName/$testApiType";
        $request = new Zend_Controller_Request_Http();
        $request->setRequestUri($testUri);

        $match = $route->match($request);
        $this->assertEquals($testApiType, $match['api_type']);
    }
}
