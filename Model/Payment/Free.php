<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\Payment;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\Data\CartInterface;
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Logger\Logger as OrderFeaturesLogger;

/**
 * Free Payment Method Model
 */
class Free extends AbstractMethod
{
    const CODE = 'ecinternet_free';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var bool
     */
    protected $_isGateway = false;

    /**
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $_authSession;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * Free constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory            $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                 $customAttributeFactory
     * @param \Magento\Payment\Helper\Data                                 $paymentHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger                         $logger
     * @param \Magento\Backend\Model\Auth\Session                          $authSession
     * @param \ECInternet\OrderFeatures\Helper\Data                        $helper
     * @param \ECInternet\OrderFeatures\Logger\Logger                      $orderFeaturesLogger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     * @param \Magento\Directory\Helper\Data|null                          $directory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        AuthSession $authSession,
        Data $helper,
        OrderFeaturesLogger $orderFeaturesLogger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentHelper,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );

        $this->_authSession = $authSession;
        $this->_helper      = $helper;
        $this->_logger      = $orderFeaturesLogger;
    }

    /**
     * Authorize payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float                                $amount
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function authorize(
        InfoInterface $payment,
        $amount
    ) {
        $this->log('authorize()', [$payment->getData()]);

        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float                                $amount
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function capture(
        InfoInterface $payment,
        $amount
    ) {
        $this->log('capture()', [$payment->getData()]);

        return $this;
    }

    /**
     * Refund specified amount for payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float                                $amount
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @api
     */
    public function refund(
        InfoInterface $payment,
        $amount
    ) {
        $this->log('refund()', [$payment->getData()]);

        return $this;
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     *
     * @return bool
     */
    public function isAvailable(
        CartInterface $quote = null
    ) {
        //$this->log('isAvailable()');

        if (!$this->_helper->isModuleEnabled()) {
            $this->log('isAvailable() - Module is not enabled.');

            return false;
        }

        // Admin only
        return $this->_authSession->isLoggedIn();
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     */
    private function log(string $message, array $extra = [])
    {
        $this->_logger->info('Model/Payment/Free - ' . $message, $extra);
    }
}
