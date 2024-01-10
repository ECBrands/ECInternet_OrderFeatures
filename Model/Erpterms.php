<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use ECInternet\OrderFeatures\Api\Data\ErptermsInterface;

/**
 * Erpterms model
 */
class Erpterms extends AbstractModel implements IdentityInterface, ErptermsInterface
{
    const CACHE_TAG = 'ecinternet_orderfeatures_erpterms';

    protected $_cacheTag    = 'ecinternet_orderfeatures_erpterms';

    protected $_eventPrefix = 'ecinternet_orderfeatures_erpterms';

    protected $_eventObject = 'erpterms';

    protected function _construct()
    {
        $this->_init(ResourceModel\Erpterms::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::COLUMN_ID);
    }

    public function getStoreId()
    {
        return $this->getData(self::COLUMN_STORE_ID);
    }

    public function setStoreId($storeId)
    {
        $this->setData(self::COLUMN_STORE_ID, $storeId);
    }

    public function getIsActive()
    {
        return $this->getData(self::COLUMN_IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        $this->setData(self::COLUMN_IS_ACTIVE, $isActive);
    }

    public function getTerm()
    {
        return $this->getData(self::COLUMN_ERP_TERMS);
    }

    public function setTerm($term)
    {
        $this->setData(self::COLUMN_ERP_TERMS, $term);
    }

    public function getDescription()
    {
        return $this->getData(self::COLUMN_ERP_TERMSDESC);
    }

    public function setDescription($description)
    {
        $this->setData(self::COLUMN_ERP_TERMSDESC, $description);
    }

    public function getPoRequirement()
    {
        return $this->getData(self::COLUMN_PO_REQUIREMENT);
    }

    public function setPoRequirement($poRequirement)
    {
        $this->setData(self::COLUMN_PO_REQUIREMENT, $poRequirement);
    }

    public function getLimitTerms()
    {
        return $this->getData(self::COLUMN_LIMIT_TERMS);
    }

    public function setLimitTerms($limitTerms)
    {
        $this->setData(self::COLUMN_LIMIT_TERMS, $limitTerms);
    }
}
