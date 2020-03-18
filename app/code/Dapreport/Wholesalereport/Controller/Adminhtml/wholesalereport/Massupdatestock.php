<?php
namespace Dapreport\Wholesalereport\Controller\Adminhtml\wholesalereport;

use Magento\Backend\App\Action;

class Massupdatestock extends \Magento\Backend\App\Action
{
    /**
     * Update blog post(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
	

   

	
    public function execute()
    {
		
	//$checkid = $this->getRequest()->getParams();
	//echo '<pre>';print_r($checkid);exit;
		
	$itemIds = $this->getRequest()->getParam('wholesalereport');
	
	$stockInputCustom = $this->getRequest()->getParam('stockInputCustom');
		
	$reorderInputCustom = $this->getRequest()->getParam('reorderInputCustom');
	
	$qtyOffsetCustom = $this->getRequest()->getParam('qtyOffsetCustom');
	
	
	$deconstock = json_decode($stockInputCustom,true);
	
	$deconreorder = json_decode($reorderInputCustom,true);
	
	$deconqtyOffset = json_decode($qtyOffsetCustom,true);

	 
		///echo '<pre>';print_r($deconstock);exit;
		
		
        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                
			foreach ($itemIds as $postId) {
					
			  $post = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($postId);
			  $stockObj = $this->_objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface');
			 
			  $productStockObj = $stockObj->getStockItem($postId);
				
	    
		
			foreach($deconqtyOffset as  $_deconqtyOffset){
				$post->setQtyOffset($_deconqtyOffset);
			}
			
			foreach($deconreorder as $_deconreorder){
				$post->setReorderQuantity($_deconreorder);
			}
			
			foreach($deconstock as $_deconstock)
			{
				$productStockObj->setQty($_deconstock);
			}
			$post->save();
			//echo '<pre>';print_r($productStockObj->getData());exit;
			$stockObj->updateStockItemBySku($post->getSku(), $productStockObj);
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