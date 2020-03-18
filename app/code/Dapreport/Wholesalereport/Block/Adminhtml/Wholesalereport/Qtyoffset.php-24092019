<?php
namespace Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Object;
use Magento\Store\Model\StoreManagerInterface;

class Qtyoffset extends AbstractRenderer
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
       

            return "<input type='text' name='qty_offset_custom' class='qtyOffsetCustom' data-pid='".$row->getId()."'/>";
        
    }
	
}