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
 * CheckRewrites Grid
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Block_Diagnostic_CheckRewrites_Grid
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
        $this->setId('check_rewrites_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * Prepare grid collection
     *
     * @return FireGento_Debug_Block_Diagnostic_CheckRewrites_Grid Grid object
     */
    protected function _prepareCollection()
    {
        $collection = Mage::helper('firegento/firegento')->getRewriteCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return FireGento_Debug_Block_Diagnostic_CheckRewrites_Grid Grid object
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $this->addColumn(
            'path',
            array(
                'header'   => $this->__('Path'),
                'align'    => 'left',
                'index'    => 'path',
                'sortable' => false,
            )
        );
        $this->addColumn(
            'rewrite_class',
            array(
                'header'   => $this->__('Rewrite Class'),
                'width'    => '200',
                'align'    => 'left',
                'index'    => 'rewrite_class',
                'sortable' => false,
            )
        );
        $this->addColumn(
            'active_class',
            array(
                'header'   => $this->__('Active Class'),
                'width'    => '200',
                'align'    => 'left',
                'index'    => 'active_class',
                'sortable' => false,
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'         => $this->__('Status'),
                'width'          => '120',
                'align'          => 'left',
                'index'          => 'status',
                'type'           => 'options',
                'options'        => array(0 => $this->__('Not Ok'), 1 => $this->__('Ok')),
                'frame_callback' => array($this, 'decorateStatus')
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @param  string                                   $value Check result
     * @param  Mage_Catalog_Model_Product|Varien_Object $row   Current row
     * @return string                                   Cell content
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
     * @param  Mage_Catalog_Model_Product|Varien_Object $row Current row
     * @return string|boolean                           Row url | false = no url
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
