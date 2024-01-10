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
 * Adminhtml Erpterms Edit controller
 */
class Edit extends Erpterms implements HttpGetActionInterface
{
    /**
     * Execute 'Edit' action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $this->_coreRegistry->register('current_erpterm_id', $id);

            /** @var \ECInternet\OrderFeatures\Model\Erpterms $model */
            $model = $this->getErpterm($id);
            if (!$model || !$model->getId()) {
                $this->messageManager->addErrorMessage(__('This term no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();

        $resultPage->setActiveMenu('ECInternet_OrderFeatures::erpterms');
        $resultPage->getConfig()->getTitle()->prepend(__('ERP Terms Maintenance'));

        // Add breadcrumb
        $resultPage->addBreadcrumb(__('ECInternet'), __('ECInternet'));
        $resultPage->addBreadcrumb(__('OrderFeatures'), __('Manage ERP Terms'));

        return $resultPage;
    }
}
