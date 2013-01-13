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
require_once 'FireGento/FireLogger/FireLogger.php';

if (!defined('FIRELOGGER_NO_CONFLICT')) {
    define('FIRELOGGER_NO_CONFLICT', true);
}
if (!defined('FIRELOGGER_NO_EXCEPTION_HANDLER')) {
    define('FIRELOGGER_NO_EXCEPTION_HANDLER', true);
}
if (!defined('FIRELOGGER_NO_ERROR_HANDLER')) {
    define('FIRELOGGER_NO_ERROR_HANDLER', true);
}
/*if (!defined('FIRELOGGER_NO_OUTPUT_HANDLER')) {
    define('FIRELOGGER_NO_OUTPUT_HANDLER', true);
}*/
if (!defined('FIRELOGGER_NO_DEFAULT_LOGGER')) {
    define('FIRELOGGER_NO_DEFAULT_LOGGER', true);
}
/**
 * FireLogger Class
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Model_Log_Firelogger extends FireLogger
{
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $flag = Mage::helper('firegento/log')->isFireloggerAllowed();
        FireLogger::$enabled = $flag;
        if ($flag) {
            parent::__construct('php', 'background-color: #9998d1');
        }
    }
}
