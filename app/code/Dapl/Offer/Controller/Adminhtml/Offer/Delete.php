<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Controller\Adminhtml\Offer;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		try {
				$model = $this->_objectManager->get('Dapl\Offer\Model\Offer')->load($id);
				$model->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
	    $this->_redirect('*/*/');
    }
}