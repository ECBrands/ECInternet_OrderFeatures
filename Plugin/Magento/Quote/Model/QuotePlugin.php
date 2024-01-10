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
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Logger\Logger;

/**
 * Plugin for Magento\Quote\Model\Quote
 */
class QuotePlugin
{
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $_addressRepository;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    private $_quoteAddressFactory;

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * @var \ECInternet\OrderFeatures\Logger\Logger
     */
    private $_logger;

    /**
     * QuotePlugin constructor.
     *
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Model\Quote\AddressFactory        $quoteAddressFactory
     * @param \ECInternet\OrderFeatures\Helper\Data            $helper
     * @param \ECInternet\OrderFeatures\Logger\Logger          $logger
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        QuoteAddressFactory $quoteAddressFactory,
        Data $helper,
        Logger $logger
    ) {
        $this->_addressRepository   = $addressRepository;
        $this->_quoteAddressFactory = $quoteAddressFactory;
        $this->_helper              = $helper;
        $this->_logger              = $logger;
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
        if (!$this->_helper->shouldBillingAddressBeHidden()) {
            return $proceed($address);
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $subject->getCustomer();
        $defaultAddressId = $customer->getDefaultBilling();

        try {
            /** @var \Magento\Customer\Api\Data\AddressInterface $defaultBillingAddress */
            $defaultBillingAddress = $this->_addressRepository->getById($defaultAddressId);

            /** @var \Magento\Quote\Model\Quote\Address $old */
            $old = $subject->getBillingAddress();

            if (!empty($old)) {
                $old->importCustomerAddressData($defaultBillingAddress);
            } else {
                $quoteAddress = $this->_quoteAddressFactory->create();
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
        $this->_logger->info('Plugin/Quote/Model/QuotePlugin - ' . $message, $extra);
    }
}
