<?php
/**                                                                       
 * This file is part of the FIREGENTO project.
 * 
 * FireGento_Core is free software; you can redistribute it and/or 
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
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $$Id$$
 */
/**
 * Data Helper for different helper functionalities
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $$Id$$
 */
class FireGento_Core_Helper_Log extends Mage_Core_Helper_Abstract
{
    const XML_PATH_FIREGENTO_LOG_FILE   = 'firegento/log/log_file';
    const XML_PATH_FIREGENTO_FORCE_LOG  = 'firegento/log/force_log';
    const XML_PATH_FIREGENTO_FIRELOGGER = 'firegento/log/firelogger';
    const XML_PATH_FIREGENTO_FIREPHP    = 'firegento/log/firephp';

    /**
     * Logs the given message in the specified log file..
     * 
     * @param mixed $message Log Message
     * 
     * @return FireGento_Core_Helper_Log Self.
     */
    public function log($message)
    {
        $logFile  = Mage::getStoreConfig(self::XML_PATH_FIREGENTO_LOG_FILE);
        $forceLog = Mage::getStoreConfigFlag(self::XML_PATH_FIREGENTO_FORCE_LOG);
        if ($logFile && strlen($logFile) > 0) {
            Mage::log($message, Zend_Log::DEBUG, $logFile, $forceLog);
        }
        return $this;
    }

    /**
     * Checks if the given message is an instance of Varien_Object and calls
     * the debug()-Method. This is very useful for large objects like
     * sales/quote, sales/order, ... for instance.
     * 
     * @param mixed $message Log Message
     * 
     * @return FireGento_Core_Helper_Log Self.
     */
    public function debug($message)
    {
        if ($message instanceof Varien_Object) {
            $message = $message->debug();
        }
        $this->log($message);
        return $this;
    }

    /**
     * Logs the message in the Firefox addon..
     * 
     * @param mixed $message Log Message
     * 
     * @return FireGento_Core_Helper_Log Self.
     */
    public function firelogger($message)
    {
        $flagFirelogger = $this->isFireloggerAllowed();
        $flagPhpVersion = version_compare(phpversion(), '5.3.0', '>');
        if ($flagFirelogger && $flagPhpVersion) {
            Mage::getSingleton('firegento/log_firelogger')->log($message);
        }
        return $this;
    }

    /**
     * Logs the message in the Firefox addon..
     * 
     * @param mixed $message Log Message
     * 
     * @return FireGento_Core_Helper_Log Self.
     */
    public function firephp($message)
    {
        $flagFirePhp    = $this->isFirephpAllowed();
        $flagPhpVersion = version_compare(phpversion(), '5.0.0', '>');
        if ($flagFirePhp && $flagPhpVersion) {
            Mage::getSingleton('firegento/log_firephp')->log($message);
        }
        return $this;
    }

    /**
     * Checks if firelogger is allowed
     * 
     * @return bool Allowed/Not allowed
     */
    public function isFireloggerAllowed()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FIREGENTO_FIRELOGGER);
    }

    /**
     * Checks if firephp is allowed
     * 
     * @return bool Allowed/Not allowed
     */
    public function isFirephpAllowed()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FIREGENTO_FIREPHP);
    }
}