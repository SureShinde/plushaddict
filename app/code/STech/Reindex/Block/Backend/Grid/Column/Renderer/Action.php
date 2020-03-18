<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace STech\Reindex\Block\Backend\Grid\Column\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render indexer status
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $class = 'reindex-action';
        $text = 'Reindex Data';
        return '<a href="'.$this->getUrl('reindex/indexer/process/', array('indexer_id' => $this->_getValue($row))).'" class="' . $class . '"><span>' . $text . '</span></a>';
    }
}
