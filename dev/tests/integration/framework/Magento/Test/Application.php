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
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Encapsulates application installation, initialization and uninstall
 *
 * @todo Implement MAGETWO-1689: Standard Installation Method for Integration Tests
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Test_Application
{
    /**
     * Default application area
     */
    const DEFAULT_APP_AREA = 'global';

    /**
     * DB vendor adapter instance
     *
     * @var Magento_Test_Db_DbAbstract
     */
    protected $_db;

    /**
     * @var Varien_Simplexml_Element
     */
    protected $_localXml;

    /**
     * Application *.xml configuration files
     *
     * @var array
     */
    protected $_globalEtcFiles;

    /**
     * Module declaration *.xml configuration files
     *
     * @var array
     */
    protected $_moduleEtcFiles;

    /**
     * Installation destination directory
     *
     * @var string
     */
    protected $_installDir;

    /**
     * Installation destination directory with configuration files
     *
     * @var string
     */
    protected $_installEtcDir;

    /**
     * Application initialization parameters
     *
     * @var array
     */
    protected $_initParams = array();

    /**
     * Mode to run application
     *
     * @var string
     */
    protected $_appMode;

    /**
     * Application area
     *
     * @var null
     */
    protected $_appArea = null;

    /**
     * Constructor
     *
     * @param Magento_Test_Db_DbAbstract $dbInstance
     * @param string $installDir
     * @param Varien_Simplexml_Element $localXml
     * @param array $globalEtcFiles
     * @param array $moduleEtcFiles
     * @param string $appMode
     */
    public function __construct(
        Magento_Test_Db_DbAbstract $dbInstance, $installDir, Varien_Simplexml_Element $localXml,
        array $globalEtcFiles, array $moduleEtcFiles, $appMode
    ) {
        $this->_db              = $dbInstance;
        $this->_localXml        = $localXml;
        $this->_globalEtcFiles  = $globalEtcFiles;
        $this->_moduleEtcFiles  = $moduleEtcFiles;
        $this->_appMode = $appMode;

        $this->_installDir = $installDir;
        $this->_installEtcDir = "$installDir/etc";

        $generationDir = "$installDir/generation";
        $this->_initParams = array(
            Mage::PARAM_APP_DIRS => array(
                Mage_Core_Model_Dir::CONFIG      => $this->_installEtcDir,
                Mage_Core_Model_Dir::VAR_DIR     => $installDir,
                Mage_Core_Model_Dir::MEDIA       => "$installDir/media",
                Mage_Core_Model_Dir::STATIC_VIEW => "$installDir/pub_static",
                Mage_Core_Model_Dir::PUB_VIEW_CACHE => "$installDir/pub_cache",
                Mage_Core_Model_Dir::GENERATION => $generationDir,
            ),
            Mage::PARAM_MODE => $appMode
        );
    }

    /**
     * Retrieve the database adapter instance
     *
     * @return Magento_Test_Db_DbAbstract
     */
    public function getDbInstance()
    {
        return $this->_db;
    }

    /**
     * Get directory path with application instance custom data (cache, temporary directory, etc...)
     */
    public function getInstallDir()
    {
        return $this->_installDir;
    }

    /**
     * Retrieve application initialization parameters
     *
     * @return array
     */
    public function getInitParams()
    {
        return $this->_initParams;
    }

    /**
     * Weather the application is installed or not
     *
     * @return bool
     */
    public function isInstalled()
    {
        return is_file($this->_installEtcDir . '/local.xml');
    }

    /**
     * Initialize an already installed application
     *
     * @param array $overriddenParams
     */
    public function initialize($overriddenParams = array())
    {
        $overriddenParams[Mage::PARAM_BASEDIR] = BP;
        $overriddenParams[Mage::PARAM_MODE] = $this->_appMode;
        Mage::$headersSentThrowsException = false;
        $config = new Mage_Core_Model_Config_Primary(BP, $this->_customizeParams($overriddenParams));
        if (!Mage::getObjectManager()) {
            $objectManager = new Magento_Test_ObjectManager($config, new Magento_Test_ObjectManager_Config());
            Mage::setObjectManager($objectManager);
        } else {
            $objectManager = Mage::getObjectManager();
            $config->configure($objectManager);
            $objectManager->addSharedInstance($config, 'Mage_Core_Model_Config_Primary');
            $objectManager->addSharedInstance($config->getDirectories(), 'Mage_Core_Model_Dir');
        }

        $objectManager->get('Mage_Core_Model_Resource')
            ->setResourceConfig(Mage::getObjectManager()->get('Mage_Core_Model_Config_Resource'));
        $objectManager->get('Mage_Core_Model_Resource')
            ->setCache(Mage::getObjectManager()->get('Mage_Core_Model_CacheInterface'));

        /** @var Mage_Core_Model_Dir_Verification $verification */
        $verification = $objectManager->get('Mage_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();

        $this->loadArea(Magento_Test_Application::DEFAULT_APP_AREA);
    }

    /**
     * Reset and initialize again an already installed application
     *
     * @param array $overriddenParams
     */
    public function reinitialize(array $overriddenParams = array())
    {
        $this->_resetApp();
        $this->initialize($overriddenParams);
    }

    /**
     * Run application normally, but with encapsulated initialization options
     *
     * @param Magento_Test_Request $request
     * @param Magento_Test_Response $response
     */
    public function run(Magento_Test_Request $request, Magento_Test_Response $response)
    {
        $composer = Mage::getObjectManager();
        $handler = $composer->get('Magento_Http_Handler_Composite');
        $handler->handle($request, $response);
    }

    /**
     * Cleanup both the database and the file system
     */
    public function cleanup()
    {
        $this->_db->cleanup();
        $this->_cleanupFilesystem();
    }

    /**
     * Install an application
     *
     * @param string $adminUserName
     * @param string $adminPassword
     * @param string $adminRoleName
     * @throws Magento_Exception
     */
    public function install($adminUserName, $adminPassword, $adminRoleName)
    {
        $this->_ensureDirExists($this->_installDir);
        $this->_ensureDirExists($this->_installEtcDir);
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'media');
        $this->_ensureDirExists($this->_installDir . DIRECTORY_SEPARATOR . 'static');

        /* Copy configuration files */
        $etcDirsToFilesMap = array(
            $this->_installEtcDir              => $this->_globalEtcFiles,
            $this->_installEtcDir . '/modules' => $this->_moduleEtcFiles,
        );
        foreach ($etcDirsToFilesMap as $targetEtcDir => $sourceEtcFiles) {
            $this->_ensureDirExists($targetEtcDir);
            foreach ($sourceEtcFiles as $sourceEtcFile) {
                $targetEtcFile = $targetEtcDir . '/' . basename($sourceEtcFile);
                copy($sourceEtcFile, $targetEtcFile);
            }
        }

        /* Make sure that local.xml contains an invalid installation date */
        $installDate = (string)$this->_localXml->global->install->date;
        if ($installDate && strtotime($installDate)) {
            throw new Magento_Exception('Local configuration must contain an invalid installation date.');
        }

        /* Replace local.xml */
        $targetLocalXml = $this->_installEtcDir . '/local.xml';
        $this->_localXml->asNiceXml($targetLocalXml);

        /* Initialize an application in non-installed mode */
        $this->initialize();

        /* Run all install and data-install scripts */
        /** @var $updater Mage_Core_Model_Db_Updater */
        $updater = Mage::getObjectManager()->get('Mage_Core_Model_Db_Updater');
        $updater->updateScheme();
        $updater->updateData();

        /* Enable cross-cutting caches in order to improve tests performance */
        /** @var $cacheTypes Mage_Core_Model_Cache_Types */
        $cacheTypes = Mage::getObjectManager()->get('Mage_Core_Model_Cache_Types');
        $cacheTypes->setEnabled(Mage_Core_Model_Cache_Type_Config::TYPE_IDENTIFIER, true);
        $cacheTypes->setEnabled(Mage_Core_Model_Cache_Type_Layout::TYPE_IDENTIFIER, true);
        $cacheTypes->setEnabled(Mage_Core_Model_Cache_Type_Translate::TYPE_IDENTIFIER, true);
        $cacheTypes->setEnabled(Mage_Eav_Model_Cache_Type::TYPE_IDENTIFIER, true);
        $cacheTypes->persist();

        /* Fill installation date in local.xml to indicate that application is installed */
        $localXml = file_get_contents($targetLocalXml);
        $localXml = str_replace($installDate, date('r'), $localXml, $replacementCount);
        if ($replacementCount != 1) {
            throw new Magento_Exception("Unable to replace installation date properly in '$targetLocalXml' file.");
        }
        file_put_contents($targetLocalXml, $localXml, LOCK_EX);

        /* Add predefined admin user to the system */
        $this->_createAdminUser($adminUserName, $adminPassword, $adminRoleName);

        /* Switch an application to installed mode */
        $this->initialize();
    }

    /**
     * Sub-routine for merging custom parameters with the ones defined in object state
     *
     * @param array $params
     * @return array
     */
    private function _customizeParams($params)
    {
        return array_replace_recursive($this->_initParams, $params);
    }

    /**
     * Reset application global state
     */
    protected function _resetApp()
    {
        /** @var $objectManager Magento_Test_ObjectManager */
        $objectManager = Mage::getObjectManager();
        $objectManager->clearCache();

        $resource = Mage::registry('_singleton/Mage_Core_Model_Resource');

        Mage::reset();
        Mage::setObjectManager($objectManager);
        Varien_Data_Form::setElementRenderer(null);
        Varien_Data_Form::setFieldsetRenderer(null);
        Varien_Data_Form::setFieldsetElementRenderer(null);
        $this->_appArea = null;

        if ($resource) {
            Mage::register('_singleton/Mage_Core_Model_Resource', $resource);
        }
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @throws Magento_Exception
     * @param string $dir
     */
    protected function _ensureDirExists($dir)
    {
        if (!file_exists($dir)) {
            $old = umask(0);
            mkdir($dir, 0777);
            umask($old);
        } else if (!is_dir($dir)) {
            throw new Magento_Exception("'$dir' is not a directory.");
        }
    }

    /**
     * Remove temporary files and directories from the filesystem
     */
    protected function _cleanupFilesystem()
    {
        Varien_Io_File::rmdirRecursive($this->_installDir);
    }

    /**
     * Creates predefined admin user to be used by tests, where admin session is required
     *
     * @param string $adminUserName
     * @param string $adminPassword
     * @param string $adminRoleName
     */
    protected function _createAdminUser($adminUserName, $adminPassword, $adminRoleName)
    {
        /** @var $user Mage_User_Model_User */
        $user = mage::getModel('Mage_User_Model_User');
        $user->setData(array(
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'email'     => 'admin@example.com',
            'username'  => $adminUserName,
            'password'  => $adminPassword,
            'is_active' => 1
        ));
        $user->save();

        /** @var $roleAdmin Mage_User_Model_Role */
        $roleAdmin = Mage::getModel('Mage_User_Model_Role');
        $roleAdmin->load($adminRoleName, 'role_name');

        /** @var $roleUser Mage_User_Model_Role */
        $roleUser = Mage::getModel('Mage_User_Model_Role');
        $roleUser->setData(array(
            'parent_id'  => $roleAdmin->getId(),
            'tree_level' => $roleAdmin->getTreeLevel() + 1,
            'role_type'  => Mage_User_Model_Acl_Role_User::ROLE_TYPE,
            'user_id'    => $user->getId(),
            'role_name'  => $user->getFirstname(),
        ));
        $roleUser->save();
    }

    /**
     * Ge current application area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_appArea;
    }

    /**
     * Load application area
     *
     * @param $area
     */
    public function loadArea($area)
    {
        $this->_appArea = $area;
        if ($area == Magento_Test_Application::DEFAULT_APP_AREA) {
            Mage::app()->loadAreaPart($area, Mage_Core_Model_App_Area::PART_CONFIG);
            Mage::app()->loadAreaPart($area, Mage_Core_Model_App_Area::PART_EVENTS);
        } else {
            Mage::app()->loadArea($area);
            Mage::getConfig()->setCurrentAreaCode($area);
        }
    }
}
