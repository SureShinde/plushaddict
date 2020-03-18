<?php
/**
 * Copyright © 2019 Digital Aptech PVT. LTD. All rights reserved.
 */

namespace Dapl\GroupChange\Observer;

use Magento\Framework\Event\ObserverInterface;

class GroupChange implements ObserverInterface
{
	const XML_PATH_GROUP_ENABLED = 'group/general/enable';

	const XML_PATH_GROUP_FROM = 'group/general/from_group';

	const XML_PATH_GROUP_TO = 'group/general/to_group';

	protected $customerRepository;

	protected $scopeConfig;

	public function __construct(
	    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
	    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
	    $this->customerRepository = $customerRepositoryInterface;
	    $this->scopeConfig = $scopeConfig;
	}

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	if ($this->isEnabled()) {
	        $customer = $observer->getEvent()->getCustomer();
	        if ($customer->getGroupId() == $this->getGroupFrom()) {
		        $customer = $this->customerRepository->getById($customer->getId());
		        $customer->setGroupId($this->getGroupTo());
		        $this->customerRepository->save($customer);
		    }
	    }
    }

    public function isEnabled()
    {
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->scopeConfig->getValue(self::XML_PATH_GROUP_ENABLED, $storeScope);
	}

	public function getGroupFrom()
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->scopeConfig->getValue(self::XML_PATH_GROUP_FROM, $storeScope);
	}

	public function getGroupTo()
	{
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->scopeConfig->getValue(self::XML_PATH_GROUP_TO, $storeScope);
	}
}
