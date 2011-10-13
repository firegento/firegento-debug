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
class FireGento_Core_Block_Diagnostic_CheckRewrites_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('check_rewrites_grid');
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
        $collection = new Varien_Data_Collection();        
        $rewrites = $this->_loadModules();

        foreach ($rewrites as $rewriteNodes) {
            foreach ($rewriteNodes as $n) {
                $nParent = $n->xpath('..');
                $module = (string) $nParent[0]->getName();
                $nParent2 = $nParent[0]->xpath('..');
                $component = (string) $nParent2[0]->getName();
                
                if (!in_array($component, array('blocks', 'helpers', 'models'))) {
                    continue;
                }

                $pathNodes = $n->children();
                foreach ($pathNodes as $pathNode) {
                    $path = (string) $pathNode->getName();
                    $completePath = $module.'/'.$path;

                    $rewriteClassName = (string) $pathNode;
                        
                    $instance = Mage::getConfig()->getGroupedClassName(
                        substr($component, 0, -1),
                        $completePath
                    );
                    
                    $collection->addItem(
                        new Varien_Object(
                            array(
                                'path' => $completePath,
                                'rewrite_class' => $rewriteClassName,
                                'active_class' => $instance,
                                'status' => ($instance == $rewriteClassName)
                            )
                        )
                    );
                }
            }
        }
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
        $baseUrl = $this->getUrl();
        $this->addColumn(
            'path',
            array(
                'header'    => $this->__('Path'),
                'align'     => 'left',
                'index'     => 'path',
                'sortable'  => false,
            )
        );
        $this->addColumn(
            'rewrite_class',
            array(
                'header'    => $this->__('Rewrite Class'),
                'width'     => '200',
                'align'     => 'left',
                'index'     => 'rewrite_class',
                'sortable'  => false,
            )
        );
        $this->addColumn(
            'active_class',
            array(
                'header'    => $this->__('Active Class'),
                'width'     => '200',
                'align'     => 'left',
                'index'     => 'active_class',
                'sortable'  => false,
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'    => $this->__('Status'),
                'width'     => '120',
                'align'     => 'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(0 => $this->__('Not Ok'), 1 => $this->__('Ok')),
                'frame_callback' => array($this, 'decorateStatus')
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @param string                                   $value Check result
     * @param Mage_Catalog_Model_Product|Varien_Object $row   Current row
     *
     * @return string Cell content
     */
    public function decorateStatus($value, $row)
    {
        $class = '';
        if ($row->getStatus()) {
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
     * Return all rewrites
     * 
     * @return array All rwrites
     */
    private function _loadModules()
    {        
        $fileName = 'config.xml';
        $modules = Mage::getConfig()->getNode('modules')->children();

        $return = array();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                $configFile = Mage::getConfig()->getModuleDir('etc', $modName).DS.$fileName;
                                
                $xml = file_get_contents($configFile);
                $xml = simplexml_load_string($xml);
                                
                if ($xml instanceof SimpleXMLElement) {
                    $return[$modName] = $xml->xpath('//rewrite');
                }
            }
        }
        return $return;
    }
}