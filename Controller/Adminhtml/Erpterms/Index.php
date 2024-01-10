<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;

use Magento\Framework\App\Action\HttpGetActionInterface;
use ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;

/**
 * Adminhtml Erpterms Index controller
 */
class Index extends Erpterms implements HttpGetActionInterface
{
    /**
     * Execute 'Index' action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();

        // Active menu
        $resultPage->setActiveMenu('ECInternet_OrderFeatures::erpterms');

        // Page title
        $resultPage->getConfig()->getTitle()->prepend(__('ERP Terms Maintenance'));

        // Breadcrumbs
        $resultPage->addBreadcrumb(__('ECInternet'), __('ECInternet'));
        $resultPage->addBreadcrumb(__('OrderFeatures'), __('Manage ERP Terms'));

        return $resultPage;
    }
}
