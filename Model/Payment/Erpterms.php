<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\Payment;

use Magento\Customer\Model\Session as CustomerSession;
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
use ECInternet\OrderFeatures\Logger\Logger as OrderFeaturesLogger;
use ECInternet\OrderFeatures\Model\Config;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory as ErptermsCollection;

/**
 * Erpterms payment method model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Erpterms extends AbstractMethod
{
    public const CODE                          = 'ecinternet_erpterms';

    public const DEFAULT_TITLE                 = 'ERPTerms';

    public const CONFIG_PATH_TITLE             = 'payment/ecinternet_erpterms/title';

    public const CONFIG_PATH_ALLOWED_GROUPS    = 'payment/ecinternet_erpterms/allowed_groups';

    public const CONFIG_PATH_DEFAULT_TERM_NAME = 'payment/ecinternet_erpterms/default_term_name';

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
     * @var \ECInternet\OrderFeatures\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory
     */
    private $erptermsCollectionFactory;

    /**
     * Erpterms constructor.
     *
     * @param \Magento\Framework\Model\Context                                         $context
     * @param \Magento\Framework\Registry                                              $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory                        $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                             $customAttributeFactory
     * @param \Magento\Payment\Helper\Data                                             $paymentHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                       $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger                                     $logger
     * @param \Magento\Customer\Model\Session                                          $customerSession
     * @param \ECInternet\OrderFeatures\Logger\Logger                                  $orderFeaturesLogger
     * @param \ECInternet\OrderFeatures\Model\Config                                   $config
     * @param \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory $erptermsCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null             $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null                       $resourceCollection
     * @param array                                                                    $data
     * @param \Magento\Directory\Helper\Data|null                                      $directory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        CustomerSession $customerSession,
        OrderFeaturesLogger $orderFeaturesLogger,
        Config $config,
        ErptermsCollection $erptermsCollection,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = [],
        ?DirectoryHelper $directory = null
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

        $this->_logger                   = $orderFeaturesLogger;
        $this->customerSession           = $customerSession;
        $this->config                    = $config;
        $this->erptermsCollectionFactory = $erptermsCollection;
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
     * Retrieve payment method title
     *
     * This MUST return a value or Magento Cloud throws an exception
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        // Set safe
        $title = self::DEFAULT_TITLE;

        // Next, check value in PaymentMethod settings
        $paymentMethodTitle = $this->_scopeConfig->getValue(self::CONFIG_PATH_TITLE);
        if ($paymentMethodTitle) {
            $title = $paymentMethodTitle;
        }

        // Use value in "Default Term Name" if it's populated
        $defaultTermName = $this->_scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_TERM_NAME);
        if ($defaultTermName) {
            $title = $defaultTermName;
        }

        // Look up term assigned to the current customer and use that if available.
        $terms = $this->getCustomerERPTerms();
        if (!empty($terms)) {
            /** @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\Collection $termsCollection */
            $termsCollection = $this->erptermsCollectionFactory->create()
                ->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_ERP_TERMS, ['eq' => $terms])
                ->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_IS_ACTIVE, ['eq' => 1]);

            if ($erpterm = $termsCollection->getFirstItem()) {
                if ($erpterm instanceof \ECInternet\OrderFeatures\Model\Erpterms) {
                    if ($description = $erpterm->getDescription()) {
                        $title = $description;
                    }
                }
            }
        }

        return $title;
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     *
     * @return bool
     */
    public function isAvailable(
        ?CartInterface $quote = null
    ) {
        if (!$this->config->isModuleEnabled()) {
            $this->log('isAvailable() - Module is not enabled.');
            return false;
        }

        $allowedCustomerGroups = $this->getAllowedCustomerGroupIds();
        if (count($allowedCustomerGroups) === 0) {
            $this->log('isAvailable() - 0 allowed customer groups.');
            return false;
        }

        $allowedErpterms = $this->getAllowedErpterms();
        if (count($allowedErpterms) === 0) {
            $this->log('isAvailable() - 0 allowed erpterms.');
            return false;
        }

        $customerErpterms = $this->getCustomerERPTerms();
        if (!$customerErpterms) {
            $this->log("isAvailable() - Customer does not have 'erp_terms' attribute value.");
            return false;
        }

        if (!in_array($customerErpterms, $allowedErpterms)) {
            $this->log("isAvailable() - Customer does not have allowed 'erp_terms' value.");
            return false;
        }

        $customerGroupId = $this->getCustomerGroupId();
        if ($customerGroupId === null) {
            $this->log('isAvailable() - Customer groupId is empty.');
            return false;
        }

        if (!in_array($customerGroupId, $allowedCustomerGroups)) {
            $this->log('isAvailable() - CustomerGroup not in allowed groups.');
            return false;
        }

        return true;
    }

    /**
     * Get array of allowed CustomerGroup Ids
     *
     * @return int[]
     */
    private function getAllowedCustomerGroupIds()
    {
        $allowedCustomerGroupIds = [];

        if ($allowedGroups = $this->_scopeConfig->getValue(self::CONFIG_PATH_ALLOWED_GROUPS)) {
            $allowedCustomerGroupIds = explode(',', $allowedGroups);
        }

        return $allowedCustomerGroupIds;
    }

    /**
     * Get collection of allowed Erpterms
     *
     * @return string[]
     */
    private function getAllowedErpterms()
    {
        /** @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\Collection $termCollection */
        $termCollection = $this->erptermsCollectionFactory->create()
            ->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_IS_ACTIVE, ['eq' => 1]);

        return $this->uppercaseTrimArray($termCollection->getColumnValues(Config::ATTRIBUTE_ERP_TERMS));
    }

    /**
     * Attempt to pull 'erp_terms' attribute value from current Customer
     *
     * @return string|null
     */
    private function getCustomerERPTerms()
    {
        if ($customer = $this->customerSession->getCustomer()) {
            if ($customerErpTermsValue = $customer->getData(Config::ATTRIBUTE_ERP_TERMS)) {
                return $this->uppercaseTrim((string)$customerErpTermsValue);
            }
        }

        return null;
    }

    /**
     * Get CustomerGroup id for current Customer
     *
     * @return int|null
     */
    private function getCustomerGroupId()
    {
        if ($customer = $this->customerSession->getCustomer()) {
            return $customer->getGroupId();
        }

        return null;
    }

    /**
     * Uppercase and trim single value
     *
     * @param string $value
     *
     * @return string
     */
    private function uppercaseTrim(string $value)
    {
        return strtoupper(trim($value));
    }

    /**
     * Uppercase and trim array of values
     *
     * @param array $array
     *
     * @return array
     */
    private function uppercaseTrimArray(array $array)
    {
        return array_map('strtoupper', array_map('trim', $array));
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     *
     * @return void
     */
    private function log(string $message, array $extra = [])
    {
        $this->_logger->info('Model/Payment/Erpterms - ' . $message, $extra);
    }
}
