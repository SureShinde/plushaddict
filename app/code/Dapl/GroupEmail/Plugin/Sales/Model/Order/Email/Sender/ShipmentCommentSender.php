<?php
namespace Dapl\GroupEmail\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\ShipmentCommentSender as ShipmentCommentSenderEmail;
use Magento\Sales\Model\Order\Shipment;

class ShipmentCommentSender
{
	protected $helper;

	public function __construct(
		\Dapl\GroupEmail\Helper\Data $helper
	) {
	    $this->helper = $helper;
	}

    public function aroundSend(ShipmentCommentSenderEmail $subject, callable $proceed, Shipment $shipment, $notify = true, $comment = '')
    {
    	$order = $shipment->getOrder();
    	if($this->helper->getShipmentGroups()) {
	    	if (in_array($order->getCustomerGroupId(), $this->helper->getShipmentGroups()) || in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $this->helper->getShipmentGroups())) {
	        	return;
	    	}
		}
        return $proceed($shipment, $notify, $comment);
    }
}
