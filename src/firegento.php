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
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://firegento.com). All rights served.
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
require_once 'app/Mage.php';
umask(0);
Mage::app('admin');

$fileName = 'config.xml';
$modules  = Mage::getConfig()->getNode('modules')->children();

$return = array();
foreach ($modules as $modName => $module) {
    if ($module->is('active')) {
        $configFile = Mage::getConfig()->getModuleDir('etc', $modName) . DS . $fileName;
        if (file_exists($configFile)) {
            $xml = file_get_contents($configFile);
            $xml = simplexml_load_string($xml);

            if ($xml instanceof SimpleXMLElement) {
                $return[$modName] = $xml->xpath('//rewrite');
            }
        }
    }
}

$collection = new Varien_Data_Collection();
foreach ($return as $rewriteNodes) {
    foreach ($rewriteNodes as $n) {
        $nParent   = $n->xpath('..');
        $module    = (string) $nParent[0]->getName();
        $nParent2  = $nParent[0]->xpath('..');
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

echo '<html><head><title>FIREGENTO - Quick Rewrite Check</title><style type="text/css">body,td,th { text-align:left;font-family:Arial, Helvetica, sans-serif;font-size:12px;padding:2px 5px;line-height:16px; }h1{margin:25px 0}</style></head><body><h1>FIREGENTO - Quick Rewrite Check</h1><table><thead><tr><th>#</th><th>Path</th><th>Rewrite Class</th><th>Active Class</th><th>Status</th></tr></thead><tbody>';

$i = 1;
foreach ($collection as $item) {
    $status = $item->getData('status');
    if ($status) {
        $status = '<span style="color:green">OK</span>';
    } else {
        $status = '<span style="color:red">NOT OK</span>';
    }

    echo '<tr>
    	<td>'.$i.'</td>
    	<td>'.$item->getData('path').'</td>
    	<td>'.$item->getData('rewrite_class').'</td>
    	<td>'.$item->getData('active_class').'</td>
    	<td>'.$status.'</td>
    	</tr>';
    	$i++;
}

echo '</tbody></table></body></html>';
