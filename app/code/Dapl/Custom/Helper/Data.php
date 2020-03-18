<?php
 
namespace Dapl\Custom\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
 
class Data extends AbstractHelper
{
    protected $objectmanager;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
	    ) {	        
	        $this->_objectManager = $objectmanager;
	        parent::__construct($context);
    }


    public function getObjectMan(){
        return $this->_objectManager;
    }
}