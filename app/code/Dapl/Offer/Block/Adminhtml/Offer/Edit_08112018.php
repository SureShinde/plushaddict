<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Block\Adminhtml\Offer;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Dapl_Offer';
        $this->_controller = 'adminhtml_offer';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save'));
        $this->removeButton('reset');

    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('offer_data')->getId()) {
            return __("Edit Offer '%1'", $this->escapeHtml($this->_coreRegistry->registry('offer_data')->getName()));
        } else {
            return __('New Offer');
        }
    }
}
