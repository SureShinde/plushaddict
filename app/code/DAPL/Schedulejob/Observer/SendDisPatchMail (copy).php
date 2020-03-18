<?php
namespace DAPL\Schedulejob\Observer;
 
use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
 
class SendDisPatchMail implements ObserverInterface
{
    
    protected $logger;

    protected $_orderCollectionFactory;

    protected $_orderFactory;
 
   
    public function __construct(
        LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
        )
    {
        $this->logger = $logger;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderFactory = $orderFactory;
    }
 
    public function execute(Observer $observer)
    {
        $order_collection=$this->_orderCollectionFactory->create()->addFieldToFilter('status',['in' => array('awaiting_postal_collection')])->setOrder('created_at','desc');
        if($order_collection->count()>0):    
            $status = 'complete_dispatchedcomplete';
            foreach($order_collection as $each)
            {
                $order = $this->_orderFactory->create()->load($each->getId());
                $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
                $order->setStatus($status);
                $order->addStatusToHistory($order->getStatus(), 'Order Dispatched Complete Successfully');
                $order->save();
                $this->logger->addInfo("Order-".$each->getId()." is executed.");
            }
        endif; 
               
        $this->logger->addInfo("Cronjob OrderDispatched is executed.");
    }
}