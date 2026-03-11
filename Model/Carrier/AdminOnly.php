<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\Carrier;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * AdminOnly Carrier Model
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class AdminOnly extends AbstractCarrier implements CarrierInterface
{
    public const CODE = 'ecinternet_admin_only';

    protected $_code = self::CODE;

    protected $_isFixed = true;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * AdminOnly constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\App\State                                $appState
     * @param array                                                       $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        State $appState,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->appState          = $appState;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return [self::CODE => $this->getConfigData('name')];
    }

    /**
     * @inheritDoc
     */
    public function collectRates(
        RateRequest $request
    ) {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        try {
            if (!$this->isAdmin()) {
                return false;
            }
        } catch (LocalizedException) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setData('carrier', self::CODE);
        $method->setData('carrier_title', $this->getConfigData('title'));
        $method->setData('method', self::CODE);
        $method->setData('method_title', $this->getConfigData('name'));
        $method->setData('price', $this->getConfigData('price'));
        $method->setData('cost', $this->getConfigData('price'));

        $result->append($method);

        return $result;
    }

    /**
     * Checks if user is logged in as admin.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isAdmin()
    {
        return $this->appState->getAreaCode() === FrontNameResolver::AREA_CODE;
    }
}
