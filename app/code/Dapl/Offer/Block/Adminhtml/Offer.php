<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Block\Adminhtml;

class Offer extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_offer';/*block grid.php directory*/
        $this->_blockGroup = 'Dapl_Offer';
        $this->_headerText = __('Offer');
        $this->_addButtonLabel = __('Add New Offer'); 
        parent::_construct();
        
    }
}

