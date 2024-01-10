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
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface
     */
    protected $_erptermsRepository;

    /**
     * @var \ECInternet\OrderFeatures\Model\ErptermsFactory
     */
    protected $_erptermsFactory;

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
        parent::__construct($context);

        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry         = $coreRegistry;
        $this->_resultPageFactory    = $resultPageFactory;
        $this->_erptermsRepository   = $erptermsRepository;
        $this->_erptermsFactory      = $erptermsFactory;
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
            return $this->_erptermsRepository->getById($id);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }
}
