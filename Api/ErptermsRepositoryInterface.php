<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Api;

use ECInternet\OrderFeatures\Api\Data\ErptermsInterface;
use Exception;

interface ErptermsRepositoryInterface
{
    /**
     * Save Erpterms
     *
     * @param \ECInternet\OrderFeatures\Api\Data\ErptermsInterface $erpterms
     *
     * @return \ECInternet\OrderFeatures\Api\Data\ErptermsInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(ErptermsInterface $erpterms);

    /**
     * Get Erpterms by Id
     *
     * @param int $id
     *
     * @return \ECInternet\OrderFeatures\Api\Data\ErptermsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $id);

    /**
     * Delete Erpterms
     *
     * @param \ECInternet\OrderFeatures\Api\Data\ErptermsInterface $erpterms
     *
     * @return bool
     * @throws Exception
     */
    public function delete(ErptermsInterface $erpterms);
}
