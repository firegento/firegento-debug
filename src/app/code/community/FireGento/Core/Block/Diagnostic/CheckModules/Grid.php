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
 * CheckRewrites Grid
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 */
class FireGento_Core_Block_Diagnostic_CheckModules_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{   
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('check_modules_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * Prepare grid collection
     * 
     * @return FireGento_Core_Block_Diagnostic_CheckRewrites_Grid Grid object
     */
    protected function _prepareCollection()
    {
        // Get the value to sort
        $sortValue = $this->getRequest()->getParam('sort', 'name');
        $sortValue = strtolower($sortValue);

        // Get the direction to sort
        $sortDir   = $this->getRequest()->getParam('dir', 'ASC');
        $sortDir   = strtoupper($sortDir);

        // Get modules and sort them
        $modules = $this->_loadModules();
        $modules = Mage::helper('firegento')->sortMultiDimArr($modules, $sortValue, $sortDir);

        // Add all modules to the collection
        $collection = new Varien_Data_Collection();        
        foreach ($modules as $key => $val) {
            $item = new Varien_Object($val);
            $collection->addItem($item);
        }

        // Set the collection in the grid and return :-)
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * 
     * @return FireGento_Core_Block_Diagnostic_CheckRewrites_Grid Grid object
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            array(
                'header'   => $this->__('Module Name'),
                'align'    => 'left',
                'index'    => 'name',
                'sortable' => true
            )
        );
        $this->addColumn(
            'code_pool',
            array(
                'header'   => $this->__('Code Pool'),
                'align'    => 'left',
                'index'    => 'code_pool',
                'width'    => '80px',
                'sortable' => true
            )
        );
        $this->addColumn(
            'active',
            array(
                'header'         => $this->__('Active'),
                'align'          => 'left',
                'width'          => '100px',
                'index'          => 'active',
                'type'           => 'options',
                'options'        => array(0 => $this->__('False'), 1 => $this->__('True')),
                'frame_callback' => array($this, 'decorateTrueFalse')
            )
        );
        $this->addColumn(
            'path',
            array(
                'header'   => $this->__('Path'),
                'align'    => 'left',
                'index'    => 'path',
                'sortable' => true
            )
        );
        $this->addColumn(
            'path_exists',
            array(
                'header'         => $this->__('Path exists'),
                'align'          => 'left',
                'width'          => '100px',
                'index'          => 'path_exists',
                'type'           => 'options',
                'options'        => array(0 => $this->__('No'), 1 => $this->__('Yes')),
                'frame_callback' => array($this, 'decoratePathExists')
            )
        );
        $this->addColumn(
            'config_exists',
            array(
                'header'         => $this->__('config.xml exists'),
                'align'          => 'left',
                'width'          => '100px',
                'index'          => 'config_exists',
                'type'           => 'options',
                'options'        => array(0 => $this->__('No'), 1 => $this->__('Yes')),
                'frame_callback' => array($this, 'decorateConfigExists')
            )
        );
        $this->addColumn(
            'dependencies',
            array(
                'header'   => $this->__('Module Dependencies'),
                'align'    => 'left',
                'index'    => 'dependencies',
                'width'    => '350px',
                'sortable' => false
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Decorate the active column values
     *
     * @param string                                   $value Check result
     * @param Mage_Catalog_Model_Product|Varien_Object $row   Current row
     *
     * @return string Cell content
     */
    public function decorateTrueFalse($value, $row)
    {
        $class = '';
        if ($row->getActive()) {
            $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
        }
        return $cell;
    }

	/**
     * Decorate the path_exists column values
     *
     * @param string                                   $value Check result
     * @param Mage_Catalog_Model_Product|Varien_Object $row   Current row
     *
     * @return string Cell content
     */
    public function decoratePathExists($value, $row)
    {
        $class = '';
        if ($row->getPathExists()) {
            $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
        }
        return $cell;
    }

	/**
     * Decorate the config_exists column values
     *
     * @param string                                   $value Check result
     * @param Mage_Catalog_Model_Product|Varien_Object $row   Current row
     *
     * @return string Cell content
     */
    public function decorateConfigExists($value, $row)
    {
        $class = '';
        if ($row->getConfigExists()) {
            $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
        }
        return $cell;
    }

    /**
     * Get row edit url
     *
     * @param Mage_Catalog_Model_Product|Varien_Object $row Current row
     * 
     * @return string|boolean Row url | false = no url
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Loads the module configurations and checks for some criteria and 
     * returns an array with the current modules in the Magento instance.
     * 
     * @return array Modules
     */
    private function _loadModules()
    {
        $modules = array();
        $config = Mage::getConfig();
		foreach ($config->getNode('modules')->children() as $moduleName => $item) {
		    $active       = ($item->active == 'true') ? true : false;
            $codePool     = (string) $config->getModuleConfig($item->getName())->codePool;
			$path         = $config->getOptions()->getCodeDir() . DS . $codePool . DS . uc_words($item->getName(), DS);
			$pathExists   = file_exists($path);
			$pathExists   = $pathExists ? true : false;
			$configExists = file_exists($path . '/etc/config.xml');
			$configExists = $configExists ? true : false;

			$dependencies = '-';
            if ($item->depends) {
		        $depends = array();
                foreach ($item->depends->children() as $depend) {
                    $depends[] = $depend->getName();
                }
                if (is_array($depends) && count($depends) > 0) {
                    $dependencies = implode(', ', $depends);
                }
            }

			$modules[$item->getName()] = array(
			    'name'          => $item->getName(),
			    'active'        => $active,
			    'code_pool'     => $codePool,
			    'path'          => $path,
			    'path_exists'   => $pathExists,
			    'config_exists' => $configExists,
			    'dependencies'  => $dependencies
			);
		}
		return $modules;
    }
}