<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Ui\Component\Listing\Column;

use ECInternet\OrderFeatures\Helper\Data;
use Exception;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * PlacementLocation Column
 */
class PlacementLocation extends Column
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * PlacementLocation constructor.
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
     * Add 'placed_in_admin' data
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
                        $item[$this->getData('name')] = $this->getWasPlacedInAdmin($order);
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
     * Get 'erp_terms' data
     *
     * @param OrderInterface $order
     *
     * @return string
     */
    private function getWasPlacedInAdmin(OrderInterface $order)
    {
        return ($order->getData(Data::ATTRIBUTE_PLACED_IN_ADMIN))
            ? 'Placed in Admin'
            : 'Placed on Frontend';
    }
}
