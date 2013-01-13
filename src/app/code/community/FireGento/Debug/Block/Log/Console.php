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
 * Block for terminal like console in Magento backend
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Block_Log_Console extends Mage_Core_Block_Template
{
    /**
     * Constructor for Block
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'log_console';
        $this->_blockGroup = 'firegento';
        $this->_headerText = Mage::helper('firegento')->__('Log Console');
        $this->setTemplate('firegento/debug/log/console.phtml');
    }

    /**
     * Returns the log files of the var/log directory
     *
     * @return array File List
     */
    public function getLogFiles()
    {
        // Check if path exists
        $path = Mage::getBaseDir('var') . DS . 'log' . DS;
        if (!file_exists($path)) {
            return array();
        }

        // Return file list
        $io = new Varien_Io_File();
        $io->open(
            array(
                'path' => $path
            )
        );

        return $io->ls(Varien_Io_File::GREP_FILES);
    }

    /**
     * Adds the secure key to the url
     *
     * @return string Secure Url
     */
    public function getSecureUrl($fileName=null)
    {
        $params = array();
        if (Mage::getStoreConfigFlag('admin/security/use_form_key')) {
            $params['key'] = Mage::getSingleton('adminhtml/url')->getSecretKey('firegento_log', 'index');
        }
        if (!is_null($fileName)) {
            $params['file'] = $fileName;
        }

        return $this->getUrl('*/*/*', $params);
    }
}
