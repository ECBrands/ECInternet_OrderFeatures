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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Model\Order\StatusFactory;

/**
 * Data upgrade script
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Sales\Model\Order\StatusFactory
     */
    private $statusFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory   $customerSetupFactory
     * @param \Magento\Eav\Model\Config                      $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Setup\EavSetupFactory             $eavSetupFactory
     * @param \Magento\Sales\Model\Order\StatusFactory       $statusFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        EavConfig $eavConfig,
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        StatusFactory $statusFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig            = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory      = $eavSetupFactory;
        $this->statusFactory        = $statusFactory;
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

        // Add 'erp_terms' to customer
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
            if ($attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'erp_terms')) {
                $attribute->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);

                /* @noinspection PhpDeprecationInspection */
                $attribute->save();
            }
        }

        // Add 'ship_via_code' to customer
        if (version_compare($context->getVersion(), '1.2.5', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->attributeSetFactory->create();
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
            if ($attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'ship_via_code')) {
                $attribute->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer'
                    ]
                ]);
            }

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_desc' to customer
        if (version_compare($context->getVersion(), '1.2.6', '<')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerEntity */
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->attributeSetFactory->create();
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
            if ($attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'ship_via_desc')) {
                $attribute->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer'
                    ]
                ]);
            }

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_code' to customer address
        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerAddressEntity */
            $customerAddressEntity = $this->eavConfig->getEntityType('customer_address');
            $attributeSetId        = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->attributeSetFactory->create();
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
            if ($attribute = $this->eavConfig->getAttribute('customer_address', 'ship_via_code')) {
                $attribute->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer_address'
                    ]
                ]);
            }

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
        }

        // Add 'ship_via_desc' to customer address
        if (version_compare($context->getVersion(), '1.2.8', '<')) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            /** @var \Magento\Eav\Model\Entity\Type $customerAddressEntity */
            $customerAddressEntity = $this->eavConfig->getEntityType('customer_address');
            $attributeSetId        = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet     = $this->attributeSetFactory->create();
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
            if ($attribute = $this->eavConfig->getAttribute('customer_address', 'ship_via_desc')) {
                $attribute->addData([
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms'      => [
                        'adminhtml_customer_address'
                    ]
                ]);
            }

            /* @noinspection PhpDeprecationInspection */
            $attribute->save();
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
            $this->statusFactory->create()
                ->setStatus('staging')
                ->assignState('processing', false, true);
        }

        // Assign 'partially_shipped' status to status 'processing'
        // Assign 'fully_shipped' status to status 'complete'
        if (version_compare($context->getVersion(), '1.4.3', '<')) {
            $this->statusFactory->create()
                ->setStatus('partially_shipped')
                ->assignState('processing', false, true);

            $this->statusFactory->create()
                ->setStatus('fully_shipped')
                ->assignState('complete', false, true);
        }

        $installer->endSetup();
    }
}
