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
        return Mage::helper('firegento/firegento')->checkCaches();
    }

    /**
     * Checks if all indexes are up-to-date
     * 
     * @return string Indexes Message
     */
    public function checkIndizes()
    {
        return Mage::helper('firegento/firegento')->checkIndizes();
    }

    /**
     * Returns a small system check report with some essential properties
     * 
     * @return array Extension Check Result
     */
    public function checkSystem()
    {
        return Mage::helper('firegento/firegento')->checkSystem();
    }
}