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
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Observer for 'sales_order_place_after' event
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    private const PAYMENT_ADDRESS_TYPE = 'payment';

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
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * SalesOrderPlaceAfter constructor.
     *
     * @param \Magento\Quote\Model\Quote\AddressFactory         $quoteAddressFactory
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $toOrderAddress
     * @param \Magento\Sales\Api\OrderRepositoryInterface       $orderRepository
     * @param \ECInternet\OrderFeatures\Model\Config            $config
     */
    public function __construct(
        QuoteAddressFactory $quoteAddressFactory,
        ToOrderAddress $toOrderAddress,
        OrderRepositoryInterface $orderRepository,
        Config $config
    ) {
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->toOrderAddress      = $toOrderAddress;
        $this->orderRepository     = $orderRepository;
        $this->config              = $config;
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
                $this->setOrderPaymentAddress($order, $billingAddress);

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

    /**
     * Sets the payment address, if any, for the order
     *
     * @param \Magento\Sales\Model\Order                    $order
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $address
     *
     * @return void
     */
    private function setOrderPaymentAddress(
        Order $order,
        OrderAddressInterface $address
    ) {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $orderPaymentAddress */
        if ($orderPaymentAddress = $this->getOrderPaymentAddress($order)) {
            $address->setId($orderPaymentAddress->getId());
        }

        $address->setEmail($order->getCustomerEmail());
        $address->setAddressType(self::PAYMENT_ADDRESS_TYPE);

        $order->addAddress($address);
    }

    /**
     * Retrieve order payment address from Order
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null
     */
    private function getOrderPaymentAddress(
        Order $order
    ) {
        foreach ($order->getAddresses() as $address) {
            if ((string)$address->getAddressType() === self::PAYMENT_ADDRESS_TYPE) {
                if (!$address->isDeleted()) {
                    return $address;
                }
            }
        }

        return null;
    }
}
