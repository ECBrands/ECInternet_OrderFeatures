<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\ViewModel\Order;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use ECInternet\OrderFeatures\Helper\Data;

class View implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * View constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->_coreRegistry = $registry;
    }

    /**
     * Get 'po_number' value
     *
     * @return string|null
     */
    public function getPoNumber()
    {
        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $this->getOrder()) {
            return (string)$order->getData(Data::ATTRIBUTE_PO_NUMBER);
        }

        return null;
    }

    /**
     * Get 'erp_terms' value
     *
     * @return string|null
     */
    public function getTerm()
    {
        /** @var \Magento\Sales\Model\Order $order */
        if ($order = $this->getOrder()) {
            return (string)$order->getData(Data::ATTRIBUTE_ERP_TERMS);
        }

        return null;
    }

    /**
     * Get order from registry
     *
     * @return \Magento\Sales\Model\Order|bool
     */
    private function getOrder()
    {
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }

        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }

        return false;
    }
}
