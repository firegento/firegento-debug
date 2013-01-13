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
 * Data Helper for different helper functionalities
 *
 * @category  FireGento
 * @package   FireGento_Debug
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @version   1.2.0
 */
class FireGento_Debug_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Sorts a multi-dimensional array with the given values
     *
     * Seen and modified from: http://www.firsttube.com/read/sorting-a-multi-dimensional-array-with-php/
     *
     * @param  array  $arr Array to sort
     * @param  string $key Field to sort
     * @param  string $dir Direction to sort
     * @return array  Sorted array
     */
    public function sortMultiDimArr($arr, $key, $dir='ASC')
    {
        foreach ($arr as $k => $v) {
            $b[$k] = strtolower($v[$key]);
        }

        if ($dir == 'ASC') {
            asort($b);
        } else {
            arsort($b);
        }
        foreach ($b as $key => $val) {
            $c[] = $arr[$key];
        }

        return $c;
    }
}
