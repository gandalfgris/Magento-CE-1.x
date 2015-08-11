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
class MDN_Antidot_Model_Export_Abstract extends Mage_Core_Model_Abstract 
{
    /**
     * Instance of XmlWriter
     *
     * @var XmlWriter
     */
    protected $xml;
    
    /**
     * List website loaded
     *
     * @var array
     */
    protected $website = array();
    
    protected $storeLang = array();
    
    /**
     * The fields to load
     *
     * @var array
     */
    protected $fields = array();
    
    protected $fieldsSerialized = array(
        'properties',
        'misc',
        'identifier',
        'description'
    );
    
    /**
     * Init the xml writer
     */
    protected function initXml()
    {
        if($this->xml === null) {
            $this->xml = Mage::helper('Antidot/XmlWriter');
            $this->xml->init();
        }
    }
    
    /**
     * Extract the uri from an url
     * 
     * @param string $url
     * @return string
     */
    protected function getUri($url)
    {
        $urls = parse_url($url);
        
        
        return $urls['path'];
    }
    
    /**
     * Init the fields
     * 
     * @param string $section The section to load
     */
    protected function initFields($section)
    {
        $this->fields = array();
        $values = Mage::getStoreConfig('antidot/fields_'.$section);
        foreach($values as $key => $value) {
            if(in_array($key, $this->fieldsSerialized) && $value = @unserialize($value)) {
                $values = array_values($value);
                foreach($values as $value) {
                    if($key !== 'properties') {
                        $this->fields[$key][] = $value['value'];
                    } else {
                        $this->fields[$key][] = $value;
                    }
                }
                continue;
            }
            $this->fields[$key] = $value;
        }
    }
    
    /**
     * Rertrieve a data from an entity
     * 
     * @param Entity $entity
     * @param string $field
     * @return string
     */
    protected function getField($entity, $field) 
    {
        $field = isset($this->fields[$field]) && !is_array($this->fields[$field]) ? $this->fields[$field] : $field;
        if(empty($field)) {
            return false;
        }
        
        $method = 'get'.ucfirst(strtolower($field));
        
        return $entity->$method();
    }
    
    /**
     * Get website by store
     * 
     * @param Store $store
     * @return WebSite
     */
    protected function getWebSiteByStore($store)
    {
        if(!isset($this->website[$store->getId()])) {
            $this->website[$store->getId()] = Mage::getModel('core/website')->load($store->getWebSiteId());
        }
        
        return $this->website[$store->getId()];
    }
    
    protected function getStoreLang($storeId)
    {
        if(!isset($this->storeLang[$storeId])) {
            list($this->storeLang[$storeId]) = explode('_', Mage::getStoreConfig('general/locale/code', $storeId));
        }
        
        return $this->storeLang[$storeId];
    }
}