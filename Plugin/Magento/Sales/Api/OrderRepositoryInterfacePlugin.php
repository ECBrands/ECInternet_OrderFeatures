<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Plugin for Magento\Sales\Api\OrderRepositoryInterface
 */
class OrderRepositoryInterfacePlugin
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * OrderRepositoryInterfacePlugin constructor
     *
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \ECInternet\OrderFeatures\Model\Config        $config
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        Config $config
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->config                = $config;
    }

    /**
     * Add extension attributes to Order data object to make them accessible in API data of Order record
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $resultOrder
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        /** @noinspection PhpUnusedParameterInspection */ OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        return $this->setOrderExtensionAttributes($resultOrder);
    }

    /**
     * Add extension attributes to order data object to make them accessible in API data of all order list
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface        $subject
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ) {
        /** @var \Magento\Sales\Api\Data\OrderInterface[] $orders */
        $orders = $searchResult->getItems();
        foreach ($orders as $order) {
            $this->afterGet($subject, $order);
        }

        return $searchResult;
    }

    /**
     * Update 'external_order_number' attribute before Save.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $resultOrder
     *
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function beforeSave(
        /** @noinspection PhpUnusedParameterInspection */ OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        /** @var \Magento\Sales\Api\Data\OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $resultOrder->getExtensionAttributes() ?: $this->orderExtensionFactory->create();
        if ($extensionAttributes instanceof OrderExtension) {
            $resultOrder->setData(Config::ATTRIBUTE_EXTERNAL_ORDER_REFERENCE, $extensionAttributes->getExternalOrderReference());
        }

        return [$resultOrder];
    }

    /**
     * Set ExtensionAttributes on Order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function setOrderExtensionAttributes(
        OrderInterface $order
    ) {
        /** @var \Magento\Sales\Api\Data\OrderExtensionInterface|null $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();

        /** @var \Magento\Sales\Api\Data\OrderExtension $orderExtension */
        $orderExtension = $extensionAttributes ?: $this->orderExtensionFactory->create();

        $attributeCodes = $this->config->getCustomOrderExtensionAttributeCodes();
        foreach ($attributeCodes as $attributeCode) {
            if ($data = $order->getData($attributeCode)) {
                $orderExtension->setData($attributeCode, $data);
            }
        }

        // Update Order ExtensionAttributes
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }
}
