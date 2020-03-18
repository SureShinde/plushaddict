<?php
namespace Dapl\GroupEmail\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\CreditmemoCommentSender as CreditmemoCommentSenderEmail;
use Magento\Sales\Model\Order\Creditmemo;

class CreditmemoCommentSender
{
	protected $helper;

	public function __construct(
		\Dapl\GroupEmail\Helper\Data $helper
	) {
	    $this->helper = $helper;
	}

    public function aroundSend(CreditmemoCommentSenderEmail $subject, callable $proceed, Creditmemo $creditmemo, $notify = true, $comment = '')
    {
    	$order = $creditmemo->getOrder();
    	if($this->helper->getCreditmemoGroups()) {
	    	if (in_array($order->getCustomerGroupId(), $this->helper->getCreditmemoGroups()) || in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $this->helper->getCreditmemoGroups())) {
	        	return;
	    	}
		}
        return $proceed($creditmemo, $notify, $comment);
    }
}
