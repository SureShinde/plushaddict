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
        //event need to update pos_order_before_submit
        $quoteData = $observer->getQuote();; 
        $quoteItems = $quoteData->getAllItems(); 
        foreach($quoteItems as $item){
            $qtyOrdered = $item->getQty();
            $this->logger->addInfo("Pos Item Id: ".$item->getItemId());
            if($qtyOrdered - floor($qtyOrdered) > 0):
                $this->logger->addInfo("Pos Item Id: ".$item->getItemId());
                $itemdetails = $quoteData->getItemById($item->getItemId());
                $itemdetails->setIsQtyDecimal(1);
                $itemdetails->save();
                $this->logger->addInfo("Pos is_qty_decimal: ".$itemdetails->getIsQtyDecimal()); 
            endif;    
        } 
    }
}    