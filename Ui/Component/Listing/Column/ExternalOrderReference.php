<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use ECInternet\OrderFeatures\Helper\Data;

/**
 * ExternalOrderReference Column
 */
class ExternalOrderReference extends Column
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $_orderRepository;

    /**
     * ExternalOrderReference constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface                  $orderRepository
     * @param array                                                        $components
     * @param array                                                        $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->_orderRepository = $orderRepository;
    }

    /**
     * Add 'external_order_reference' data
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->getExternalOrderReference((int)$item['entity_id']);
            }
        }

        return $dataSource;
    }

    /**
     * Get 'external_order_reference' data
     *
     * @param int $orderId
     *
     * @return string
     */
    private function getExternalOrderReference(int $orderId)
    {
        $order = $this->_orderRepository->get($orderId);

        return (string)$order->getData(Data::ATTRIBUTE_EXTERNAL_ORDER_REFERENCE);
    }
}
