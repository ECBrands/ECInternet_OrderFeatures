<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Block\Adminhtml\Erpterms\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Adminhtml Edit Erpterms SaveButton Block
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save ERP Term'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order'     => 90
        ];
    }
}
