<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\ResourceModel\Erpterms;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Erpterms Collection Resource Model
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected $_eventPrefix = 'ecinternet_orderfeatures_erpterms_collection';

    protected $_eventObject = 'erpterms_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        $this->_init(
            \ECInternet\OrderFeatures\Model\Erpterms::class,
            \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms::class
        );
    }
}
