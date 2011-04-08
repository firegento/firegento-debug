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
 * CheckEvents Grid
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2011 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   $Id:$
 */
class FireGento_Core_Block_Diagnostic_CheckEvents_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('check_events_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * Prepare grid collection
     * 
     * @return FireGento_Core_Block_Diagnostic_CheckEvents_Grid Grid object
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
                $pathNodes = $n->children();

                foreach ($pathNodes as $pathNode) {
                    $eventName = (string) $pathNode->getName();
                    $instance = $pathNode->xpath('observers/node()/class');
                    $instance = (string)current($instance);
                    $instance = Mage::getConfig()->getModelClassName($instance);
                    
                    $collection->addItem(
                        new Varien_Object(
                            array(
                                'event' => $eventName,
                                'location' => $instance
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
     * @return FireGento_Core_Block_Diagnostic_CheckEvents_Grid Grid object
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $this->addColumn(
            'event',
            array(
                'header'    => $this->__('Event'),
                'align'     => 'left',
                'index'     => 'event',
                'width'     => '50%',
                'sortable'  => false,
            )
        );
        $this->addColumn(
            'location',
            array(
                'header'    => $this->__('Location'),
                'align'     => 'left',
                'index'     => 'location',
                'width'     => '50%',
                'sortable'  => false,
            )
        );
        return parent::_prepareColumns();
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
     * Return all events
     * 
     * @return array All events
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
                    $return[$modName] = $xml->xpath('//events');
                }
            }
        }
        return $return;
    }
}