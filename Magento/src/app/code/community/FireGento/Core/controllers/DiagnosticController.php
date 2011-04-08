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
 * @version   $Id:$
 */
/**
 * Diagnostic Controller
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 */
class FireGento_Core_DiagnosticController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * indexAction
     * 
     * Does nothing.
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->renderLayout();
    }

    /**
     * checkRewritesAction
     * 
     * Checks for rewrites in the Magento system and display them
     * in a grid.
     * 
     * @return void
     */
    public function checkRewritesAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->renderLayout();
    }

    /**
     * checkEventsAction
     * 
     * Checks for events in the Magento system and display all of them
     * in a grid and the class name of the specific observer.
     * 
     * @return void
     */
    public function checkEventsAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->renderLayout();
    }

    /**
     * checkSystemAction
     * 
     * Displays the status of important Magento settings which can be
     * a potential error cause.
     * 
     * @return void
     */
    public function checkSystemAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->renderLayout();
    }

    /**
     * phpinfoAction
     * 
     * Displays a phpinfo() output.
     * 
     * @return void
     */
    public function phpinfoAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->renderLayout();
    }
}