<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Observer;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Observer for 'sales_order_save_before' event
 */
class SalesOrderSaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * SalesOrderSaveBefore constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session       $authSession
     * @param \Magento\Customer\Model\Session           $customerSession
     * @param \ECInternet\OrderFeatures\Model\Config    $config
     */
    public function __construct(
        AuthSession $authSession,
        CustomerSession $customerSession,
        Config $config
    ) {
        $this->authSession     = $authSession;
        $this->customerSession = $customerSession;
        $this->config          = $config;
    }

    /**
     * Update order item and order header prices to 0 if free payment method chosen.
     * Set 'erp_terms' on order
     * Set 'placed_in_admin' on order
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(
        Observer $observer
    ) {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $observer->getEvent()->getData('order')) {
            /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
            if ($payment = $order->getPayment()) {
                /** @noinspection PhpFullyQualifiedNameUsageInspection */
                if ($payment->getMethod() === \ECInternet\OrderFeatures\Model\Payment\Free::CODE) {
                    // Get all items and set them to $0
                    $products = $order->getAllItems();
                    foreach ($products as $product) {
                        $product->setBasePrice(0);
                        $product->setPrice(0);
                        $product->setBaseOriginalPrice(0);
                        $product->setOriginalPrice(0);
                        $product->setBaseTaxAmount(0);
                        $product->setTaxAmount(0);
                        $product->setTaxPercent(0);
                        $product->setBaseRowTotal(0);
                        $product->setRowTotal(0);
                    }

                    $order->setGrandTotal(0);
                    $order->setSubtotal(0);
                    $order->setBaseTaxAmount(0);
                    $order->setTaxAmount(0);
                    $order->setSubtotalInclTax(0);
                }
            }

            // Handle ERP Terms as payment Method
            // TODO: Can we get this from order so we don't need to use customer session?
            if ($customer = $this->customerSession->getCustomer()) {
                $customerErpterms = $customer->getData('erp_terms');

                if (!empty($customerErpterms)) {
                    $order->setData(Config::ATTRIBUTE_ERP_TERMS, $customerErpterms);
                }
            }

            // Handle informal placed_in_admin attribute
            if ($this->authSession->getUser()) {
                $order->setData(Config::ATTRIBUTE_PLACED_IN_ADMIN, true);
            }
        }
    }
}
