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
 * Log Controller
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 */
class FireGento_Core_LogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Sends the ajax response for the console output
     * 
     * @return void
     */
    public function tailAction()
    {
         $startPos = $this->getRequest()->getParam('position');

         $file = $this->getRequest()->getParam('file');
         $filename = Mage::getBaseDir('var') . DS . 'log' . DS . $file;
         if (!$filename || !file_exists($filename) || $file == '') {
             return '';
         }

         $handle   = fopen($filename, 'r');
         $filesize = filesize($filename);

         $firstTime = false;

         if ($startPos == 0) {
             $firstTime    = true;
             $lengthBefore = 1000;
             fseek($handle, -$lengthBefore, SEEK_END);

             $text    = fread($handle, $filesize);
             $updates = '[...]' . substr($text, strpos($text, "\n"), strlen($text));
             $newPos  = ftell($handle);
         } else {
             fseek($handle, $startPos, SEEK_SET);
             $updates = fread($handle, $filesize);
             $newPos = ftell($handle);
         }

         if ($updates != NULL) {
            $response = Zend_Json::encode(array('text' => $updates, 'position' => $newPos, 'firsttime' => $firstTime)); 
            print $response;
         }
    }

    /**
     * Validate Secret Key
     *
     * @return bool
     */
    protected function _validateSecretKey()
    {
        return true;
    }    

    /**
     * Displays the log in a "console"
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('firegento_core');
        $this->_title($this->__('Index') . ' / '. 'FIREGENTO');
        $this->_addContent($this->getLayout()->createBlock('firegento_core/log_console'));
        $this->renderLayout();
    }

    /**
     * Enter description here ...
     * 
     * @return void
     */
    public function testAction()
    {
        Mage::log('----------');
        $array = array(
            'namespace'  => 'FireGento',
            'extension'  => 'Core',
            'controller' => 'LogController',
            'action'     => 'testAction'
        );
        Mage::log($array);
    }
}