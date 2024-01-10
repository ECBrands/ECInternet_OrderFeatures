<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;
use Exception;

/**
 * Adminhtml Erpterms Save controller
 */
class Save extends Erpterms implements HttpPostActionInterface
{
    /**
     * Execute 'Save' action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($request = $this->getRequest()) {
            if ($request instanceof HttpRequest) {
                $data = $request->getPostValue('erpterms');
                if (!$data) {
                    return $resultRedirect->setPath('*/*/edit');
                }

                /** @var \ECInternet\OrderFeatures\Model\Erpterms $model */
                $model = $this->_erptermsFactory->create();

                // Update model from form data
                $model->setData($data);

                $this->_eventManager->dispatch('orderfeatures_erpterms_prepare_save', [
                    'erpterm' => $model,
                    'request' => $this->getRequest()
                ]);

                try {
                    $this->_erptermsRepository->save($model);

                    // Show success message
                    $this->messageManager->addSuccessMessage('You successfully saved the term!');

                    // Check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('*/*/edit', [
                            'id'       => $model->getId(),
                            '_current' => true
                        ]);
                    }
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
