<?php
namespace Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport;

class wholesaleoptions 
{
   private $_eavAttributeRepository;
   /**
    * @param \Magento\Backend\Block\Context $context
    * @param array $data
    */
	public function __construct(
	     \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository
	 ) {
	     $this->_eavAttributeRepository = $eavAttributeRepository; 
	 }

	public function retrieveOptions(){
	        $attributes = $this->_eavAttributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, 'wholesaler');
	        //$options = $attributes->getSource()->getAllOptions(false);
	          $options = array();
		foreach( $attributes->getSource()->getAllOptions(true) as $option ) {
		   $options[$option['value']] = $option['label'];
		}
		
		
		return $options;
	}
	
}