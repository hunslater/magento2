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
 * Web service api main helper.
 *
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Api_Helper_Data extends Mage_Core_Helper_Abstract
{

    /** @var Mage_Core_Controller_Request_Http */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Controller_Request_Http $request
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_Core_Controller_Request_Http $request)
    {
        parent::__construct($context);
        $this->_request = $request;
    }

    /**
     * Go through a WSI args array and turns it to correct state.
     *
     * @param Object $obj - Link to Object
     * @return Object
     */
    public function wsiArrayUnpacker(&$obj)
    {
        if (is_object($obj)) {

            $modifiedKeys = $this->clearWsiFootprints($obj);

            foreach ($obj as $value) {
                if (is_object($value)) {
                    $this->wsiArrayUnpacker($value);
                }
                if (is_array($value)) {
                    foreach ($value as &$val) {
                        if (is_object($val)) {
                            $this->wsiArrayUnpacker($val);
                        }
                    }
                }
            }

            foreach ($modifiedKeys as $arrKey) {
                if ($arrKey !== 'complex_filter') {
                    $this->associativeArrayUnpack($obj->$arrKey);
                }
            }
        }
    }

    /**
     * Go through an object parameters and unpack associative object to array.
     *
     * @param Object $obj - Link to Object
     * @return Object
     */
    public function v2AssociativeArrayUnpacker(&$obj)
    {
        if (is_object($obj)
            && property_exists($obj, 'key')
            && property_exists($obj, 'value')
        ) {
            if (count(array_keys(get_object_vars($obj))) == 2) {
                $obj = array($obj->key => $obj->value);
                return true;
            }
        } elseif (is_array($obj)) {
            $arr = array();
            $needReplacement = true;
            foreach ($obj as &$value) {
                $isAssoc = $this->v2AssociativeArrayUnpacker($value);
                if ($isAssoc) {
                    foreach ($value as $aKey => $aVal) {
                        $arr[$aKey] = $aVal;
                    }
                } else {
                    $needReplacement = false;
                }
            }
            if ($needReplacement) {
                $obj = $arr;
            }
        } elseif (is_object($obj)) {
            $objectKeys = array_keys(get_object_vars($obj));

            foreach ($objectKeys as $key) {
                $this->v2AssociativeArrayUnpacker($obj->$key);
            }
        }
        return false;
    }

    /**
     * Go through mixed and turns it to a correct look.
     *
     * @param Mixed $mixed A link to variable that may contain associative array.
     */
    public function associativeArrayUnpack(&$mixed)
    {
        if (is_array($mixed)) {
            $tmpArr = array();
            foreach ($mixed as $key => $value) {
                if (is_object($value)) {
                    $value = get_object_vars($value);
                    if (count($value) == 2 && isset($value['key']) && isset($value['value'])) {
                        $tmpArr[$value['key']] = $value['value'];
                    }
                }
            }
            if (count($tmpArr)) {
                $mixed = $tmpArr;
            }
        }

        if (is_object($mixed)) {
            $numOfVals = count(get_object_vars($mixed));
            if ($numOfVals == 2 && isset($mixed->key) && isset($mixed->value)) {
                $mixed = get_object_vars($mixed);
                /*
                 * Processing an associative arrays.
                 * $mixed->key = '2'; $mixed->value = '3'; turns to array(2 => '3');
                 */
                $mixed = array($mixed['key'] => $mixed['value']);
            }
        }
    }

    /**
     * Corrects data representation.
     *
     * @param Object $obj - Link to Object
     * @return Object
     */
    public function clearWsiFootprints(&$obj)
    {
        $modifiedKeys = array();

        $objectKeys = array_keys(get_object_vars($obj));

        foreach ($objectKeys as $key) {
            if (is_object($obj->$key) && isset($obj->$key->complexObjectArray)) {
                if (is_array($obj->$key->complexObjectArray)) {
                    $obj->$key = $obj->$key->complexObjectArray;
                } else { // for one element array
                    $obj->$key = array($obj->$key->complexObjectArray);
                }
                $modifiedKeys[] = $key;
            }
        }
        return $modifiedKeys;
    }

    /**
     * For the WSI, generates an response object.
     *
     * @param mixed $mixed - Link to Object
     * @return mixed
     */
    public function wsiArrayPacker($mixed)
    {
        if (is_array($mixed)) {
            $arrKeys = array_keys($mixed);
            $isDigit = false;
            foreach ($arrKeys as $key) {
                if (is_int($key)) {
                    $isDigit = true;
                    break;
                }
            }
            if ($isDigit) {
                $mixed = $this->packArrayToObject($mixed);
            } else {
                $mixed = (object)$mixed;
            }
        }
        if (is_object($mixed) && isset($mixed->complexObjectArray)) {
            foreach ($mixed->complexObjectArray as $k => $v) {
                $mixed->complexObjectArray[$k] = $this->wsiArrayPacker($v);
            }
        }
        return $mixed;
    }

    /**
     * For response to the WSI, generates an object from array.
     *
     * @param Array $arr - Link to Object
     * @return Object
     */
    public function packArrayToObject(Array $arr)
    {
        $obj = new stdClass();
        $obj->complexObjectArray = $arr;
        return $obj;
    }

    /**
     * Convert objects and arrays to array recursively
     *
     * @param  array|object $data
     * @return void
     */
    public function toArray(&$data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_array($value) or is_object($value)) {
                    $this->toArray($value);
                }
            }
        }
    }

    /**
     * Parse filters and format them to be applicable for collection filtration
     *
     * @param null|object|array $filters
     * @param array $fieldsMap Map of field names in format: array('field_name_in_filter' => 'field_name_in_db')
     * @return array
     */
    public function parseFilters($filters, $fieldsMap = null)
    {
        // if filters are used in SOAP they must be represented in array format to be used for collection filtration
        if (is_object($filters)) {
            $parsedFilters = array();
            // parse simple filter
            if (isset($filters->filter) && is_array($filters->filter)) {
                foreach ($filters->filter as $field => $value) {
                    if (is_object($value) && isset($value->key) && isset($value->value)) {
                        $parsedFilters[$value->key] = $value->value;
                    } else {
                        $parsedFilters[$field] = $value;
                    }
                }
            }
            // parse complex filter
            // @codingStandardsIgnoreStart
            if (isset($filters->complex_filter) && is_array($filters->complex_filter)) {
                $parsedFilters = $this->_parseComplexFilter($filters->complex_filter);
            }
            // @codingStandardsIgnoreEnd

            $filters = $parsedFilters;
        }
        // make sure that method result is always array
        if (!is_array($filters)) {
            $filters = array();
        }
        // apply fields mapping
        if (isset($fieldsMap) && is_array($fieldsMap)) {
            foreach ($filters as $field => $value) {
                if (isset($fieldsMap[$field])) {
                    unset($filters[$field]);
                    $field = $fieldsMap[$field];
                    $filters[$field] = $value;
                }
            }
        }
        return $filters;
    }

    /**
     * Parses complex filter, which may contain several nodes, e.g. when user want to fetch orders which were updated
     * between two dates.
     *
     * @param mixed $complexFilter
     *
     * @return array
     */
    protected function _parseComplexFilter($complexFilter)
    {
        $parsedFilters = array();

        foreach ($complexFilter as $filter) {
            if (!isset($filter->key) || !isset($filter->value)) {
                continue;
            }

            list($fieldName, $condition) = array($filter->key, $filter->value);
            $condition = $this->formatFilterConditionValue($condition->key, $condition->value);

            if (array_key_exists($fieldName, $parsedFilters)) {
                $parsedFilters[$fieldName] += $condition;
            } else {
                $parsedFilters[$fieldName] = $condition;
            }
        }

        return $parsedFilters;
    }

    /**
     * Check if API is working in SOAP WS-I compliant mode.
     *
     * @return bool
     */
    public function isWsiCompliant()
    {
        $pathInfo = $this->_request->getPathInfo();
        $pathParts = explode('/', trim($pathInfo, '/'));
        $controllerPosition = 1;
        if (isset($pathParts[$controllerPosition]) && $pathParts[$controllerPosition] == 'soap_wsi') {
            $isWsiCompliant = true;
        } else {
            $isWsiCompliant = false;
        }
        return $isWsiCompliant;
    }

    /**
     * Convert condition value from the string into the array
     * for the condition operators that require value to be an array.
     * Condition value is changed by reference
     *
     * @param string $conditionOperator
     * @param string $conditionValue
     * @return array
     */
    public function formatFilterConditionValue($conditionOperator, $conditionValue)
    {
        if (is_string($conditionOperator) && in_array($conditionOperator, array('in', 'nin', 'finset'))
            && is_string($conditionValue)
        ) {
            $delimiter = ',';
            $conditionValue = explode($delimiter, $conditionValue);
        }

        return array($conditionOperator => $conditionValue);
    }

    /**
     * Check if attribute is allowed to be used.
     *
     * @param string $attributeCode
     * @param string $type
     * @param array $ignoredAttributes
     * @param array $attributes
     * @return bool
     */
    public function isAttributeAllowed($attributeCode, $type, $ignoredAttributes, array $attributes = null)
    {
        if (!empty($attributes) && !(in_array($attributeCode, $attributes))) {
            return false;
        }
        if (isset($ignoredAttributes['global']) && in_array($attributeCode, $ignoredAttributes['global'])) {
            return false;
        }
        if (isset($ignoredAttributes[$type]) && in_array($attributeCode, $ignoredAttributes[$type])) {
            return false;
        }
        return true;
    }
}
