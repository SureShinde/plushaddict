<?php
namespace DAPL\OverRide\Observer;
 
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

 
class OrderPlaceAfter implements ObserverInterface
{
    
    protected $logger; 

    private $_objectManager;
 
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectmanager
        )
    {
        $this->logger = $logger;
        $this->_objectManager = $objectmanager;
    }
 
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();  
        $statusHistoryItem = $order->getStatusHistoryCollection()->getFirstItem();
        $comment = $statusHistoryItem->getComment();
        $this->logger->addInfo("Order Id: ".$order->getId());
        $this->logger->addInfo("Order Comment: ".$comment);
        $order->setData('bold_order_comment',$comment);
        $order->save();     
    }
}    