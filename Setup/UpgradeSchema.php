<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Schema upgrade script
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        // VERSION 1.0.3
        // Add 'staging' order status.
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $setup->getTable('sales_order_status');
            $status[]  = ['status' => 'staging', 'label' => 'Staging'];

            // Make this safe for sites where 'staging' has already been added.
            $setup->getConnection()->insertOnDuplicate($tableName, $status);
        }

        // VERSION 1.0.4
        // Add 'partially_shipped' order status.
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $tableName = $setup->getTable('sales_order_status');
            $status[]  = ['status' => 'partially_shipped', 'label' => 'Partially Shipped'];

            // Make this safe for sites where 'partially_shipped' has already been added.
            $setup->getConnection()->insertOnDuplicate($tableName, $status);
        }

        $setup->endSetup();
    }
}
