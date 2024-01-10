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

/**
 * Observer for 'sales_order_place_after' event
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    private $_quoteAddressFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    private $_toOrderAddress;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $_orderRepository;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * SalesOrderPlaceAfter constructor.
     *
     * @param \Magento\Quote\Model\Quote\AddressFactory         $quoteAddressFactory
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $toOrderAddress
     * @param \Magento\Sales\Api\OrderRepositoryInterface       $orderRepository
     * @param \ECInternet\OrderFeatures\Helper\Data             $helper
     */
    public function __construct(
        QuoteAddressFactory $quoteAddressFactory,
        ToOrderAddress $toOrderAddress,
        OrderRepositoryInterface $orderRepository,
        Data $helper
    ) {
        $this->_quoteAddressFactory = $quoteAddressFactory;
        $this->_toOrderAddress      = $toOrderAddress;
        $this->_orderRepository     = $orderRepository;
        $this->_helper              = $helper;
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
        if (!$this->_helper->isModuleEnabled()) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $observer->getEvent()->getData('order')) {
            /** @var \Magento\Sales\Api\Data\OrderAddressInterface $billingAddress */
            if ($billingAddress = $order->getBillingAddress()) {
                // Set the billing address to equal the customer's payment address (if enabled)
                if ($this->_helper->isPaymentBillingEnabled()) {
                    // Set the payment address.
                    $this->_helper->setOrderPaymentAddress($order, $billingAddress);

                    /** @var \Magento\Customer\Model\Customer $customer */
                    if ($customer = $order->getCustomer()) {
                        if ($customerDefaultBillingAddress = $customer->getDefaultBillingAddress()) {
                            // Convert to quote Address
                            $quoteAddress = $this->_quoteAddressFactory->create();
                            $quoteAddress->importCustomerAddressData($customerDefaultBillingAddress->getDataModel());

                            // Convert to order Address
                            $orderAddress = $this->_toOrderAddress->convert($quoteAddress);

                            // Update order
                            $order->setBillingAddress($orderAddress);
                        }
                    }
                }

                $this->_orderRepository->save($order);
            }
        }
    }
}
