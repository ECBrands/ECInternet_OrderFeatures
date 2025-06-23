<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\AddressFactory as QuoteAddressFactory;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\OrderRepositoryInterface;
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Observer for 'sales_order_place_after' event
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    private $quoteAddressFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    private $toOrderAddress;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $helper;

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * SalesOrderPlaceAfter constructor.
     *
     * @param \Magento\Quote\Model\Quote\AddressFactory         $quoteAddressFactory
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $toOrderAddress
     * @param \Magento\Sales\Api\OrderRepositoryInterface       $orderRepository
     * @param \ECInternet\OrderFeatures\Helper\Data             $helper
     * @param \ECInternet\OrderFeatures\Model\Config            $config
     */
    public function __construct(
        QuoteAddressFactory $quoteAddressFactory,
        ToOrderAddress $toOrderAddress,
        OrderRepositoryInterface $orderRepository,
        Data $helper,
        Config $config
    ) {
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->toOrderAddress      = $toOrderAddress;
        $this->orderRepository      = $orderRepository;
        $this->helper               = $helper;
        $this->config               = $config;
    }

    /**
     * Update order billing address to be Customer default billing address
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     */
    public function execute(
        Observer $observer
    ) {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        if (!$this->config->isPaymentBillingEnabled()) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $observer->getEvent()->getData('order')) {
            /** @var \Magento\Sales\Api\Data\OrderAddressInterface $billingAddress */
            if ($billingAddress = $order->getBillingAddress()) {
                // Set the payment address.
                $this->helper->setOrderPaymentAddress($order, $billingAddress);

                /** @var \Magento\Customer\Model\Customer $customer */
                if ($customer = $order->getCustomer()) {
                    if ($customerDefaultBillingAddress = $customer->getDefaultBillingAddress()) {
                        // Convert to quote Address
                        $quoteAddress = $this->quoteAddressFactory->create();
                        $quoteAddress->importCustomerAddressData($customerDefaultBillingAddress->getDataModel());

                        // Convert to order Address
                        $orderAddress = $this->toOrderAddress->convert($quoteAddress);

                        // Update order
                        $order->setBillingAddress($orderAddress);
                    }
                }

                $this->orderRepository->save($order);
            }
        }
    }
}
