<?php
/**
 * Copyright © 2015 DAPL . All rights reserved.
 */
namespace DAPL\StockControl\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const FAT_QUARTER_LENGTH_OF_CUT = 0.5;
	const FAT_QUARTER_QUANTITY_OF_CUT = 2;
	
	const NAPPY_CUT_LENGTH_OF_CUT = 0.5;
	const NAPPY_CUT_QUANTITY_OF_CUT = 3;
	
	const LARGE_NAPPY_CUT_LENGTH_OF_CUT = 0.75;
	const LARGE_NAPPY_CUT_QUANTITY_OF_CUT = 3;
	
	const BLANKET_CUT_LENGTH_OF_CUT = 1;
	const BLANKET_CUT_QUANTITY_OF_CUT = 2;
	
	const SQUARE_75CM_LENGTH_OF_CUT = 0.75;
	const SQUARE_75CM_QUANTITY_OF_CUT = 2;
	
	const SQUARE_1M_LENGTH_OF_CUT = 1;
	const SQUARE_1M_QUANTITY_OF_CUT = 1;

	protected $stockItem;
	protected $productLinks;
	protected $productloader;
	protected $stockRegistry; 

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockItem
     * @param \Magento\GroupedProduct\Model\ResourceModel\Product\Link $catalogProductLink
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\CatalogInventory\Api\StockStateInterface $stockItem,
		\Magento\GroupedProduct\Model\ResourceModel\Product\Link $catalogProductLink,
		\Magento\Catalog\Model\ProductFactory $productloader,
		\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
	) {
		parent::__construct($context);
		$this->stockItem = $stockItem;
		$this->productLinks = $catalogProductLink;
		$this->productloader = $productloader;
		$this->stockRegistry = $stockRegistry;
	}


	private function getLengthOfCut($cut_type){
		$LC = 0;
		switch ($cut_type){
			case 64:
				$LC = self::FAT_QUARTER_LENGTH_OF_CUT;
				break;
			case 63:
				$LC = self::NAPPY_CUT_LENGTH_OF_CUT;
				break;	
			case 62:
				$LC = self::LARGE_NAPPY_CUT_LENGTH_OF_CUT;
				break;	
			case 61:
				$LC = self::BLANKET_CUT_LENGTH_OF_CUT;
				break;
			case 66:
				$LC = self::SQUARE_75CM_LENGTH_OF_CUT;
				break;
			case 65:
				$LC = self::SQUARE_1M_LENGTH_OF_CUT;
				break;
		}
		return $LC;
	}
		
	private function getQuantityOfCut($cut_type){
		$QC = 0;
		switch ($cut_type){
			case 64:
				$QC = self::FAT_QUARTER_QUANTITY_OF_CUT;
				break;
			case 63:
				$QC = self::NAPPY_CUT_QUANTITY_OF_CUT;
				break;	
			case 62:
				$QC = self::LARGE_NAPPY_CUT_QUANTITY_OF_CUT;
				break;	
			case 61:
				$QC = self::BLANKET_CUT_QUANTITY_OF_CUT;
				break;
			case 66:
				$QC = self::SQUARE_75CM_QUANTITY_OF_CUT;
				break;
			case 65:
				$QC = self::SQUARE_1M_QUANTITY_OF_CUT;
				break;
		}
		return $QC;
	}

	private function recalculateCutStock($simple, $qty_of_metre){	
		$cut_metre_grouped = $simple->getAttributeText('cut_metre');
		$cut_type_grouped = $simple->getCutType();
		$length_of_cut = $this->getLengthOfCut($cut_type_grouped);
		$quantity_of_cut = $this->getQuantityOfCut($cut_type_grouped);
		
		// Only recalculate if a type of Cut (ignore metre)				 
		if ($cut_metre_grouped == 'Cut'){
			//load stock info for product
			$simpleproductId = $simple->getId();	
			$simplewebsiteId = $simple->getStore()->getWebsiteId();		
			//load stock info for product
			$old_stock_qty=  $this->stockItem->getStockQty($simpleproductId, $simplewebsiteId);                    
			$new_stock_qty = ( floor($qty_of_metre / $length_of_cut)* $quantity_of_cut ) + ($old_stock_qty % $quantity_of_cut); 

			$stockItemObj = $this->stockRegistry->getStockItem($simpleproductId,$simplewebsiteId);		    														
			$stockItemObj->setData('product_id', $simple->getId());
			$stockItemObj->setData('stock_id', 1);
			$stockItemObj->setData('qty', $new_stock_qty );
			if ($new_stock_qty > 0) $stockItemObj->setData('is_in_stock', 1 );                 
			$this->stockRegistry->updateStockItemBySku($simple->getSku(), $stockItemObj);				
		}				
	}

	public function processSavedProduct($product) {
			$productId = $product->getId();	
			$websiteId = $product->getStore()->getWebsiteId();		
			//load stock info for product
			$actualStockMetre =  $this->stockItem->getStockQty($productId, $websiteId);	
			
			//setup attributes and constans for atributes of cuts  
			$cut_metre = $product->getAttributeText('cut_metre');   	
							
			//if edited product has Metre atriubute
			if($cut_metre == 'Metre')
			{
			
				//load grouped product from parent IDs
				$groupId = $this->productLinks->getParentIdsByChild($productId, \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED);
				$groupID = '';				
				if(isset($groupId[0])) 
				{
					$groupID = $groupId[0];
				} else {
					return;
				}	 
				// Load the products in parent group
				$associatedProducts = $this->productLinks->getChildrenIds($groupID,\Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED);
				$assoIds =	$associatedProducts[\Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED];
				//check if prod is cut the recalculate stock
				foreach ($assoIds as $id)
				{
					$productObj=$this->productloader->create()->load($id);
					$this->recalculateCutStock($productObj, $actualStockMetre);								
				}
			}
		}
}