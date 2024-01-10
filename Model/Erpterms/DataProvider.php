<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\Erpterms;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory as ErptermsCollectionFactory;
use Exception;

/**
 * DataProvider for Erpterms model
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $_request;

    /**
     * @var \ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface
     */
    private $_erptermsRepository;

    /**
     * DataProvider constructor.
     *
     * @param string                                                                   $name
     * @param string                                                                   $primaryFieldName
     * @param string                                                                   $requestFieldName
     * @param \Magento\Framework\App\RequestInterface                                  $request
     * @param \ECInternet\OrderFeatures\Api\ErptermsRepositoryInterface                $erptermsRepository
     * @param \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory $erptermsCollectionFactory
     * @param array                                                                    $meta
     * @param array                                                                    $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        RequestInterface $request,
        ErptermsRepositoryInterface $erptermsRepository,
        ErptermsCollectionFactory $erptermsCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->_request            = $request;
        $this->_erptermsRepository = $erptermsRepository;
        $this->collection          = $erptermsCollectionFactory->create();
        $this->collection->addFieldToSelect('*');

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = [];

        if ($requestId = $this->_request->getParam($this->requestFieldName)) {
            /** @var \ECInternet\OrderFeatures\Api\Data\ErptermsInterface|null $erpterm */
            if ($erpterm = $this->getErpterms((int)$requestId)) {
                $this->loadedData[$erpterm->getId()]['erpterms'] = $erpterm->getData();
            }
        }

        return $this->loadedData;
    }

    /**
     * Retrieve Erpterm by Id
     *
     * @param int $erptermsId
     *
     * @return \ECInternet\OrderFeatures\Api\Data\ErptermsInterface|null
     */
    private function getErpterms(int $erptermsId)
    {
        try {
            return $this->_erptermsRepository->getById($erptermsId);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }
}
