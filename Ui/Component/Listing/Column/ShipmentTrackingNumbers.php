<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Ui\Component\Listing\Column;

use Exception;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * ShipmentTrackingNumbers Column
 */
class ShipmentTrackingNumbers extends Column
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * ShipmentTrackingNumbers constructor.
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

        $this->orderRepository = $orderRepository;
    }

    /**
     * Add tracking number data
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (is_numeric($item['entity_id'])) {
                    if ($order = $this->getOrderById((int)$item['entity_id'])) {
                        $item[$this->getData('name')] = $this->getShipmentTrackingNumbers($order);
                    }
                }
            }
        }

        return $dataSource;
    }

    private function getOrderById(int $orderId)
    {
        try {
            return $this->orderRepository->get($orderId);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }

    /**
     * Get comma-separated string of shipment tracking numbers
     *
     * @param OrderInterface $order
     *
     * @return string
     */
    private function getShipmentTrackingNumbers(OrderInterface $order)
    {
        $trackingNumbers = [];

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $tracks */
        $tracks = $order->getTracksCollection();

        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
        foreach ($tracks->getItems() as $track) {
            $trackingNumbers[] = $track->getTrackNumber();
        }

        return implode(', ', $trackingNumbers);
    }
}
