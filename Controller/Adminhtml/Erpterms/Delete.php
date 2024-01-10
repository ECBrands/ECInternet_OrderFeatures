<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;

use Magento\Framework\App\Action\HttpPostActionInterface;
use ECInternet\OrderFeatures\Controller\Adminhtml\Erpterms;
use Exception;

/**
 * Adminhtml Erpterms Delete controller
 */
class Delete extends Erpterms implements HttpPostActionInterface
{
    /**
     * Execute 'Delete' action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id = $this->getRequest()->getParam('id')) {
            /** @var \ECInternet\OrderFeatures\Api\Data\ErptermsInterface|null $term */
            $term = $this->getErpterm((int)$id);

            // Check if this term exists or not
            if ($term === null) {
                $this->messageManager->addErrorMessage(__('This term no longer exists.'));
            } else {
                try {
                    $this->_erptermsRepository->delete($term);
                    $this->messageManager->addSuccessMessage(__('The term has been deleted.'));
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());

                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }
            }
        }

        return $resultRedirect->setPath('orderfeatures/erpterms');
    }
}
