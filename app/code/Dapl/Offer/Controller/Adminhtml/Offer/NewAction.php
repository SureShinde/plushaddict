<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Controller\Adminhtml\Offer;

class NewAction extends \Magento\Backend\App\Action
{
     public function execute()
    {
		$this->_forward('edit');
    }
}
