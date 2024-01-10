<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Block\Adminhtml\Erpterms\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Adminhtml Edit Erpterms BackButton Block
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class'      => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    private function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
