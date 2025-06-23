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

        try {
            /** @var \Magento\Customer\Api\Data\AddressInterface $defaultBillingAddress */
            $defaultBillingAddress = $this->addressRepository->getById($defaultAddressId);

            /** @var \Magento\Quote\Model\Quote\Address $old */
            if ($old = $subject->getBillingAddress()) {
                $old->importCustomerAddressData($defaultBillingAddress);
            } else {
                $quoteAddress = $this->quoteAddressFactory->create();
                $quoteAddress->importCustomerAddressData($defaultBillingAddress);

                $subject->addAddress($quoteAddress);
            }
        } catch (LocalizedException $e) {
            $this->log('aroundSetBillingAddress()', [
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString()
            ]);

            // Address not found?
            return $proceed($address);
        }

        return $subject;
    }

    private function log(string $message, array $extra = [])
    {
        $this->logger->info('Plugin/Quote/Model/QuotePlugin - ' . $message, $extra);
    }
}
