<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Block\Adminhtml\Erpterms\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Adminhtml Edit Erpterms GenericButton Block
 */
class GenericButton
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * GenericButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_registry   = $registry;
    }

    /**
     * Return the Erpterms Id.
     *
     * @return int|null
     */
    public function getERPTermId()
    {
        return $this->_registry->registry('current_erpterm_id');
    }

    /**
     * Generate url by route and parameters.
     *
     * @param string $route
     * @param array  $params
     *
     * @return string
     */
    public function getUrl(string $route = '', array $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
}
