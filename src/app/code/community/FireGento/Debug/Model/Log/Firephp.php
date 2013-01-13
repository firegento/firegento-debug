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
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 */
require_once 'FireGento/FirePHP/FirePHP.class.php';
/**
 * FirePHP Class
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 */
class FireGento_Core_Model_Log_Firephp extends FirePHP
{
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        self::getInstance(true);
        $flag = Mage::helper('firegento/log')->isFirephpAllowed();
        self::setEnabled($flag);
    }

    /**
     * Logs the message in the firebug console
     *
     * @param mixed Log Message
     * @see FirePHP::log()
     * @return bool True/False
     */
    public function log($message)
    {
        return self::fb($message);
    }
}
