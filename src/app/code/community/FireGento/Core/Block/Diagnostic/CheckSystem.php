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
 * CheckSystem Grid Container
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $$Id$$
 */
class FireGento_Core_Block_Diagnostic_CheckSystem
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Checks if one or more caches are active
     * 
     * @return string Cache Message
     */
    public function checkCaches()
    {
        $active   = 0;
        $inactive = 0;
        foreach (Mage::app()->getCacheInstance()->getTypes() as $type) {
            $tmp = $type->getData();
            if ($tmp['status']) {
                $active++; 
            } else {
                $inactive++;
            }
        }
        return $this->__(
            '%s caches active, %s caches inactive',
            $active,
            $inactive
        );
    }

    /**
     * Checks if all indexes are up-to-date
     * 
     * @return string Indexes Message
     */
    public function checkIndizes()
    {
        $ready      = 0;
        $processing = 0;
        $reindex    = 0;

        $collection = Mage::getResourceModel('index/process_collection');
        foreach ($collection as $item) {
            $tmp = $item->getData();
            if ($tmp['status'] == 'pending') {
                $ready++;
            } elseif ($tmp['status'] == 'working') {
                $processing++;
            } else {
                $reindex++;
            }
        }

        return $this->__(
            '%s indexes are ready, %s indexes are working, %s indexes need reindex',
            $ready,
            $processing,
            $reindex
        );
    }

    /**
     * Returns a small system check report with some essential properties
     * 
     * @return array Extension Check Result
     */
    public function checkSystem()
    {
        return $this->_extensionCheck(
            array(
                'curl',
                'dom',
                'gd',
                'hash',
                'iconv',
                'mcrypt',
                'pcre',
                'pdo',
                'pdo_mysql',
                'simplexml'
            )
        );
    }

    /**
     * Checks some kind of essential properties
     * 
     * @param string $extensions Extensions to check
     * 
     * @return array Array with failed and passed checks
     */
    protected function _extensionCheck($extensions)
    {
        $fail = array();
        $pass = array();

        if (version_compare(phpversion(), '5.2.0', '<')) {
            $fail[] = 'You need <strong>PHP 5.2.0</strong> (or greater)';
        } else {
            $pass[] = 'You have <strong>PHP 5.2.0</strong> (or greater)';
        }

        if (!ini_get('safe_mode')) {
            $pass[] = 'Safe Mode is <strong>off</strong>';

            $con     = Mage::getSingleton('core/resource')->getConnection('core_read');
            $version = $con->getServerVersion();

            if (version_compare($version, '4.1.20', '<')) {
                $fail[] = 'You need <strong>MySQL 4.1.20</strong> (or greater)';
            } else {
                $pass[] = 'You have <strong>MySQL 4.1.20</strong> (or greater)';
            }
        } else { 
            $fail[] = 'Safe Mode is <strong>on</strong>';  
        }

        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $fail[] = 'You are missing the <strong>'.$extension.'</strong> extension';
            } else {
                $pass[] = 'You have the <strong>'.$extension.'</strong> extension';
            }
        }

        return array('pass' => $pass, 'fail' => $fail);
    }
}