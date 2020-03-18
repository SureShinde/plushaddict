<?php
namespace Dapl\GroupEmail\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderSenderEmail;
use Magento\Sales\Model\Order;

class OrderSender
{
	protected $helper;

	public function __construct(
		\Dapl\GroupEmail\Helper\Data $helper
	) {
	    $this->helper = $helper;
	}

    public function aroundSend(OrderSenderEmail $subject, callable $proceed, Order $order, $forceSyncMode = false)
    {
    	if($this->helper->getOrderGroups()) {
	    	if (in_array($order->getCustomerGroupId(), $this->helper->getOrderGroups()) || in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $this->helper->getOrderGroups())) {
	        	return;
	    	}
		}
        return $proceed($order, $forceSyncMode);
    }
}
