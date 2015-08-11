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
class MDN_Antidot_Helper_CatalogSearch_Data extends Mage_CatalogSearch_Helper_Data {
    
    /**
     * {@inherit}
     */
    public function getSuggestUrl()
    {
        $url = Mage::getStoreConfig('antidot/suggest/enable') === 'Antidot/engine_antidot' ? 'Antidot/Front_Search/Suggest' : 'catalogsearch/ajax/suggest';
        return $this->_getUrl($url, array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }
}
