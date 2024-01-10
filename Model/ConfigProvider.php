<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use ECInternet\OrderFeatures\Helper\Data;

/**
 * ConfigProvider Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ecinternet_orderfeatures';

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * ConfigProvider constructor.
     *
     * @param \ECInternet\OrderFeatures\Helper\Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [
            self::CODE => [
                'hide_billing'    => $this->_helper->shouldBillingAddressBeHidden(),
                'show_comment'    => $this->_helper->shouldShowComment(),
                'show_ponumber'   => $this->_helper->shouldShowPONumber(),
                'payment_billing' => $this->_helper->isPaymentBillingEnabled()
            ]
        ];
    }
}
