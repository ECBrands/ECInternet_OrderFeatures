<?php
/**
 * Copyright (C) EC Brands Corporation - All Rights Reserved
 * Contact Licensing@ECInternet.com for use guidelines
 */
declare(strict_types=1);

namespace ECInternet\OrderFeatures\Block\Adminhtml\Erpterms\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Adminhtml Edit Erpterms DeleteButton Block
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getERPTermId()) {
            $data = [
                'label'      => __('Delete'),
                'on_click'   => 'deleteConfirm(\''
                    . __('Are you sure you want to delete this ERPTerm?')
                    . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20
            ];
        }

        return $data;
    }

    /**
     * Get URL for delete action
     *
     * @return string
     */
    private function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getERPTermId()]);
    }
}
