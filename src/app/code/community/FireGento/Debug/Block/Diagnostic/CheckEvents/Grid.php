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
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('check_events_grid');
        $this->setDefaultSort('event');
        $this->setDefaultDir('ASC');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
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
            'event',
            array(
                'header'   => $this->__('Event'),
                'align'    => 'left',
                'index'    => 'event',
                'width'    => '50%',
                'sortable' => true,
            )
        );
        $this->addColumn(
            'location',
            array(
                'header'   => $this->__('Location'),
                'align'    => 'left',
                'index'    => 'location',
                'width'    => '50%',
                'sortable' => true,
                'renderer' => 'firegento/diagnostic_renderer_paragraph'
            )
        );

        return parent::_prepareColumns();
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
