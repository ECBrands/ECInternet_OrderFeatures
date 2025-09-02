<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface;
use ECInternet\OrderFeatures\Model\ErptermsFactory;
use Exception;

/**
 * Abstract Adminhtml Erpterms controller
 */
abstract class Erpterms extends Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface
     */
    protected $erptermsRepository;

    /**
     * @var \ECInternet\OrderFeatures\Model\ErptermsFactory
     */
    protected $erptermsFactory;

    /**
     * Erpterms constructor.
     *
     * @param \Magento\Backend\App\Action\Context                       $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory         $resultForwardFactory
     * @param \Magento\Framework\Registry                               $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory                $resultPageFactory
     * @param \ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface $erptermsRepository
     * @param \ECInternet\OrderFeatures\Model\ErptermsFactory           $erptermsFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ErptermsRepositoryInterface $erptermsRepository,
        ErptermsFactory $erptermsFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->coreRegistry         = $coreRegistry;
        $this->resultPageFactory    = $resultPageFactory;
        $this->erptermsRepository   = $erptermsRepository;
        $this->erptermsFactory      = $erptermsFactory;

        parent::__construct($context);
    }

    /**
     * ERP Terms access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Retrieve Erpterm by Id
     *
     * @param int $id
     *
     * @return \ECInternet\OrderFeatures\Api\Data\ErptermsInterface|null
     */
    protected function getErpterm(int $id)
    {
        try {
            return $this->erptermsRepository->getById($id);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }
}
