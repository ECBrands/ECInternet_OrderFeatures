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
 * Adminhtml Erpterms NewAction controller
 */
class NewAction extends Erpterms implements HttpGetActionInterface
{
    /**
     * Execute 'New' action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        return $this->_resultForwardFactory->create()->forward('edit');
    }
}
