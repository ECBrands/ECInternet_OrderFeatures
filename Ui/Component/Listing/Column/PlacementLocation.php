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
 * PlacementLocation Column
 */
class PlacementLocation extends Column
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $_orderRepository;

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

        $this->_orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                /** @var \Magento\Sales\Api\Data\OrderInterface $order */
                $order = $this->_orderRepository->get($item['entity_id']);

                $placedInAdmin = $order->getData(Data::ATTRIBUTE_PLACED_IN_ADMIN);
                if ($placedInAdmin) {
                    $location = 'Placed in Admin';
                } else {
                    $location = 'Placed on Frontend';
                }

                // Assign to item
                $item[$this->getData('name')] = $location;
            }
        }

        return $dataSource;
    }
}
