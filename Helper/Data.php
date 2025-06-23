<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;

/**
 * Helper
 */
class Data extends AbstractHelper
{
    public const ATTRIBUTE_ERP_TERMS                = 'erp_terms';

    public const ATTRIBUTE_EXTERNAL_ORDER_REFERENCE = 'external_order_reference';

    public const ATTRIBUTE_ORDER_COMMENT            = 'order_comment';

    public const ATTRIBUTE_PLACED_IN_ADMIN          = 'placed_in_admin';

    public const ATTRIBUTE_PO_NUMBER                = 'po_number';

    public const ATTRIBUTE_SHIP_VIA                 = 'ship_via';

    public const ATTRIBUTE_SHIP_VIA_CODE            = 'ship_via_code';

    public const ATTRIBUTE_SHIP_VIA_DESC            = 'ship_via_desc';

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
