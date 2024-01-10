<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use ECInternet\OrderFeatures\Helper\Data;
use ECInternet\OrderFeatures\Logger\Logger;
use Exception;

/**
 * Observer for 'sales_order_shipment_save_after' event
 */
class SalesOrderShipmentAfter implements ObserverInterface
{
    const ORDER_STATUS_PARTIALLY_SHIPPED = 'partially_shipped';

    const ORDER_STATUS_FULLY_SHIPPED     = 'fully_shipped';

    /**
     * @var \ECInternet\OrderFeatures\Helper\Data
     */
    private $_helper;

    /**
     * @var \ECInternet\OrderFeatures\Logger\Logger
     */
    private $_logger;

    /**
     * @var array
     */
    private $_skippableSkus;

    /**
     * SalesOrderShipmentAfter constructor.
     *
     * @param \ECInternet\OrderFeatures\Helper\Data   $helper
     * @param \ECInternet\OrderFeatures\Logger\Logger $logger
     */
    public function __construct(
        Data $helper,
        Logger $logger
    ) {
        $this->_helper        = $helper;
        $this->_skippableSkus = explode(',', $helper->getSkippableSkus());
        $this->_logger        = $logger;
    }

    /**
     * Execute code for 'sales_order_shipment_save_after' event
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(
        Observer $observer
    ) {
        $this->log('execute()');

        if (!$this->_helper->isModuleEnabled()) {
            $this->log('execute() - Disabled module.');

            return;
        }

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getData('shipment');
        $this->log('execute()', ['shipmentId' => $shipment->getId()]);

        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $this->log('execute()', ['increment_id' => $order->getIncrementId()]);

        $orderId = $order->getId();
        $this->log('execute()', ['orderId' => $orderId]);

        // If the order contains unshipped items:
        // - Set order status to 'partially_shipped' via new order status comment
        // If the order does not contain unshipped items
        // - If order is new:
        //   - Set order state to processing
        //   - Set order status to 'partially_shipped'
        //   - Add order status comment for 'partially_shipped'
        // Update order status based on whether or not all items have been shipped
        if ($this->doesOrderContainUnshippedItems($order)) {
            $this->log('execute() - Order contains unshipped items.', ['status' => self::ORDER_STATUS_PARTIALLY_SHIPPED]);
            $order->addCommentToStatusHistory('Partial shipment created.', self::ORDER_STATUS_PARTIALLY_SHIPPED);
        } else {
            $this->log('execute() - Order does not contain unshipped items.', ['status' => self::ORDER_STATUS_FULLY_SHIPPED]);

            if ($this->isOrderNew($order)) {
                $this->log('execute() - Order is new, make partially shipped first...');

                $order->setState(Order::STATE_PROCESSING);
                $order->setStatus(self::ORDER_STATUS_PARTIALLY_SHIPPED);
                $order->addCommentToStatusHistory('Partial shipment created.', self::ORDER_STATUS_PARTIALLY_SHIPPED);
                try {
                    $order->save();
                } catch (Exception $e) {
                    $this->log('execute()', ['exception' => $e->getMessage()]);
                }
            }

            $order->addCommentToStatusHistory('Order fully-shipped.', self::ORDER_STATUS_FULLY_SHIPPED);

            if ($this->shouldMarkOrderAsComplete()) {
                $order->addCommentToStatusHistory('Order complete.', Order::STATE_COMPLETE);
            }
        }
    }

    /**
     * Is Order new?
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    private function isOrderNew(
        Order $order
    ) {
        return $order->getState() == Order::STATE_NEW;
    }

    /**
     * Is Order fully shipped?
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    private function doesOrderContainUnshippedItems(
        Order $order
    ) {
        $this->log('doesOrderContainUnshippedItems()', ['order' => $order->getIncrementId()]);

        // Iterate over items in order, if we find one which isn't fully shipped, change order status.
        foreach ($order->getItems() as $item) {
            // Skip simple products with 'parent_item_id' populated
            if ($item->getProductType() === 'simple' && $item->getParentItemId()) {
                continue;
            }

            // Skip products we've blacklisted
            if ($this->shouldSkipOrderItem($item)) {
                continue;
            }

            // We can return as soon as we find one
            if (!$this->isItemFullyShipped($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Is OrderItem fully shipped?
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     *
     * @return bool
     */
    private function isItemFullyShipped(
        OrderItemInterface $orderItem
    ) {
        $this->log('isItemFullyShipped()', ['sku' => $orderItem->getSku()]);

        $ordered = (float)$orderItem->getQtyOrdered();
        $shipped = (float)$orderItem->getQtyShipped();

        $this->log('isItemFullyShipped()', ['ordered' => $ordered, 'shipped' => $shipped]);

        // Don't use === because value is sometimes returned as integer instead of float
        return $ordered == $shipped;
    }

    /**
     * Is SKU in skippable sku list for shippable products?
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     *
     * @return bool
     */
    private function shouldSkipOrderItem(
        OrderItemInterface $orderItem
    ) {
        return in_array($orderItem->getSku(), $this->_skippableSkus);
    }

    /**
     * Should we mark the order as 'complete' if all items have been fully shipped?
     *
     * @return bool
     */
    private function shouldMarkOrderAsComplete()
    {
        return $this->_helper->shouldMarkOrderAsComplete();
    }

    /**
     * Write to extension log
     *
     * @param string $message
     * @param array  $extra
     *
     * @return void
     */
    private function log(string $message, array $extra = [])
    {
        $this->_logger->info('Observer/SalesOrderShipmentAfter - ' . $message, $extra);
    }
}
