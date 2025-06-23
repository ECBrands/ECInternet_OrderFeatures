<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * ConfigProvider Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'ecinternet_orderfeatures';

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * ConfigProvider constructor.
     *
     * @param \ECInternet\OrderFeatures\Model\Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [
            self::CODE => [
                'hide_billing'    => $this->config->shouldHideBillingAddress(),
                'show_comment'    => $this->config->shouldShowComment(),
                'show_ponumber'   => $this->config->shouldShowPONumber(),
                'payment_billing' => $this->config->isPaymentBillingEnabled()
            ]
        ];
    }
}
