<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Plugin\Magento\Quote\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\AddressFactory as QuoteAddressFactory;
use ECInternet\OrderFeatures\Logger\Logger;
use ECInternet\OrderFeatures\Model\Config;

/**
 * Plugin for Magento\Quote\Model\Quote
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class QuotePlugin
{
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    private $quoteAddressFactory;

    /**
     * @var \ECInternet\OrderFeatures\Logger\Logger
     */
    private $logger;

    /**
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * QuotePlugin constructor.
     *
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Model\Quote\AddressFactory        $quoteAddressFactory
     * @param \ECInternet\OrderFeatures\Logger\Logger          $logger
     * @param \ECInternet\OrderFeatures\Model\Config           $config
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        QuoteAddressFactory $quoteAddressFactory,
        Logger $logger,
        Config $config
    ) {
        $this->addressRepository   = $addressRepository;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->logger              = $logger;
        $this->config              = $config;
    }

    /**
     * Hides billing address
     *
     * @param \Magento\Quote\Model\Quote               $subject
     * @param callable                                 $proceed
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function aroundSetBillingAddress(
        Quote $subject,
        callable $proceed,
        AddressInterface $address
    ) {
        if (!$this->config->isModuleEnabled()) {
            $this->log('aroundSetBillingAddress() - Module is disabled');
            return $proceed($address);
        }

        if (!$this->config->shouldHideBillingAddress()) {
            $this->log('aroundSetBillingAddress() - Hide billing address setting not enabled');
            return $proceed($address);
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $subject->getCustomer();
        if (!$customer) {
            $this->log('aroundSetBillingAddress() - No customer found in quote');
            return $proceed($address);
        }

        $defaultAddressId = $customer->getDefaultBilling();
        if (empty($defaultAddressId)) {
            $this->log('aroundSetBillingAddress() - No default billing address found for customer');
            return $proceed($address);
        }

        if (!is_numeric($defaultAddressId)) {
            $this->log('aroundSetBillingAddress() - Default billing address ID is not numeric: ' . $defaultAddressId);
            return $proceed($address);
        }

        /** @var \Magento\Customer\Api\Data\AddressInterface $defaultBillingAddress */
        $defaultBillingAddress = $this->getCustomerAddressById((int)$defaultAddressId);
        if ($defaultBillingAddress === null) {
            $this->log('aroundSetBillingAddress() - No customer address with id ' . $defaultAddressId);
            return $proceed($address);
        }

        /*
         * If there is a billing address set on the quote, override with the Customer's default billing address.
         * If there is no billing address set on the quote, create a new Quote Address object, import the Customer's
         * default billing address data into it, and then add it to the quote.
         */

        /** @var \Magento\Quote\Model\Quote\Address $billingAddress */
        if ($billingAddress = $subject->getBillingAddress()) {
            // Import quote address data from customer address Data Object
            $billingAddress->importCustomerAddressData($defaultBillingAddress);
        } else {
            // Create new QuoteAddress object and import the Customer's default billing address data into it
            $quoteAddress = $this->quoteAddressFactory->create();
            $quoteAddress->importCustomerAddressData($defaultBillingAddress);

            $subject->addAddress($quoteAddress);
        }

        return $subject;
    }

    /**
     * Fetch CustomerAddress by ID
     *
     * @param int $customerAddressId
     *
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    private function getCustomerAddressById(int $customerAddressId)
    {
        try {
            return $this->addressRepository->getById($customerAddressId);
        } catch (LocalizedException $e) {
            $this->log('getCustomerAddress()', [
                'customerAddressId' => $customerAddressId,
                'exception'         => $e->getMessage()
            ]);
        }

        return null;
    }

    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Plugin/Quote/Model/QuotePlugin - ' . $message, $extra);
    }
}
