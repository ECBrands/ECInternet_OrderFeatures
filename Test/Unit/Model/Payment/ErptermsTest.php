<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Test\Unit\Model\Payment;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Logger\Logger as OrderFeaturesLogger;
use ECInternet\OrderFeatures\Model\Config;
use ECInternet\OrderFeatures\Model\Payment\Erpterms;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\Collection;
use ECInternet\OrderFeatures\Model\ResourceModel\Erpterms\CollectionFactory as ErptermsCollectionFactory;

/**
 * Unit tests for ECInternet\OrderFeatures\Model\Payment\Erpterms
 */
class ErptermsTest extends TestCase
{
    /**
     * @var \ECInternet\OrderFeatures\Model\Payment\Erpterms
     */
    private $erpTerms;

    /**
     * @var MockObject|Context
     */
    protected $contextMock;

    /**
     * @var MockObject|Registry
     */
    protected $registryMock;

    /**
     * @var MockObject|ExtensionAttributesFactory
     */
    protected $extensionFactoryMock;

    /**
     * @var MockObject|AttributeValueFactory
     */
    protected $customAttributeFactoryMock;

    /**
     * @var MockObject|PaymentHelper
     */
    protected $paymentHelperMock;

    /**
     * @var MockObject|ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /**
     * @var MockObject|Logger
     */
    protected $methodLoggerMock;

    /**
     * @var MockObject|CustomerSession
     */
    protected $customerSessionMock;

    /**
     * @var MockObject|StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var MockObject|OrderFeaturesLogger
     */
    protected $orderFeaturesLoggerMock;

    /**
     * @var MockObject|Config
     */
    protected $configMock;

    /**
     * @var MockObject|ErptermsCollectionFactory
     */
    protected $erptermsCollectionFactoryMock;

    /**
     * @var MockObject|AbstractResource
     */
    protected $resourceMock;

    /**
     * @var MockObject|AbstractDb
     */
    protected $resourceCollectionMock;

    /**
     * @var MockObject|Collection
     */
    protected $erptermsCollectionMock;

    /**
     * @var MockObject|Customer
     */
    protected $customerMock;

    /**
     * @var MockObject|Store
     */
    protected $storeMock;

    protected function setUp(): void
    {
        $this->contextMock                   = $this->createMock(Context::class);
        $this->registryMock                  = $this->createMock(Registry::class);
        $this->extensionFactoryMock          = $this->createMock(ExtensionAttributesFactory::class);
        $this->customAttributeFactoryMock    = $this->createMock(AttributeValueFactory::class);
        $this->paymentHelperMock             = $this->createMock(PaymentHelper::class);
        $this->scopeConfigMock               = $this->createMock(ScopeConfigInterface::class);
        $this->methodLoggerMock              = $this->createMock(Logger::class);
        $this->customerSessionMock           = $this->createMock(CustomerSession::class);
        $this->storeManagerMock              = $this->createMock(StoreManagerInterface::class);
        $this->orderFeaturesLoggerMock       = $this->createMock(OrderFeaturesLogger::class);
        $this->configMock                    = $this->createMock(Config::class);
        $this->erptermsCollectionFactoryMock = $this->getMockBuilder(ErptermsCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->resourceMock                  = $this->createMock(AbstractResource::class);
        $this->resourceCollectionMock        = $this->createMock(AbstractDb::class);

        // Mocks for ErptermsCollection and Customer
        $this->erptermsCollectionMock = $this->createMock(Collection::class);
        $this->customerMock           = $this->createMock(Customer::class);
        $this->storeMock              = $this->createMock(Store::class);

        $this->erptermsCollectionFactoryMock->method('create')->willReturn($this->erptermsCollectionMock);

        $this->erpTerms = new Erpterms(
            $this->contextMock,
            $this->registryMock,
            $this->extensionFactoryMock,
            $this->customAttributeFactoryMock,
            $this->paymentHelperMock,
            $this->scopeConfigMock,
            $this->methodLoggerMock,
            $this->customerSessionMock,
            $this->storeManagerMock,
            $this->orderFeaturesLoggerMock,
            $this->configMock,
            $this->erptermsCollectionFactoryMock,
            $this->resourceMock,
            $this->resourceCollectionMock
        );
    }

    public function testGetCode(): void
    {
        $this->assertEquals('erpterms', $this->erpTerms->getCode());
    }

    public function testGetTitle(): void
    {
        $this->assertEquals('ERP Terms', $this->erpTerms->getTitle());
    }
}
