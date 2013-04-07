<?php
/**
 * This file is part of the FIREGENTO project.
 *
 * FireGento_Debug is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
/**
 * CheckSystem Grid Container
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Block_Diagnostic_CheckSystem
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Varien_Object
     */
    protected $_system;

    /**
     * @return FireGento_Debug_Helper_Firegento
     */
    protected function _getHelper()
    {
        return Mage::helper('firegento/firegento');
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getDb()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $system = new Varien_Object();

        /*
         * MAGENTO
         */
        $magento = array(
            'edition' => Mage::getEdition(),
            'version' => Mage::getVersion(),
            'developer_mode' => Mage::getIsDeveloperMode(),
            'secret_key' => Mage::getStoreConfigFlag('admin/security/use_form_key'),
            'flat_catalog_category' => Mage::getStoreConfigFlag('catalog/frontend/flat_catalog_category'),
            'flat_catalog_product' => Mage::getStoreConfigFlag('catalog/frontend/flat_catalog_product'),
            'cache_status' => $this->_getHelper()->checkCaches(),
            'index_status' => $this->_getHelper()->checkIndizes()
        );
        $system->setData('magento', new Varien_Object($magento));

        /*
         * SERVER
         */

        $server = array(
            'domain' => isset($_SERVER['HTTP_HOST']) ? str_replace('www.', '', $_SERVER['HTTP_HOST']) : null,
            'ip' => $_SERVER['SERVER_ADDR'],
            'dir' => Mage::getBaseDir(),
            'info' => php_uname(),

        );
        $system->setData('server', new Varien_Object($server));

        /*
         * PHP
         */

        $php = array(
            'version' => @phpversion(),
            'server_api' => @php_sapi_name(),
            'memory_limit' => @ini_get('memory_limit'),
            'max_execution_time' => @ini_get('max_execution_time')
        );
        $system->setData('php', new Varien_Object($php));

        /*
         * MySQL
         */

        // Get MySQL Server API
        $connection = $this->_getDb()->getConnection();
        if ($connection instanceof PDO) {
            $mysqlServerApi = $connection->getAttribute(PDO::ATTR_CLIENT_VERSION);
        } else {
            $mysqlServerApi = 'n/a';
        }

        // Get table prefix
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        if (empty($tablePrefix)) {
            $tablePrefix = $this->__('Disabled');
        }

        // Get MySQL vars
        $sqlQuery = "SHOW VARIABLES WHERE `Variable_name` IN ('connect_timeout','wait_timeout')";
        $sqlResult = $this->_getDb()->fetchAll($sqlQuery);
        $mysqlVars = array();
        foreach ($sqlResult as $mysqlVar) {
            $mysqlVars[$mysqlVar['Variable_name']] = $mysqlVar['Value'];
        }

        $mysql = array(
            'version' => $this->_getDb()->getServerVersion(),
            'server_api' => $mysqlServerApi,
            'database_name' => (string) Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname'),
            'database_tables' => count($this->_getDb()->listTables()),
            'table_prefix' => $tablePrefix,
            'connection_timeout' => $mysqlVars['connect_timeout'].' sec.',
            'wait_timeout' => $mysqlVars['wait_timeout'].' sec.'
        );
        $system->setData('mysql', new Varien_Object($mysql));

        /*
         * System Requirements
         */

        $safeMode = (@ini_get('safe_mode')) ? true : false;
        $memoryLimit = $php['memory_limit'];
        $memoryLimit = substr($memoryLimit, 0, strlen($memoryLimit) -1);
        $phpCurl = @extension_loaded('curl');
        $phpDom = @extension_loaded('dom');
        $phpGd = @extension_loaded('gd');
        $phpHash = @extension_loaded('hash');
        $phpIconv = @extension_loaded('iconv');
        $phpMcrypt = @extension_loaded('mcrypt');
        $phpPcre = @extension_loaded('pcre');
        $phpPdo = @extension_loaded('pdo');
        $phpPdoMysql = @extension_loaded('pdo_mysql');
        $phpSimplexml = @extension_loaded('simplexml');

        $requirements = array(
            'php_version' => array(
                'label' => 'PHP Version:',
                'recommended_value' => '>= 5.3.0',
                'current_value' => $php['version'],
                'result' => version_compare($php['version'], '5.3.0', '>=')
            ),
            'mysql_version' => array(
                'label' => 'MySQL Version:',
                'recommended_value' => '>= 4.1.20',
                'current_value' => $mysql['version'],
                'result' => version_compare($mysql['version'], '4.1.20', '>='),
            ),
            'safe_mode' => array(
                'label' => 'Safe Mode:',
                'recommended_value' => $this->renderBooleanField(false),
                'current_value' => $this->renderBooleanField($safeMode),
                'result' => !$safeMode,
            ),
            'memory_limit' => array(
                'label' => 'Memory Limit:',
                'recommended_value' => '>= 256M',
                'current_value' => $php['memory_limit'],
                'result' => ($memoryLimit >= 256),
            ),
            'max_execution_time' => array(
                'label' => 'Max. Execution Time:',
                'recommended_value' => '>= 360 sec.',
                'current_value' => $php['max_execution_time'],
                'result' => ($php['max_execution_time'] >= 360),
            ),
            'curl' => array(
                'label' => 'curl',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpCurl),
                'result' => $phpCurl,
            ),
            'dom' => array(
                'label' => 'dom',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpDom),
                'result' => $phpDom,
            ),
            'gd' => array(
                'label' => 'gd',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpGd),
                'result' => $phpGd,
            ),
            'hash' => array(
                'label' => 'hash',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpHash),
                'result' => $phpHash,
            ),
            'iconv' => array(
                'label' => 'iconv',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpIconv),
                'result' => $phpIconv,
            ),
            'mcrypt' => array(
                'label' => 'mcrypt',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpMcrypt),
                'result' => $phpMcrypt,
            ),
            'pcre' => array(
                'label' => 'pcre',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpPcre),
                'result' => $phpPcre,
            ),
            'pdo' => array(
                'label' => 'pdo',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpPdo),
                'result' => $phpPdo,
            ),
            'pdo_mysql' => array(
                'label' => 'pdo_mysql',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpPdoMysql),
                'result' => $phpPdoMysql,
            ),
            'simplexml' => array(
                'label' => 'simplexml',
                'recommended_value' => $this->renderBooleanField(true),
                'current_value' => $this->renderBooleanField($phpSimplexml),
                'result' => $phpSimplexml,
            )
        );
        $system->setData('requirements', new Varien_Object($requirements));

        $this->_system = $system;
    }

    /**
     * @return Varien_Object
     */
    public function getSystem()
    {
        return $this->_system;
    }

    /**
     * @param boolen $value
     * @return string
     */
    public function renderBooleanField($value)
    {
        if ($value) {
            return $this->__('Enabled');
        }
        return $this->__('Disabled');
    }

    /**
     * @param $result
     * @return string
     */
    public function renderRequirementValue($result)
    {
        if ($result) {
            return 'requirement-passed';
        }
        return 'requirement-failed';
    }
}
