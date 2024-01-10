<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;

/**
 * Helper
 */
class Data extends AbstractHelper
{
    private const CONFIG_PATH_ENABLED                = 'order_features/general/enable';

    private const CONFIG_PATH_CLEAR_TOTALS           = 'order_features/checkout/clear_totals';

    private const CONFIG_PATH_HIDE_BILLING           = 'order_features/checkout/hide_billing';

    private const CONFIG_PATH_SHOW_COMMENT           = 'order_features/checkout/show_comment';

    private const CONFIG_PATH_SHOW_PONUMBER          = 'order_features/checkout/show_ponumber';

    private const CONFIG_PATH_PAYMENT_BILLING        = 'order_features/checkout/payment_billing';

    private const CONFIG_PATH_SKIPPABLE_SKUS         = 'order_features/shipments/skippable_skus';

    private const CONFIG_PATH_STORE_PICKUP           = 'order_features/shipments/store_pickups_to_trigger';

    private const CONFIG_PATH_MARK_AS_COMPLETE       = 'order_features/shipments/mark_order_complete';

    private const PAYMENT_ADDRESS_TYPE               = 'payment';

    public const ATTRIBUTE_ERP_TERMS                 = 'erp_terms';

    public const ATTRIBUTE_EXTERNAL_ORDER_REFERENCE  = 'external_order_reference';

    public const ATTRIBUTE_ORDER_COMMENT             = 'order_comment';

    public const ATTRIBUTE_PLACED_IN_ADMIN           = 'placed_in_admin';

    public const ATTRIBUTE_PO_NUMBER                 = 'po_number';

    public const ATTRIBUTE_SHIP_VIA                  = 'ship_via';

    public const ATTRIBUTE_SHIP_VIA_CODE             = 'ship_via_code';

    public const ATTRIBUTE_SHIP_VIA_DESC             = 'ship_via_desc';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session       $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);

        $this->_customerSession = $customerSession;
    }

    /**
     * Is module enabled?
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED);
    }

    /**
     * Should we clear total for the Free payment method?
     *
     * @return bool
     */
    public function clearTotalsForFreePaymentMethod()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_CLEAR_TOTALS);
    }

    /**
     * Should the billing address be hidden?
     *
     * @return bool
     */
    public function shouldBillingAddressBeHidden()
    {
        if (!$this->isModuleEnabled()) {
            return false;
        }

        if (!$this->_customerSession->isLoggedIn()) {
            return false;
        }

        if (!$this->_customerSession->getCustomer()->getDefaultBillingAddress()) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_HIDE_BILLING);
    }

    /**
     * Should we show Comment input in checkout?
     *
     * @return bool
     */
    public function shouldShowComment()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_SHOW_COMMENT);
    }

    /**
     * Should we show PO Number input in checkout?
     *
     * @return bool
     */
    public function shouldShowPONumber()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_SHOW_PONUMBER);
    }

    /**
     * Is payment billing enabled?
     *
     * @return bool
     */
    public function isPaymentBillingEnabled()
    {
        if (!$this->isModuleEnabled()) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_PAYMENT_BILLING);
    }

    /**
     * List of SKUs to skip
     *
     * @return string
     */
    public function getSkippableSkus()
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SKIPPABLE_SKUS);
    }

    /**
     * Get array of store pickup methods
     *
     * @return string[]
     */
    public function getStorePickupMethodsForNotification()
    {
        $storePickupMethods = [];

        if ($storePickupsToTrigger = $this->scopeConfig->getValue(self::CONFIG_PATH_STORE_PICKUP)) {
            $storePickupMethods = explode(',', $storePickupsToTrigger);
        }

        return $storePickupMethods;
    }

    /**
     * Should we mark the order as 'complete' if all items have been fully shipped?
     *
     * @return bool
     */
    public function shouldMarkOrderAsComplete()
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_MARK_AS_COMPLETE);
    }

    /**
     * Sets the payment address, if any, for the order
     *
     * @param \Magento\Sales\Model\Order                         $order
     * @param \Magento\Sales\Api\Data\OrderAddressInterface|null $address
     *
     * @return \Magento\Sales\Model\Order
     */
    public function setOrderPaymentAddress(
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
    public function getOrderPaymentAddress(
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

    /**
     * Get array of extension-specific Order ExtensionAttribute codes
     *
     * @return string[]
     */
    public function getCustomOrderExtensionAttributeCodes()
    {
        return [
            self::ATTRIBUTE_ERP_TERMS,
            self::ATTRIBUTE_EXTERNAL_ORDER_REFERENCE,
            self::ATTRIBUTE_ORDER_COMMENT,
            self::ATTRIBUTE_PLACED_IN_ADMIN,
            self::ATTRIBUTE_PO_NUMBER,
            self::ATTRIBUTE_SHIP_VIA,
            self::ATTRIBUTE_SHIP_VIA_DESC
        ];
    }
}
