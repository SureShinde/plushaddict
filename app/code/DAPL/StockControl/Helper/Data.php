<?php
/**
 * Copyright © 2015 DAPL . All rights reserved.
 */
namespace DAPL\StockControl\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $current_metre_stock = 0;

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
	protected $_orderFactory;
	protected $logger;
	protected $_objectManager;

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockItem
     * @param \Magento\GroupedProduct\Model\ResourceModel\Product\Link $catalogProductLink
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Psr\Log\LoggerInterface $logger
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\CatalogInventory\Api\StockStateInterface $stockItem,
		\Magento\GroupedProduct\Model\ResourceModel\Product\Link $catalogProductLink,
		\Magento\Catalog\Model\ProductFactory $productloader,
		\Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Psr\Log\LoggerInterface $logger
	) {
		parent::__construct($context);
		$this->stockItem = $stockItem;
		$this->productLinks = $catalogProductLink;
		$this->productloader = $productloader;
		$this->stockRegistry = $stockRegistry;
		$this->_orderFactory = $orderFactory;
		$this->logger = $logger;
		$this->_objectManager = $objectmanager;
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
		if ($cut_metre_grouped == 'Cut' && $length_of_cut > 0){
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
			try {
		        $this->stockRegistry->updateStockItemBySku($simple->getSku(), $stockItemObj);
		    } catch (\Exception $e) {
		    }            
						
		}			
	}

	private function recalculateMetreStock($cut_product, $metre_product, $purchased_qty){
		
		$cut_type = $cut_product->getCutType();
		
		$length_of_cut = $this->getLengthOfCut($cut_type);
		$quantity_of_cut = $this->getQuantityOfCut($cut_type);

		$cutproductId = $cut_product->getId();	
		$cutwebsiteId = $cut_product->getStore()->getWebsiteId();		
		//load stock info for product
		$stock_cut_product=  $this->stockItem->getStockQty($cutproductId, $cutwebsiteId); 

		$metreproductId = $metre_product->getId();	
		$metrewebsiteId = $metre_product->getStore()->getWebsiteId();

		$old_stock_metre = $this->stockItem->getStockQty($metreproductId, $metrewebsiteId); 

		$new_stock_metre = $old_stock_metre - (ceil($purchased_qty/$quantity_of_cut)*$length_of_cut);
		$this->current_metre_stock = $new_stock_metre;
		$this->logger->addInfo("current metre_product quantity1: ".$new_stock_metre);

		$stockItemObj = $this->stockRegistry->getStockItem($metreproductId,$metrewebsiteId);		    														
		$stockItemObj->setData('product_id', $metreproductId);
		$stockItemObj->setData('stock_id', 1);
		$stockItemObj->setData('qty', $new_stock_metre);
		if ($new_stock_metre > 0) $stockItemObj->setData('is_in_stock', 1 );                 
		$this->stockRegistry->updateStockItemBySku($metre_product->getSku(), $stockItemObj);												
	}

	public function processSavedProduct($product,$actualStockMetre) {
			$productId = $product->getId();
			$websiteId = $product->getStore()->getWebsiteId();		
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
				//check if prod is cut the recalculate stock;
				foreach ($assoIds as $id)
				{
					$productObj=$this->productloader->create()->load($id);
					$this->recalculateCutStock($productObj, $actualStockMetre);							
				}
			}
	}

	public function processOrderForStockControl($orderId){
			$order = $this->_orderFactory->create()->load($orderId);
			//$this->logger->addInfo("Order Id: ".$order->getId());
			$metre_product_ids = array();
			$metre_product_qty = array();
			
			// Loop through the items in the order
			foreach ($order->getAllItems() as $item)
			{
				// Load the product
				$product=$this->productloader->create()->load($item->getProductId());
                $product_id = $product->getId();
				
				// Get the cut_metre attribute text
				$cut_or_metre = $product->getAttributeText('cut_metre');

				// Only process cuts (metre products just get added to the array for post processing
				if ($cut_or_metre === 'Cut')
				{
					//Get the parent grouped product
					$group_id = $this->productLinks->getParentIdsByChild($product_id, \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED);					
					// Move on to next product if not part of a group
					if(isset($group_id[0]))
					{ $group_id = $group_id[0]; }
					else
					{ continue; }
										
					// Get other children of parent looking for metre

				  // Load the products in parent group
					$associatedProducts = $this->productLinks->getChildrenIds($group_id,\Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED);
					$assoIds =	$associatedProducts[\Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED];
					//check if prod is cut the recalculate stock
					foreach ($assoIds as $id)
					{
						$child_product=$this->productloader->create()->load($id);
						$cut_metre_other = $child_product->getAttributeText('cut_metre');
						// If metre product then recalculate stock level based on cut product new qty
						if ($cut_metre_other == 'Metre')
						{
							$this->recalculateMetreStock($product, $child_product, $item->getQtyOrdered());
							if (!in_array($child_product->getId(), $metre_product_ids))
							{
								$metre_product_ids[] = $child_product->getId();
								$this->logger->addInfo("current metre_product quantity2: ".$this->current_metre_stock);
								$metre_product_qty[$child_product->getId()] = $this->current_metre_stock;
							}
							break;
						} 
									
					}
					
				}
				else
				{
					if (!in_array($product_id, $metre_product_ids))
					{
						$metre_product_ids[] = $product_id;
					}
				}
			}
			
			// Loop through the metre products and recalculat sub cuts
			foreach ($metre_product_ids as $metre_product_id)
			{
			    $metre_product=$this->productloader->create()->load($metre_product_id);	
			    $metrewebsiteId = $metre_product->getStore()->getWebsiteId();
			    if(array_key_exists($metre_product_id,$metre_product_qty) && $metre_product_qty[$metre_product_id]>0){
			        $metre_product_stock_qty = $metre_product_qty[$metre_product_id];  
			    }
			    else{
    				$metre_product_stock_qty = $this->stockItem->getStockQty($metre_product_id, $metrewebsiteId);
			    }
			    $this->logger->addInfo("current metre_product quantity: ".$metre_product_stock_qty);
			    //if($metre_product_stock_qty>0){
					$this->processSavedProduct($metre_product,$metre_product_stock_qty);
			    //}
			}
	}
}