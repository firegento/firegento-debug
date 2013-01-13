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
class FireGento_Debug_Block_Diagnostic_CheckModules_Grid
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
        $this->setId('check_modules_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
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
        $collection = Mage::helper('firegento/firegento')->getModulesCollection();
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
            'version',
            array(
                'header' => $this->__('Version'),
                'align'  => 'left',
                'index'  => 'version',
                'width'  => '100px',
            )
        );
        $this->addColumn(
            'dependencies',
            array(
                'header'   => $this->__('Module Dependencies'),
                'align'    => 'left',
                'index'    => 'dependencies',
                'width'    => '350px',
                'sortable' => false,
                'renderer' => 'firegento/diagnostic_renderer_paragraph'
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('sales')->__('Action'),
                'width'   => '50px',
                'type'    => 'action',
                'getter'  => 'getName',
                'actions' => array(
                    array(
                        'caption' => $this->__('Activate/Deactivate'),
                        'url'     => array('base' => '*/*/activation'),
                        'field'   => 'name',
                        'confirm' => $this->__('ATTENTION! Are you sure you want to deactivate this module? This can cause major problems, if you are not careful enough! ATTENTION!')
                    )
                ),
                'filter'         => false,
                'sortable'       => false,
                'index'          => 'name',
                'is_system'      => true,
                'frame_callback' => array($this, 'checkActivationLink')
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Check the activation link and replace the string "Deactivate" with
     * "Activate" if the module is deactivated.
     *
     * @param  string        $value Link
     * @param  Varien_Object $row   Current row
     * @return string        Cell content
     */
    public function checkActivationLink($value, $row)
    {
        if ($row->getCodePool() == 'core') {
            return '';
        }

        return $value;
    }

    /**
     * Decorate the active column values
     *
     * @param  string        $value Check result
     * @param  Varien_Object $row   Current row
     * @return string        Cell content
     */
    public function decorateTrueFalse($value, $row)
    {
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
     * @param  string        $value Check result
     * @param  Varien_Object $row   Current row
     * @return string        Cell content
     */
    public function decoratePathExists($value, $row)
    {
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
     * @param  string        $value Check result
     * @param  Varien_Object $row   Current row
     * @return string        Cell content
     */
    public function decorateConfigExists($value, $row)
    {
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
     * @param  Varien_Object  $row Current row
     * @return string|boolean Row url | false = no url
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
