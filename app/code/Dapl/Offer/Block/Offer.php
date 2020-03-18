<?php
namespace Dapl\Offer\Block;


class Offer extends \Magento\Framework\View\Element\Template
{  
  /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry,
    * @param array $data
    */
   public function __construct(
      \Magento\Backend\Block\Template\Context $context,
      \Magento\Framework\Registry $registry,
      \Dapl\Offer\Model\ResourceModel\Offer\Collection $collection,
      
      array $data = []
   ) {
      
      $this->_registry = $registry;
      $this->collection = $collection;
      parent::__construct($context, $data);
   }


    public function getCurrentProduct()
    {      
        return $this->_registry->registry('current_product');
    }  
    
    
    public function getmyCollection()
    {      
        $coll = $this->collection
                        ->addFieldToFilter('start_date', ['to' => date('Y-m-d')])
                        ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
                        ->addFieldToFilter('offer_type','blackfriday')
                        ->addFieldToFilter('status',1);
        return $coll;
    }  

    public function getGeneralOfferCollection()
    {      
        $coll = $this->collection
                        ->addFieldToFilter('start_date', ['to' => date('Y-m-d')])
                        ->addFieldToFilter('end_date', ['gteq' => date('Y-m-d')])
                        ->addFieldToFilter('offer_type','general')
                        ->addFieldToFilter('status',1);
        return $coll;
    }

}
?>