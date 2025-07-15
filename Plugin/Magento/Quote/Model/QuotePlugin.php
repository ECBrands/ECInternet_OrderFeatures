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
use ECInternet\OrderFeatures\Model\Config;
use Psr\Log\LoggerInterface;

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
     * @var \ECInternet\OrderFeatures\Model\Config
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * QuotePlugin constructor.
     *
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Model\Quote\AddressFactory        $quoteAddressFactory
     * @param \ECInternet\OrderFeatures\Model\Config           $config
     * @param \Psr\Log\LoggerInterface                         $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        QuoteAddressFactory $quoteAddressFactory,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->addressRepository   = $addressRepository;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->config              = $config;
        $this->logger              = $logger;
    }

    /**
     * Hides billing address
     *
     * @param \Magento\Quote\Model\Quote                    $subject
     * @param callable                                      $proceed
     * @param \Magento\Quote\Api\Data\AddressInterface|null $address
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function aroundSetBillingAddress(
        Quote $subject,
        callable $proceed,
        ?AddressInterface $address = null
    ) {
        if (!$this->config->shouldBillingAddressBeHidden()) {
            return $proceed($address);
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer         = $subject->getCustomer();
        $defaultAddressId = $customer->getDefaultBilling();

        try {
            /** @var \Magento\Customer\Api\Data\AddressInterface $defaultBillingAddress */
            $defaultBillingAddress = $this->addressRepository->getById($defaultAddressId);

            /** @var \Magento\Quote\Model\Quote\Address $old */
            $old = $subject->getBillingAddress();

            if ($old !== null) {
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
