<?php
namespace Dapl\GroupEmail\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order\Email\Sender\ShipmentSender as ShipmentSenderEmail;
use Magento\Sales\Model\Order\Shipment;

class ShipmentSender
{
	protected $helper;

	public function __construct(
		\Dapl\GroupEmail\Helper\Data $helper
	) {
	    $this->helper = $helper;
	}

    public function aroundSend(ShipmentSenderEmail $subject, callable $proceed, Shipment $shipment, $forceSyncMode = false)
    {
    	$order = $shipment->getOrder();
    	if($this->helper->getShipmentGroups()) {
	    	if (in_array($order->getCustomerGroupId(), $this->helper->getShipmentGroups()) || in_array(\Magento\Customer\Model\Group::CUST_GROUP_ALL, $this->helper->getShipmentGroups())) {
	        	return;
	    	}
		}
        return $proceed($shipment, $forceSyncMode);
    }
}
