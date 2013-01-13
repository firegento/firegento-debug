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
 * Diagnostic Controller
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_DiagnosticController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('Index') . ' / '. 'FIREGENTO');
        $this->renderLayout();
    }

    /**
     * activationAction
     *
     * Activates/Deactivates an extension via the Magento adminhtml
     *
     * @return void
     */
    public function activationAction()
    {
        $name = $this->getRequest()->getParam('name', false);
        $status = Mage::helper('firegento/firegento')->deactivateModule($name);
        $this->_getSession()->addNotice($status);
        $this->_redirect('*/*/checkModules');
    }

    /**
     * checkModulesAction
     *
     * Checks all modules in the Magento system and display them
     * in a grid.
     *
     * @return void
     */
    public function checkModulesAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('Check Modules') . ' / '. 'FIREGENTO');
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
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('Check Rewrites') . ' / '. 'FIREGENTO');
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
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('Check Events') . ' / '. 'FIREGENTO');
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
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('Check System') . ' / '. 'FIREGENTO');
        $this->renderLayout();
    }

    /**
     * Displays a phpinfo() output.
     *
     * @return void
     */
    public function phpinfoAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento');
        $this->_title($this->__('phpinfo') . ' / '. 'FIREGENTO');
        $this->renderLayout();
    }
}
