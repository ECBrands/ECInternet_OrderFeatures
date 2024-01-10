<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use ECInternet\OrderFeatures\Api\Data\ErptermsInterface;
use ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms as ErptermsResource;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory as ErptermsCollectionFactory;

/**
 * Repository for Erpterms model
 */
class ErptermsRepository implements ErptermsRepositoryInterface
{
    /**
     * @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms
     */
    private $_resourceModel;

    /**
     * @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory
     */
    private $_erptermsCollectionFactory;

    /**
     * ErptermsRepository constructor.
     *
     * @param \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms                   $resourceModel
     * @param \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory $erptermsCollection
     */
    public function __construct(
        ErptermsResource $resourceModel,
        ErptermsCollectionFactory $erptermsCollection
    ) {
        $this->_resourceModel             = $resourceModel;
        $this->_erptermsCollectionFactory = $erptermsCollection;
    }

    /**
     * @inheritDoc
     */
    public function save(ErptermsInterface $erpterms)
    {
        $this->_resourceModel->save($erpterms);

        return $erpterms;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id)
    {
        /** @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\Collection $erptermsCollection */
        $erptermsCollection = $this->_erptermsCollectionFactory->create()
            ->addFieldToFilter(Erpterms::COLUMN_ID, ['eq' => $id]);

        $erptermsCollectionCount = $erptermsCollection->getSize();
        if ($erptermsCollectionCount == 1) {
            $erpTerm = $erptermsCollection->getFirstItem();
            if ($erpTerm instanceof Erpterms) {
                return $erpTerm;
            }
        } elseif ($erptermsCollectionCount == 0) {
            throw new NoSuchEntityException(__('Unable to find Erpterms with ID "%1"', $id));
        } else {
            throw new LocalizedException(__('Found multiple Erpterms with ID "%1"', $id));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function delete(ErptermsInterface $erpterms)
    {
        $this->_resourceModel->delete($erpterms);
    }
}
