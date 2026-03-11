<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Config
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

    public const ATTRIBUTE_ERP_TERMS                 = 'erp_terms';

    public const ATTRIBUTE_EXTERNAL_ORDER_REFERENCE  = 'external_order_reference';

    public const ATTRIBUTE_ORDER_COMMENT             = 'order_comment';

    public const ATTRIBUTE_PLACED_IN_ADMIN           = 'placed_in_admin';

    public const ATTRIBUTE_PO_NUMBER                 = 'po_number';

    public const ATTRIBUTE_SHIP_VIA                  = 'ship_via';

    public const ATTRIBUTE_SHIP_VIA_CODE             = 'ship_via_code';

    public const ATTRIBUTE_SHIP_VIA_DESC             = 'ship_via_desc';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
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
     * Should we hide the billing address and always use the customer's default?
     *
     * @return bool
     */
    public function shouldHideBillingAddress()
    {
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
