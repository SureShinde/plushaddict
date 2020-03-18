<?php
namespace DAPL\OverRide\Observer;
 
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

 
class PosOrderDecimalQuantityUpdate implements ObserverInterface
{
    
    protected $logger; 

    private $_objectManager;

    protected $_orderFactory;
    
    protected $_orderItemFactory;
    
    protected $_quoteItemFactory;
    
 
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
        )
    {
        $this->logger = $logger;
        $this->_objectManager = $objectmanager;
        $this->_orderFactory = $orderFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_quoteItemFactory = $quoteItemFactory;
    }
 
    public function execute(Observer $observer)
    {
        $orderData = $observer->getOrder();
        $orderId = $orderData->getEntityId(); 
        $this->logger->addInfo("Pos Order Id: ".$orderId); 
        $order = $this->_orderFactory->create()->load($orderId);  
        $orderItems = $order->getAllItems(); 
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
        foreach($orderItems as $item){
            $qtyOrdered = $item->getQtyOrdered();
            $this->logger->addInfo("Pos Item Id: ".$item->getItemId());
            if($qtyOrdered - floor($qtyOrdered) > 0):
                $this->logger->addInfo("Pos Item Id: ".$item->getItemId());
                $tableName = $resource->getTableName('quote_item');
                $sql = "UPDATE " . $tableName . " SET is_qty_decimal = 1 WHERE item_id = " . $item->getQuoteItemId();
                $connection->query($sql);
                //$this->logger->addInfo("Pos Quote Item Id: ".$item->getQuoteItemId());
                //$quoteItem = $this->_quoteItemFactory->create()->load($item->getQuoteItemId());
                //$quoteItem->setIsQtyDecimal(1);
                //$quoteItem->save();
                //$itemdetails = $order->getItemById($item->getItemId());
                //$itemdetails->setIsQtyDecimal(1);
                //$itemdetails->save();
                $tableName = $resource->getTableName('sales_order_item');
                $sql = "UPDATE " . $tableName . " SET is_qty_decimal = 1 WHERE item_id = " . $item->getItemId();
                $connection->query($sql);
                $upitemdetails = $this->_orderItemFactory->create()->load($item->getItemId());
                $this->logger->addInfo("Pos is_qty_decimal: ".$upitemdetails->getIsQtyDecimal()); 
            endif;    
        } 
    }
}    