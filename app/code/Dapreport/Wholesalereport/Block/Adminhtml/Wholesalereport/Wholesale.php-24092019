<?php
namespace Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Object;
use Magento\Store\Model\StoreManagerInterface;

class Wholesale extends AbstractRenderer
{
   private $_storeManager;
   /**
    * @param \Magento\Backend\Block\Context $context
    * @param array $data
    */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
      }

    public function render(\Magento\Framework\DataObject $row)
    {
	
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $_product = $objectManager->get('Magento\Catalog\Model\Product')->load($row->getId());
	
	
	
	$attr = $_product->getResource()->getAttribute('wholesaler');
	if ($attr->usesSource()) {
	      return $optionText = $attr->getSource()->getOptionText($row->getWholesaler());
	}
       
            
        
    }
	
}