<?php
namespace Dapl\GroupEmail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
	const XML_PATH_EMAIL_ENABLED = 'order/general/enable';

	const XML_PATH_EMAIL_ORDER_GROUPS = 'order/general/order_groups';

	const XML_PATH_EMAIL_SHIPMENT_GROUPS = 'order/general/shipment_groups';

	const XML_PATH_EMAIL_INVOICE_GROUPS = 'order/general/invoice_groups';

	const XML_PATH_EMAIL_CREDITMEMO_GROUPS = 'order/general/creditmemo_groups';

	protected $scopeConfig;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->scopeConfig = $scopeConfig;
		parent::__construct($context);
	}
    
    public function isEnabled() {
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_ENABLED, $storeScope);
	}

	public function getOrderGroups() {
		if ($this->isEnabled()) {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$groups = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_ORDER_GROUPS, $storeScope);
			return explode(',', $groups);
		}

		return false;
	}

	public function getShipmentGroups() {
		if ($this->isEnabled()) {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$groups = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SHIPMENT_GROUPS, $storeScope);
			return explode(',', $groups);
		}

		return false;
	}

	public function getInvoiceGroups() {
		if ($this->isEnabled()) {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$groups = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_INVOICE_GROUPS, $storeScope);
			return explode(',', $groups);
		}

		return false;
	}

	public function getCreditmemoGroups() {
		if ($this->isEnabled()) {
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			$groups = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_CREDITMEMO_GROUPS, $storeScope);
			return explode(',', $groups);
		}

		return false;
	}
}