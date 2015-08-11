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
class MDN_Antidot_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @var array Searchable attributes
     */
    protected $_searchableAttributes;

    /**
     * @var array Facets configuration
     */
    protected $facetConfiguration;

    /**
     * Returns attribute field name (localized if needed).
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string $localeCode
     * @return string
     */
    public function getAttributeFieldName($attribute, $localeCode = null)
    {
        if (is_string($attribute)) {
            $this->getSearchableAttributes();
            if (!isset($this->_searchableAttributes[$attribute])) {
                return $attribute;
            }
            $attribute = $this->_searchableAttributes[$attribute];
        }
        $attributeCode = $attribute->getAttributeCode();

        return $attributeCode;
    }

    /**
     * Returns search engine config data.
     *
     * @param string $prefix
     * @param mixed $store
     * @return array
     */
    public function getEngineConfigData($prefix = '', $store = null)
    {
        $config = Mage::getStoreConfig('catalog/search', $store);
        $data = array();
        if ($prefix) {
            foreach ($config as $key => $value) {
                $matches = array();
                if (preg_match("#^{$prefix}(.*)#", $key, $matches)) {
                    $data[$matches[1]] = $value;
                }
            }
        } else {
            $data = $config;
        }

        return $data;
    }

    /**
     * Returns EAV config singleton.
     *
     * @return Mage_Eav_Model_Config
     */
    public function getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * Returns seach config data.
     *
     * @param string $field
     * @param mixed $store
     * @return array
     */
    public function getSearchConfigData($field, $store = null)
    {
        $path = 'catalog/search/' . $field;

        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Check if the facet accepts multiple options
     *
     * @param string $facetId
     * @return boolean
     */
    public function hasFacetMultiple($facetId)
    {
        $facets = $this->getFacetsFilter();

        return array_key_exists($facetId, $facets) && $facets[$facetId]['multiple'] === '1';
    }

    /**
     * Retrieve the facets configuration
     *
     * @return array
     */
    public function getFacetsFilter()
    {
        if($this->facetConfiguration === null) {
            $this->facetConfiguration = array();
            if($serializeFacets = Mage::getStoreConfig('antidot/engine/facets')) {
                $facets = unserialize($serializeFacets);
                foreach($facets as $facet) {
                    list($facetId) = explode('|', $facet['facet']);
                    $this->facetConfiguration[$facetId] = $facet;
                }
            }
        }

        return $this->facetConfiguration;
    }

    /**
     * Returns searched parameter as array.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param mixed $value
     * @return array
     */
    public function getSearchParam($attribute, $value)
    {
        if (empty($value) ||
            (isset($value['from']) && empty($value['from']) &&
                isset($value['to']) && empty($value['to']))) {
            return false;
        }

        $field = $this->getAttributeFieldName($attribute);
        if ($attribute->usesSource()) {
            $attribute->setStoreId(Mage::app()->getStore()->getId());
        }

        return array($field => $value);
    }

    /**
     * Checks if configured engine is active.
     *
     * @return bool
     */
    public function isActiveEngine()
    {
        $engine = $this->getSearchConfigData('engine');
        if ($engine && Mage::getConfig()->getResourceModelClassName($engine)) {
            $model = Mage::getResourceSingleton($engine);
            return $model
                && $model instanceof MDN_Antidot_Model_Resource_Engine_Abstract
                && $model->test();
        }

        return false;
    }

    /**
     * Send an email to admin
     *
     * @param string $subject
     * @param string $message
     */
    public function sendMail($subject, $message)
    {
        if(!$email = Mage::getStoreConfig('antidot/general/email')) {
            return;
        }

        $mail = Mage::getModel('core/email');
        $mail->setToEmail($email);
        $mail->setBody($message);
        $mail->setSubject(Mage::getStoreConfig('system/website/name').': '. $subject);
        $mail->setFromEmail('no-reply@antidot.net');
        $mail->setFromName("AFSStore for Magento");
        $mail->setType('text');

        try {
            $mail->send();
            Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('Unable to send.');
        }
    }

    /**
     * Translate facet name
     *
     * @param $facetcode
     * @param $defaultValue
     * @return mixed
     */
    public function translateFacetName($facetcode, $defaultValue)
    {
        $model = Mage::getModel('Antidot/Search_Search');

        $label = $defaultValue;
        if (isset($model::$lastSearchTranslations[$facetcode]))
            $label = $model::$lastSearchTranslations[$facetcode];
        return $label;
    }

}