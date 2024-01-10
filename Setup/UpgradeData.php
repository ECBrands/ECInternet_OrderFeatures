<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Data upgrade script
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $_customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $_attributeSetFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var \Magento\Quote\Setup\QuoteSetupFactory
     */
    private $_quoteSetupFactory;

    /**
     * @var \Magento\Sales\Model\Order\StatusFactory
     */
    private $_statusFactory;

    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    private $_salesSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory   $customerSetupFactory
     * @param \Magento\Eav\Model\Config                      $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Setup\EavSetupFactory             $eavSetupFactory
     * @param \Magento\Quote\Setup\QuoteSetupFactory         $quoteSetupFactory
     * @param \Magento\Sales\Model\Order\StatusFactory       $statusFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory         $salesSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        EavConfig $eavConfig,
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        StatusFactory $statusFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->_customerSetupFactory = $customerSetupFactory;
        $this->_eavConfig            = $eavConfig;
        $this->_attributeSetFactory  = $attributeSetFactory;
        $this->_eavSetupFactory      = $eavSetupFactory;
        $this->_quoteSetupFactory    = $quoteSetupFactory;
        $this->_statusFactory        = $statusFactory;
        $this->_salesSetupFactory    = $salesSetupFactory;
    }

    /**
     * Upgrades DB for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     *
     * @return void
     * @throws \Exception
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        // Add 'erp_terms' to quote, order, and invoice
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
            $quoteSetup = $this->_quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            // Build AttributeCode array
            $attributesCodes = [
                'erp_terms',
                'po_number',
                'order_comment',
            ];

            $attributeData = [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'visible'  => false,
                'nullable' => true,
            ];

            foreach ($attributesCodes as $attributeCode) {
                // Add to Quote, Order, and Invoice
                $quoteSetup->addAttribute('quote', $attributeCode, $attributeData);
                $salesSetup->addAttribute('order', $attributeCode, $attributeData);
                $salesSetup->addAttribute('invoice', $attributeCode, $attributeData);
            }
        }

        // Add 'erp_terms' to customer
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, 'erp_terms')
                ->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'erp_terms' to customer address
        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
            $quoteSetup = $this->_quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            // Build AttributeCode array
            $attributesCodes = [
                'ship_via'
            ];

            $attributeData = [
                'type'     => Table::TYPE_TEXT,
                'length'   => 6,
                'visible'  => false,
                'nullable' => true,
            ];

            foreach ($attributesCodes as $attributeCode) {
                // Add to Quote, Order, and Invoice
                $quoteSetup->addAttribute('quote', $attributeCode, $attributeData);
                $salesSetup->addAttribute('order', $attributeCode, $attributeData);
                $salesSetup->addAttribute('invoice', $attributeCode, $attributeData);
            }
        }

        // Add 'ship_via_code' to customer
        if (version_compare($context->getVersion(), '1.2.5', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(
                Customer::ENTITY,
                'ship_via_code',
                [
                    'type'         => 'varchar',
                    'label'        => 'Ship Via Code',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => true,
                    'position'     => 999,
                    'system'       => 0
                ]
            );

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, 'ship_via_code')
                ->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer'
                    ]
                ]);

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_desc' to customer
        if (version_compare($context->getVersion(), '1.2.6', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute(
                Customer::ENTITY,
                'ship_via_desc',
                [
                    'type'         => 'varchar',
                    'label'        => 'Ship Via Description',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => true,
                    'position'     => 999,
                    'system'       => 0
                ]
            );

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, 'ship_via_desc')
                ->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer'
                    ]
                ]);

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_code' to customer address
        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerAddressEntity */
            $customerAddressEntity = $this->_eavConfig->getEntityType('customer_address');
            $attributeSetId        = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $eavSetup->addAttribute(
                'customer_address',
                'ship_via_code',
                [
                    'type'         => 'varchar',
                    'label'        => 'Ship Via Code',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => false,
                    'position'     => 999,
                    'system'       => 0
                ]
            );

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $this->_eavConfig
                ->getAttribute('customer_address', 'ship_via_code')
                ->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer_address'
                    ]
                ]);

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_desc' to customer address
        if (version_compare($context->getVersion(), '1.2.8', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerAddressEntity */
            $customerAddressEntity = $this->_eavConfig->getEntityType('customer_address');
            $attributeSetId        = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $eavSetup->addAttribute(
                'customer_address',
                'ship_via_desc',
                [
                    'type'         => 'varchar',
                    'label'        => 'Ship Via Description',
                    'input'        => 'text',
                    'required'     => false,
                    'visible'      => true,
                    'user_defined' => false,
                    'position'     => 999,
                    'system'       => 0
                ]
            );

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            $attribute = $this->_eavConfig
                ->getAttribute('customer_address', 'ship_via_desc')
                ->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer_address'
                    ]
                ]);

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'external_order_reference' to order
        // Add 'fully_shipped' order status
        if (version_compare($context->getVersion(), '1.3.7', '<')) {
            /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
            $salesSetup = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            $salesSetup->addAttribute(
                Order::ENTITY,
                'external_order_reference',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                    'visible'  => false,
                    'nullable' => true,
                ]
            );
        }

        // Confirm 'fully_shipped' order status has been added (UpgradeSchema)
        if (version_compare($context->getVersion(), '1.3.8', '<')) {
            $tableName = $setup->getTable('sales_order_status');
            $status[]  = ['status' => 'fully_shipped', 'label' => 'Fully Shipped'];

            // Make this safe for sites where 'partially_shipped' has already been added.
            $setup->getConnection()->insertOnDuplicate($tableName, $status);
        }

        // Assign 'staging' status to status 'processing'
        if (version_compare($context->getVersion(), '1.4.2', '<')) {
            $this->_statusFactory->create()
                ->setStatus('staging')
                ->assignState('processing', false, true);
        }

        // Assign 'partially_shipped' status to status 'processing'
        // Assign 'fully_shipped' status to status 'complete'
        if (version_compare($context->getVersion(), '1.4.3', '<')) {
            $this->_statusFactory->create()
                ->setStatus('partially_shipped')
                ->assignState('processing', false, true);

            $this->_statusFactory->create()
                ->setStatus('fully_shipped')
                ->assignState('complete', false, true);
        }

        $installer->endSetup();
    }
}
