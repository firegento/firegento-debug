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
 * CheckEvents Grid
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Block_Diagnostic_CheckEvents_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('check_events_grid');
        $this->setDefaultSort('event');
        $this->setDefaultDir('ASC');
        //$this->_filterVisibility = false;
        $this->_pagerVisibility = false;
    }

    /**
     * Prepare grid collection
     *
     * @return FireGento_Debug_Block_Diagnostic_CheckEvents_Grid Grid object
     */
    protected function _prepareCollection()
    {
        $collection = Mage::helper('firegento/firegento')->getEventsCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return FireGento_Debug_Block_Diagnostic_CheckEvents_Grid Grid object
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn(
            'module',
            array(
                'header'   => $this->__('Module Name'),
                'align'    => 'left',
                'index'    => 'module',
                'sortable' => false,
                'filter'   => false
            )
        );
        $this->addColumn(
            'code_pool',
            array(
                'header'                    => $this->__('Code Pool'),
                'align'                     => 'left',
                'index'                     => 'code_pool',
                'width'                     => '80px',
                'sortable'                  => true,
                'type'                      => 'options',
                'options'                   => Mage::helper('firegento')->getHashCodePools(),
                'filter_condition_callback' => array($this, '_codePoolFilter'),
            )
        );

        $this->addColumn(
            'event',
            array(
                'header'                    => $this->__('Event'),
                'align'                     => 'left',
                'index'                     => 'event',
                'width'                     => '50%',
                'sortable'                  => true,
                'filter_condition_callback' => array($this, '_eventFilter'),
            )
        );
        $this->addColumn(
            'location',
            array(
                'header'   => $this->__('Location'),
                'align'    => 'left',
                'index'    => 'location',
                'width'    => '30%',
                'sortable' => false,
                'renderer' => 'firegento/diagnostic_renderer_paragraph',
                'filter'   => false
            )
        );


        return parent::_prepareColumns();
    }

    /**
     * Filter code pool collection
     *
     * @param Varien_Data_Collection                  $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function _codePoolFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection = $this->getCollection();

        foreach ($collection as $itemKey => $item) {
            if ($value != $item->getCodePool()) {
                $collection->removeItemByKey($itemKey);
            }
        }

        return $this;
    }

    /**
     * Filter event collection
     *
     * @param Varien_Data_Collection                  $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function _eventFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection = $this->getCollection();

        foreach ($collection as $itemKey => $item) {
            if (strpos($item->getEvent(), $value) === false) {
                $collection->removeItemByKey($itemKey);
            }
        }

        return $this;
    }

    /**
     * Get row edit url
     *
     * @param  Mage_Catalog_Model_Product|Varien_Object $row Current row
     * @return string|boolean                           Row url | false = no url
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
