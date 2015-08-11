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
class MDN_Antidot_Model_Export_Category extends MDN_Antidot_Model_Export_Abstract 
{
    const TYPE = 'CATEGORY';
    const FILENAME_XML = 'categories-mdn-%s.xml';
    const FILENAME_ZIP = '%s_full_mdn_categories.zip';
    const XSD = 'http://ref.antidot.net/store/latest/categories.xsd';
    
    /**
     * Get xml
     * 
     * @param type $context
     */
    public function writeXml($context, $filename)
    {
        $this->initXml();
        $this->initFields('category');
        
        $this->xml->push('categories', array('xmlns' => "http://ref.antidot.net/store/afs#"));
        
        $this->xml->push('header');
        $this->xml->element('owner', $context['owner']);
        $this->xml->element('feed', 'category');
        $this->xml->element('generated_at', date('c', Mage::getModel('core/date')->timestamp(time())));
        $this->xml->pop();

        $nbItems = 0;
        foreach($context['stores'] as $store) {
            foreach($this->getCategories($store) as $cat) {
                $this->xml->push('category', array('id' => $cat->getId(), 'xml:lang' => $context['lang']));

                $this->xml->element('name', $this->xml->encloseCData($this->getField($cat, 'name')));
                $this->xml->element('url', $this->getUri($cat->getUrl()));

                if ($cat->getImageUrl()) {
                    $this->xml->element('image', $cat->getImageUrl());
                }

                if ($keywords = $this->getField($cat, 'keywords')) {
                    $this->xml->element('keywords', $this->xml->encloseCData($keywords));
                }

                if ($description = $this->getField($cat, 'description')) {
                    $this->xml->element('description', $this->xml->encloseCData($description));
                }

                if ($cat->getProductCount() > 0) {
                    $this->xml->element('productsCount', $cat->getProductCount());
                }

                if ($cat->getParentId() && ($cat->getParentId() != $store->getRootCategoryId())) {
                    $this->xml->emptyelement('broader', array('idref' => $cat->getParentId()));
                }

                $storeIds = array_intersect($context['store_id'], $cat->getStoreIds());
                $this->xml->push('websites');
                foreach($storeIds as $storeId) {
                    $website = $this->getWebSiteByStore($context['stores'][$storeId]);
                    $this->xml->element('website', '', array('id' => $website->getId(), 'name' => $website->getName()));
                }
                $this->xml->pop();

                $this->xml->pop();

                $nbItems++;
            }
        }
        $this->xml->pop();
        
        file_put_contents($filename, $this->xml->flush());
        
        return $nbItems;
    }
    
    /**
     * Return categories
     * 
     * @param Store $store
     * @return array
     */
    protected function getCategories($store)
    {
        return Mage::getModel('catalog/category')
            ->getCollection()
            ->setStoreId($store->getId())
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addFieldToFilter('path', array('like' => Mage::getModel('catalog/category')->load($store->getRootCategoryId())->getPath().'/%'))
        ;
    }
}