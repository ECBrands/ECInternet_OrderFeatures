<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup\Patch\Data;

use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddShipViaCodeAttributeToCustomerAddress implements DataPatchInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Attribute
     */
    private $attributeResource;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Attribute   $attributeResource
     * @param \Magento\Customer\Setup\CustomerSetupFactory      $customerSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        AttributeResource $attributeResource,
        CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $setup
    ) {
        $this->attributeResource    = $attributeResource;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->setup                = $setup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(): void
    {
        $this->setup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);
        $customerSetup->addAttribute('customer_address', 'ship_via_code', [
            'type'         => 'varchar',
            'label'        => 'Ship Via Code',
            'input'        => 'text',
            'required'     => false,
            'visible'      => true,
            'user_defined' => false,
            'position'     => 999,
            'system'       => 0,
        ]);

        if ($attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'ship_via_code')) {
            $attribute->setData('used_in_forms', ['adminhtml_customer_address']);
            $this->attributeResource->save($attribute);
        }

        $this->setup->getConnection()->endSetup();
    }
}
