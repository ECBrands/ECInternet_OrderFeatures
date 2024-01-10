<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Model\Payment;

use Magento\Backend\Model\Session\Quote as AdminQuoteSession;
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
use Magento\Store\Model\StoreManagerInterface;
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Logger\Logger as OrderFeaturesLogger;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory as ErptermsCollection;

/**
 * Erpterms payment method model
 */
class Erpterms extends AbstractMethod
{
    const CODE                          = 'ecinternet_erpterms';

    const DEFAULT_TITLE                 = 'ERPTerms';

    const CONFIG_PATH_TITLE             = 'payment/ecinternet_erpterms/title';

    const CONFIG_PATH_ALLOWED_GROUPS    = 'payment/ecinternet_erpterms/allowed_groups';

    const CONFIG_PATH_DEFAULT_TERM_NAME = 'payment/ecinternet_erpterms/default_term_name';

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
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $_adminQuoteSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory
     */
    private $_erptermsCollectionFactory;

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
     * @param \Magento\Backend\Model\Session\Quote                                     $adminQuoteSession
     * @param \Magento\Customer\Model\Session                                          $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface                               $storeManager
     * @param \ECInternet\OrderFeatures\Helper\Data                                    $helper
     * @param \ECInternet\OrderFeatures\Logger\Logger                                  $orderFeaturesLogger
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
        AdminQuoteSession $adminQuoteSession,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        Data $helper,
        OrderFeaturesLogger $orderFeaturesLogger,
        ErptermsCollection $erptermsCollection,
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

        $this->_adminQuoteSession         = $adminQuoteSession;
        $this->_customerSession           = $customerSession;
        $this->_storeManager              = $storeManager;
        $this->_helper                    = $helper;
        $this->_logger                    = $orderFeaturesLogger;
        $this->_erptermsCollectionFactory = $erptermsCollection;
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
            // Cache storeId
            $storeId = $this->_storeManager->getStore()->getId();

            /** @var \ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\Collection $termsCollection */
            $termsCollection = $this->_erptermsCollectionFactory->create()
                ->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_ERP_TERMS, ['eq' => $terms])
                //->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_STORE_ID, ['eq' => $storeId])
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
        CartInterface $quote = null
    ) {
        //$this->log('isAvailable()');

        if (!$this->_helper->isModuleEnabled()) {
            $this->log('isAvailable() - Module is not enabled.');

            return false;
        }

        $allowedCustomerGroups = $this->getAllowedCustomerGroupIds();
        if (count($allowedCustomerGroups)) {
            $allowedErpterms = $this->getAllowedErpterms();
            if (count($allowedErpterms)) {
                if ($customerErpterms = $this->getCustomerERPTerms()) {
                    if (in_array($customerErpterms, $allowedErpterms)) {
                        $customerGroupId = $this->getCustomerGroupId();
                        if (!empty($customerGroupId)) {
                            if (in_array($customerGroupId, $allowedCustomerGroups)) {
                                return true;
                            } else {
                                $this->log('isAvailable() - CustomerGroup not in allowed groups.');
                            }
                        } else {
                            $this->log('isAvailable() - Customer groupId is empty.');
                        }
                    } else {
                        $this->log("isAvailable() - Customer does not have allowed 'erp_terms' value.");
                    }
                } else {
                    $this->log("isAvailable() - Customer does not have 'erp_terms' attribute value.");
                }
            } else {
                $this->log('isAvailable() - 0 allowed erpterms.');
            }
        } else {
            $this->log('isAvailable() - 0 allowed customer groups.');
        }

        return false;
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
        $termCollection = $this->_erptermsCollectionFactory->create()
            ->addFieldToFilter(\ECInternet\OrderFeatures\Model\Erpterms::COLUMN_IS_ACTIVE, ['eq' => 1]);

        return $this->uppercaseTrimArray($termCollection->getColumnValues(Data::ATTRIBUTE_ERP_TERMS));
    }

    /**
     * Attempt to pull 'erp_terms' attribute value from current Customer
     *
     * @return string|null
     */
    private function getCustomerERPTerms()
    {
        if ($adminQuote = $this->_adminQuoteSession->getQuote()) {
            if ($adminCustomer = $adminQuote->getCustomer()) {
                if ($termAttribute = $adminCustomer->getCustomAttribute(Data::ATTRIBUTE_ERP_TERMS)) {
                    if ($termValue = $termAttribute->getValue()) {
                        return $this->uppercaseTrim((string)$termValue);
                    }
                }
            }
        }

        if ($customer = $this->_customerSession->getCustomer()) {
            if ($customerErpTermsValue = $customer->getData(Data::ATTRIBUTE_ERP_TERMS)) {
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
        $customerGroupId = null;

        if ($adminQuote = $this->_adminQuoteSession->getQuote()) {
            if ($adminCustomer = $adminQuote->getCustomer()) {
                $customerGroupId = $adminCustomer->getGroupId();
            }
        }

        if ($customerGroupId === null) {
            if ($customer = $this->_customerSession->getCustomer()) {
                $customerGroupId = $customer->getGroupId();
            }
        }

        return $customerGroupId;
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
     */
    private function log(string $message, array $extra = [])
    {
        $this->_logger->info('Model/Payment/Erpterms - ' . $message, $extra);
    }
}
