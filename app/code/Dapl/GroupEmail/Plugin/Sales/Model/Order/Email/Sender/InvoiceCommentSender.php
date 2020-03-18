<?php
namespace Dapl\GroupEmail\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\InvoiceCommentSender as InvoiceCommentSenderEmail;
use Magento\Sales\Model\Order\Invoice;

class InvoiceCommentSender
{
	protected $helper;

	public function __construct(
		\Dapl\GroupEmail\Helper\Data $helper
	) {
	    $this->helper = $helper;
	}

    public function aroundSend(InvoiceCommentSenderEmail $subject, callable $proceed, Invoice $invoice, $notify = true, $comment = '')
    {
    	$order = $invoice->getOrder();
    	if($this->helper->getInvoiceGroups()) {
	    	if (in_array($order->getCustomerGroupId(), $this->helper->getInvoiceGroups()) || in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $this->helper->getInvoiceGroups())) {
	        	return;
	    	}
		}
        return $proceed($invoice, $notify, $comment);
    }
}
