<?php
/**
 * Copyright © 2015 DAPL . All rights reserved.
 */
namespace DAPL\Banner\Block;
use Magento\Framework\UrlFactory;
class BaseBlock extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \DAPL\Banner\Helper\Data
     */
	 protected $_devToolHelper;
	 
	 /**
     * @var \Magento\Framework\Url
     */
	 protected $_urlApp;
	 
	 /**
     * @var \DAPL\Banner\Model\Config
     */
    protected $_config;

    protected $_collectionFactory;

    /**
     * @param \DAPL\Banner\Block\Context $context
	 * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct( 
    	\DAPL\Banner\Block\Context $context,
    	\DAPL\Banner\Model\BannerFactory $collectionFactory
	)
    {
        $this->_devToolHelper = $context->getBannerHelper();
		$this->_config = $context->getConfig();
        $this->_urlApp=$context->getUrlFactory()->create();
        $this->_collectionFactory = $collectionFactory;
		parent::__construct($context);
	
    }
	
	/**
	 * Function for getting event details
	 * @return array
	 */
    public function getEventDetails()
    {
		return  $this->_devToolHelper->getEventDetails();
    }
	
	/**
     * Function for getting current url
	 * @return string
     */
	public function getCurrentUrl(){
		return $this->_urlApp->getCurrentUrl();
	}
	
	/**
     * Function for getting banner collection
	 * @return string
     */
	public function getBannerCollection(){
		$collection = $this->_collectionFactory->create();		
        return $collection->getCollection();;
	}

	/**
     * Function for getting controller url for given router path
	 * @param string $routePath
	 * @return string
     */
	public function getControllerUrl($routePath){
		
		return $this->_urlApp->getUrl($routePath);
	}
	
	/**
     * Function for getting current url
	 * @param string $path
	 * @return string
     */
	public function getConfigValue($path){
		return $this->_config->getCurrentStoreConfigValue($path);
	}
	
	/**
     * Function canShowBanner
	 * @return bool
     */
	public function canShowBanner(){
		$isEnabled=$this->getConfigValue('banner/module/is_enabled');
		if($isEnabled)
		{
			$allowedIps=$this->getConfigValue('banner/module/allowed_ip');
			 if(is_null($allowedIps)){
				return true;
			}
			else {
				$remoteIp=$_SERVER['REMOTE_ADDR'];
				if (strpos($allowedIps,$remoteIp) !== false) {
					return true;
				}
			}
		}
		return false;
	}
	
}
