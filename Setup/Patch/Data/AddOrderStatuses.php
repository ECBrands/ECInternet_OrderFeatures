<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;

class AddOrderStatuses implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var \Magento\Sales\Model\Order\StatusFactory
     */
    private $statusFactory;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Sales\Model\Order\StatusFactory          $statusFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        StatusFactory $statusFactory
    ) {
        $this->setup         = $setup;
        $this->statusFactory = $statusFactory;
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
     */
    public function apply(): void
    {
        $this->setup->getConnection()->startSetup();

        $connection = $this->setup->getConnection();
        $tableName  = $this->setup->getTable('sales_order_status');

        $connection->insertOnDuplicate($tableName, ['status' => 'staging',           'label' => 'Staging']);
        $connection->insertOnDuplicate($tableName, ['status' => 'partially_shipped', 'label' => 'Partially Shipped']);
        $connection->insertOnDuplicate($tableName, ['status' => 'fully_shipped',     'label' => 'Fully Shipped']);

        $this->statusFactory->create()->setStatus('staging')->assignState('processing', false, true);
        $this->statusFactory->create()->setStatus('partially_shipped')->assignState('processing', false, true);
        $this->statusFactory->create()->setStatus('fully_shipped')->assignState('complete', false, true);

        $this->setup->getConnection()->endSetup();
    }
}
