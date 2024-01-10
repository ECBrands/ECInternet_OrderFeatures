<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Data install script
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param \Magento\Eav\Model\Config          $eavConfig
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_eavConfig       = $eavConfig;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Install data for a module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface   $context
     *
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'erp_terms',
            [
                'type'         => 'varchar',
                'label'        => 'ERP Terms',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => false,
                'position'     => 999,
                'system'       => 0,
            ]
        );

        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $customerNumberAttribute */
        $customerERPTermsAttribute = $this->_eavConfig->getAttribute(
            Customer::ENTITY,
            'erp_terms'
        );

        $customerERPTermsAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );

        /* @noinspection PhpDeprecationInspection */
        $customerERPTermsAttribute->save();
    }
}
