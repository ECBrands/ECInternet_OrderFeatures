<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Api\Data;

interface ErptermsInterface
{
    public const COLUMN_ID             = 'entity_id';

    public const COLUMN_STORE_ID       = 'store_id';

    public const COLUMN_IS_ACTIVE      = 'is_active';

    public const COLUMN_ERP_TERMS      = 'erp_terms';

    public const COLUMN_ERP_TERMSDESC  = 'erp_termsdesc';

    public const COLUMN_PO_REQUIREMENT = 'po_requirement';

    public const COLUMN_LIMIT_TERMS    = 'limit_terms';

    /**
     * Get ID
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get StoreId
     */
    public function getStoreId();

    /**
     * Set StoreId
     *
     * @param int $storeId
     *
     * @return void
     */
    public function setStoreId(int $storeId);

    /**
     * Get IsActive
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Set IsActive
     *
     * @param int $isActive
     *
     * @return void
     */
    public function setIsActive(int $isActive);

    /**
     * Get Term
     *
     * @return string
     */
    public function getTerm();

    /**
     * Set Term
     *
     * @param string $term
     *
     * @return void
     */
    public function setTerm(string $term);

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription(string $description);

    /**
     * Get PO Requirement
     *
     * @return int
     */
    public function getPoRequirement();

    /**
     * Set PO Requirement
     *
     * @param int $poRequirement
     *
     * @return void
     */
    public function setPoRequirement(int $poRequirement);

    /**
     * Get LimitTerms
     *
     * @return int
     */
    public function getLimitTerms();

    /**
     * Set LimitTerms
     *
     * @param int $limitTerms
     *
     * @return void
     */
    public function setLimitTerms(int $limitTerms);
}
