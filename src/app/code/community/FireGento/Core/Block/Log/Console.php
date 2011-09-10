<?php

class FireGento_Core_Block_Log_Console extends Mage_Core_Block_Template
{
	/**
	 * Constructor for Block
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->_controller = 'log_console';
		$this->_blockGroup = 'firegento_core';
		$this->_headerText = Mage::helper('firegento_core')->__('Log Console');
		$this->setTemplate('firegento/core/log/console.phtml');    
	}

	/**
	 * Returns the log files of the var/log directory
	 * 
	 * @return array File List
	 */
	public function getLogFiles()
	{
	    $io = new Varien_Io_File();
	    $io->open(
	        array(
	            'path' => Mage::getBaseDir('var') . DS . 'log' . DS
	        )
	    );
	    return $io->ls(Varien_Io_File::GREP_FILES);
	}
	
	public function getSecureUrl($fileName = null)
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