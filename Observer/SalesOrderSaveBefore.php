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
use Magento\Framework\Event\ManagerInterface;
use ECInternet\OrderFeatures\Helper\Data;

/**
 * Observer for 'sales_order_save_before' event
 */
class SalesOrderSaveBefore implements ObserverInterface
{
    const FREE_PAYMENT_CODE = 'ecinternet_free';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $_authSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManager;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    protected $_helper;

    /**
     * SalesOrderSaveBefore constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session       $authSession
     * @param \Magento\Customer\Model\Session           $customerSession
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \ECInternet\OrderFeatures\Helper\Data     $helper
     */
    public function __construct(
        AuthSession $authSession,
        CustomerSession $customerSession,
        ManagerInterface $eventManager,
        Data $helper
    ) {
        $this->_authSession     = $authSession;
        $this->_customerSession = $customerSession;
        $this->_eventManager    = $eventManager;
        $this->_helper          = $helper;
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
        if (!$this->_helper->isModuleEnabled()) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $observer->getEvent()->getData('order')) {
            /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
            if ($payment = $order->getPayment()) {
                if ($payment->getMethod() === self::FREE_PAYMENT_CODE) {
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
            if ($customer = $this->_customerSession->getCustomer()) {
                $customerErpterms = $customer->getData('erp_terms');

                if (!empty($customerErpterms)) {
                    $order->setData(Data::ATTRIBUTE_ERP_TERMS, $customerErpterms);
                }
            }

            // Handle informal placed_in_admin attribute
            if ($this->_authSession->getUser()) {
                $order->setData(Data::ATTRIBUTE_PLACED_IN_ADMIN, true);
            }

            $this->_eventManager->dispatch('ecinternet_orderfeatures_erp_terms_set', ['order' => $order]);
        }
    }
}
