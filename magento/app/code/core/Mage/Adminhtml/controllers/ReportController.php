<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Reports'), Mage::helper('adminhtml')->__('Reports'));
        return $this;
    }


/*
    public function wishlistAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/wishlist')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Wishlist Report'), Mage::helper('adminhtml')->__('Wishlist Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_wishlist'))
            ->renderLayout();
    }

    /**
     * Export wishlist report grid to CSV format
     * /
    public function exportWishlistCsvAction()
    {
        $fileName   = 'wishlist.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_wishlist_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export wishlist report to Excel XML format
     * /
    public function exportWishlistExcelAction()
    {
        $fileName   = 'wishlist.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_wishlist_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
*/
    public function searchAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/search')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Search Terms'), Mage::helper('adminhtml')->__('Search Terms'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_search'))
            ->renderLayout();
    }

    /**
     * Export search report grid to CSV format
     */
    public function exportSearchCsvAction()
    {
        $fileName   = 'search.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_search_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export search report to Excel XML format
     */
    public function exportSearchExcelAction()
    {
        $fileName   = 'search.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_search_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
/*
    public function ordersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/orders')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Recent Orders'), Mage::helper('adminhtml')->__('Recent Orders'))
            ->renderLayout();
    }

    public function totalsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/totals')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Order Totals'), Mage::helper('adminhtml')->__('Order Totals'))
            ->renderLayout();
    }
*/

    protected function _isAllowed()
    {
	    switch ($this->getRequest()->getActionName()) {
            case 'search':
                return Mage::getSingleton('admin/session')->isAllowed('report/search');
                break;
            /*
            case 'customers':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
            */
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report');
                break;
        }
    }
}