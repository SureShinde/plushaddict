<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Block\Adminhtml\Offer\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct(){
		    parent::_construct();
        $this->setId('offer_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Offer Information'));
    }
	
   protected function _beforeToHtml(){
        $this->addTab('form_section', array(
          'label'     => __('Offer Information'),
          'title'     => __('Offer Information'),
          'content'   => $this->getLayout()->createBlock('Dapl\Offer\Block\Adminhtml\Offer\Edit\Tab\Basic')->toHtml(),
        ));
        return parent::_beforeToHtml();
   }
}