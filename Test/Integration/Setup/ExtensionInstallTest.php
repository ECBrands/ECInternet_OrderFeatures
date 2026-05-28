<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Test\Integration\Setup;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Exception;
use PHPUnit\Framework\TestCase;

class ExtensionInstallTest extends TestCase
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    protected function setUp(): void
    {
        $objectManager            = Bootstrap::getObjectManager();
        $this->eavConfig          = $objectManager->get(EavConfig::class);
        $this->resourceConnection = $objectManager->get(ResourceConnection::class);
    }

    // -------------------------------------------------------------------------
    // Customer EAV attributes
    // -------------------------------------------------------------------------

    public function testCustomerAttributeErpTermsWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('customer', 'erp_terms');

        $this->assertNotNull($attribute, 'Customer attribute "erp_terms" does not exist.');
        $this->assertEquals('varchar', $attribute->getBackendType());
        $this->assertEquals('ERP Terms', $attribute->getStoreLabel());
        $this->assertEquals('text', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(0, $attribute->getIsUserDefined());
        $this->assertEquals(999, $attribute->getData('sort_order'));
        $this->assertEquals(['adminhtml_customer'], $attribute->getUsedInForms());
    }

    public function testCustomerAttributeShipViaCodeWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('customer', 'ship_via_code');

        $this->assertNotNull($attribute, 'Customer attribute "ship_via_code" does not exist.');
        $this->assertEquals('varchar', $attribute->getBackendType());
        $this->assertEquals('Ship Via Code', $attribute->getStoreLabel());
        $this->assertEquals('text', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(1, $attribute->getIsUserDefined());
        $this->assertEquals(999, $attribute->getData('sort_order'));
        $this->assertEquals(['adminhtml_customer'], $attribute->getUsedInForms());
    }

    public function testCustomerAttributeShipViaDescWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('customer', 'ship_via_desc');

        $this->assertNotNull($attribute, 'Customer attribute "ship_via_desc" does not exist.');
        $this->assertEquals('varchar', $attribute->getBackendType());
        $this->assertEquals('Ship Via Description', $attribute->getStoreLabel());
        $this->assertEquals('text', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(1, $attribute->getIsUserDefined());
        $this->assertEquals(999, $attribute->getData('sort_order'));
        $this->assertEquals(['adminhtml_customer'], $attribute->getUsedInForms());
    }

    // -------------------------------------------------------------------------
    // Customer address EAV attributes
    // -------------------------------------------------------------------------

    public function testCustomerAddressAttributeShipViaCodeWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('customer_address', 'ship_via_code');

        $this->assertNotNull($attribute, 'CustomerAddress attribute "ship_via_code" does not exist.');
        $this->assertEquals('varchar', $attribute->getBackendType());
        $this->assertEquals('Ship Via Code', $attribute->getStoreLabel());
        $this->assertEquals('text', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(0, $attribute->getIsUserDefined());
        $this->assertEquals(999, $attribute->getData('sort_order'));
        $this->assertEquals(['adminhtml_customer_address'], $attribute->getUsedInForms());
    }

    public function testCustomerAddressAttributeShipViaDescWasCreatedCorrectly(): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
        $attribute = $this->getAttribute('customer_address', 'ship_via_desc');

        $this->assertNotNull($attribute, 'CustomerAddress attribute "ship_via_desc" does not exist.');
        $this->assertEquals('varchar', $attribute->getBackendType());
        $this->assertEquals('Ship Via Description', $attribute->getStoreLabel());
        $this->assertEquals('text', $attribute->getFrontendInput());
        $this->assertEquals(0, $attribute->getIsRequired());
        $this->assertEquals(1, $attribute->getData('is_visible'));
        $this->assertEquals(0, $attribute->getIsUserDefined());
        $this->assertEquals(999, $attribute->getData('sort_order'));
        $this->assertEquals(['adminhtml_customer_address'], $attribute->getUsedInForms());
    }

    // -------------------------------------------------------------------------
    // Flat table columns (db_schema.xml)
    // -------------------------------------------------------------------------

    public function testSalesOrderColumnsWereCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('sales_order');

        $this->assertTrue($connection->tableColumnExists($table, 'erp_terms'),                'sales_order.erp_terms column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'external_order_reference'), 'sales_order.external_order_reference column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'order_comment'),            'sales_order.order_comment column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'placed_in_admin'),          'sales_order.placed_in_admin column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'po_number'),                'sales_order.po_number column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'ship_via'),                 'sales_order.ship_via column does not exist.');
    }

    public function testQuoteColumnsWereCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('quote');

        $this->assertTrue($connection->tableColumnExists($table, 'erp_terms'),       'quote.erp_terms column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'order_comment'),   'quote.order_comment column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'placed_in_admin'), 'quote.placed_in_admin column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'po_number'),       'quote.po_number column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'ship_via'),        'quote.ship_via column does not exist.');
    }

    public function testSalesInvoiceColumnsWereCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('sales_invoice');

        $this->assertTrue($connection->tableColumnExists($table, 'erp_terms'),       'sales_invoice.erp_terms column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'placed_in_admin'), 'sales_invoice.placed_in_admin column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'order_comment'),   'sales_invoice.order_comment column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'po_number'),       'sales_invoice.po_number column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'ship_via'),        'sales_invoice.ship_via column does not exist.');
    }

    public function testErptermsTableWasCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('ecinternet_orderfeatures_erpterms');

        $this->assertTrue($connection->isTableExists($table), 'ecinternet_orderfeatures_erpterms table does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'entity_id'),      'erpterms.entity_id column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'created_at'),     'erpterms.created_at column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'updated_at'),     'erpterms.updated_at column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'store_id'),       'erpterms.store_id column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'is_active'),      'erpterms.is_active column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'erp_terms'),      'erpterms.erp_terms column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'erp_termsdesc'),  'erpterms.erp_termsdesc column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'po_requirement'), 'erpterms.po_requirement column does not exist.');
        $this->assertTrue($connection->tableColumnExists($table, 'limit_terms'),    'erpterms.limit_terms column does not exist.');
    }

    // -------------------------------------------------------------------------
    // Order status data (AddOrderStatuses patch)
    // -------------------------------------------------------------------------

    public function testOrderStatusesWereCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('sales_order_status');

        foreach (['staging' => 'Staging', 'partially_shipped' => 'Partially Shipped', 'fully_shipped' => 'Fully Shipped'] as $code => $label) {
            $select = $connection->select()->from($table)->where('status = ?', $code);
            $row    = $connection->fetchRow($select);

            $this->assertNotEmpty($row, "Order status \"$code\" should exist.");
            $this->assertEquals($label, $row['label'], "Order status \"$code\" should have label \"$label\".");
        }
    }

    public function testOrderStatusStateAssignmentsWereCreated(): void
    {
        $connection = $this->getConnection();
        $table      = $this->resourceConnection->getTableName('sales_order_status_state');

        $expected = [
            ['status' => 'staging',           'state' => 'processing'],
            ['status' => 'partially_shipped', 'state' => 'processing'],
            ['status' => 'fully_shipped',     'state' => 'complete'],
        ];

        foreach ($expected as $assignment) {
            $select = $connection->select()
                ->from($table)
                ->where('status = ?', $assignment['status'])
                ->where('state = ?', $assignment['state']);
            $row = $connection->fetchRow($select);

            $this->assertNotEmpty(
                $row,
                "Order status \"{$assignment['status']}\" should be assigned to state \"{$assignment['state']}\"."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getAttribute(string $entityTypeCode, string $attributeCode)
    {
        try {
            if ($attribute = $this->eavConfig->getAttribute($entityTypeCode, $attributeCode)) {
                if ($attribute->getAttributeId()) {
                    return $attribute;
                }
            }
        } catch (Exception) {
        }

        return null;
    }

    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }
}
