<?php
namespace Dapreport\Wholesalereport\Controller\Adminhtml\wholesalereport;

use Magento\Backend\App\Action;

class Massponumber extends \Magento\Backend\App\Action
{
    /**
     * Update blog post(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $itemIds = $this->getRequest()->getParam('wholesalereport');
        
        
       
        
        //echo '<pre>';echo ($ponumber);exit;
        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                 $ponumber = $this->getRequest()->getParam('reorder_po_number');
                foreach ($itemIds as $postId) {
                    $post = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($postId);
		
                    $post->setReorderPoNumber($ponumber)->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($itemIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('wholesalereport/*/index');
    }

}