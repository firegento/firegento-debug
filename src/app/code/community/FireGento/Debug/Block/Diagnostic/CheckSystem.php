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
}
