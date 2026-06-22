<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Plugin\Magento\Checkout\Model;

use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Quote\Api\Data\AddressInterface as QuoteAddressInterface;
use Magento\Quote\Api\Data\PaymentExtension;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Plugin for Magento\Checkout\Model\PaymentInformationManagement
 */
class PaymentInformationManagementPlugin
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * PaymentInformationManagementPlugin constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Pull 'order_comment' and 'po_number' from Payment ExtensionAttributes and set on Order
     *
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param int                                                  $result
     * @param int                                                  $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface             $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null        $billingAddress
     *
     * @noinspection PhpMissingParamTypeInspection
     * @noinspection PhpUnusedParameterInspection
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return int
     */
    public function afterSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagement $subject,
        int $result,
        $cartId,
        PaymentInterface $paymentMethod,
        ?QuoteAddressInterface $billingAddress = null
    ) {
        if ($result) {
            /** @var \Magento\Quote\Api\Data\PaymentExtensionInterface $paymentExtensionAttributes */
            if ($paymentExtensionAttributes = $paymentMethod->getExtensionAttributes()) {
                if ($paymentExtensionAttributes instanceof PaymentExtension) {
                    // Cache 'order_comment' and 'po_number'
                    $orderComment = $paymentExtensionAttributes->getOrderComment();
                    $poNumber     = $paymentExtensionAttributes->getPoNumber();

                    /** @var \Magento\Sales\Api\Data\OrderInterface $order */
                    $order = $this->orderRepository->get($result);

                    // Set attribute values on Order
                    $order->setData(Config::ATTRIBUTE_ORDER_COMMENT, $orderComment);
                    $order->setData(Config::ATTRIBUTE_PO_NUMBER, $poNumber);

                    // Save order
                    $this->orderRepository->save($order);
                }
            }
        }

        return $result;
    }
}
