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
use ECInternet\OrderFeatures\Logger\Logger;
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
     * @var \ECInternet\OrderFeatures\Logger\Logger
     */
    private $logger;

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
     * @param \ECInternet\OrderFeatures\Logger\Logger           $logger
     * @param \ECInternet\OrderFeatures\Model\Config            $config
     */
    public function __construct(
        QuoteAddressFactory $quoteAddressFactory,
        ToOrderAddress $toOrderAddress,
        OrderRepositoryInterface $orderRepository,
        Logger $logger,
        Config $config
    ) {
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->toOrderAddress      = $toOrderAddress;
        $this->orderRepository     = $orderRepository;
        $this->logger              = $logger;
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
        $this->log('execute()');

        if (!$this->config->isModuleEnabled()) {
            $this->log('Module is disabled');
            return;
        }

        if (!$this->config->isPaymentBillingEnabled()) {
            $this->log('Payment billing is disabled');
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
     * @param \Magento\Sales\Model\Order                         $order
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     *
     * @return \Magento\Sales\Model\Order
     */
    private function setOrderPaymentAddress(
        Order $order,
        OrderAddressInterface $address = null
    ) {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $old */
        $old = $this->getOrderPaymentAddress($order);
        if (!empty($old) && !empty($address)) {
            $address->setId($old->getId());
        }

        if (!empty($address)) {
            $address->setEmail($order->getCustomerEmail());
            $order->addAddress($address->setAddressType(self::PAYMENT_ADDRESS_TYPE));
        }

        return $order;
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
            if ($address->getAddressType() == self::PAYMENT_ADDRESS_TYPE) {
                if (!$address->isDeleted()) {
                    return $address;
                }
            }
        }

        return null;
    }

    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Observer/SalesOrderPlaceAfter - ' . $message, $extra);
    }
}
