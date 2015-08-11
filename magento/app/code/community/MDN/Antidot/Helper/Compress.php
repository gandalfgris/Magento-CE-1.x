<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Antidot_Helper_Compress extends Mage_Core_Helper_Abstract {
    
    /**
     * Compress files
     * 
     * @param array|string $files
     * @param string $zipFile
     * @return boolean
     */
    public function zip($files, $zipFile) 
    {
        Mage::log('Start zip of '.count($files).' files to '.$zipFile, null, 'antidot.log');
        
        $files = (array)$files;
        try {
            if(class_exists('ZipArchive')) {
                Mage::log('ZipArchive exists', null, 'antidot.log');
                $zip = new ZipArchive();
                if(!$zip->open($zipFile, ZipArchive::CREATE)) {
                    throw new Exception("cannot open ".$zipFile." for writing");
                }

                foreach($files as $file) {
                    Mage::log('Add '.$file.' to archive', null, 'antidot.log');
                    $zip->addFile($file, basename($file));
                }
                $zip->close($zip);
            }
            else
            {
                throw new Exception('Zip archive is not installed on your server');
                Mage::log('ZipArchive DOES NOT exist', null, 'antidot.log');
            }
        } catch (Exception $e) {
            Mage::log('Zip exception : '.$e->getMessage(), null, 'antidot.log');
            $files = array_map('basename', $files);
            exec('cd /tmp && zip '.$zipFile.' '.implode(' ', $files));
        }
        
        if(!file_exists($zipFile)) {
            Mage::log('zip file '.$zipFile.' doest no exist', null, 'antidot.log');
            throw new Exception('Could not zip the file at '.$zipFile);
        }
    }
}