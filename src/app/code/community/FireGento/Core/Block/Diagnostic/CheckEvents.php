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
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 */
/**
 * CheckEvents Grid Container
 *
 * @category  FireGento
 * @package   FireGento_Core
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2012 FireGento Team (http://www.firegento.de). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.0.0
 */
class FireGento_Core_Block_Diagnostic_CheckEvents
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_controller = 'diagnostic_checkEvents';
        $this->_blockGroup = 'firegento';
        $this->_headerText = $this->__('Check Events');
        parent::__construct();
        $this->_removeButton('add');
    }
}
