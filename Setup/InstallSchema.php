<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Schema install script
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install DB schema for a module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        // ERPTERMS
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecinternet_orderfeatures_erpterms')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Active'
        )->addColumn(
            'erp_terms',
            Table::TYPE_TEXT,
            60,
            ['nullable' => false, 'default' => ''],
            'ERP Terms'
        )->addColumn(
            'erp_termsdesc',
            Table::TYPE_TEXT,
            60,
            ['nullable' => true, 'default' => ''],
            'ERP Terms Description'
        )->addColumn(
            'po_requirement',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is PO Required'
        )->addColumn(
            'limit_terms',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Limit Terms'
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
