<?php
namespace DAPL\Schedulejob\Observer;
 
use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

 
class SendDisPatchMail implements ObserverInterface
{
    
    protected $logger;

    protected $orderRepository;

    protected $shipmentRepository;

    protected $convertOrder;    

    private $_objectManager;
   
    public function __construct(
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Framework\ObjectManagerInterface $objectmanager
        )
    {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->convertOrder = $convertOrder;
        $this->_objectManager = $objectmanager;
    }

    public function createShipmentFromOrderId(int $orderId)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
 
        if (!$order->canShip()) {
            throw new LocalizedException(__('Order cannot be shipped.'));
        }
 
        // Create the shipment:
        $shipment = $this->convertOrder->toShipment($order);
 
        // Add items to shipment:
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
 
            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }
 
        // Register the shipment:
        $shipment->register();
 
        try {
            $this->shipmentRepository->save($shipment);
            $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
 
        return;
    }
 
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $this->logger->addInfo("order state: ".$order->getState());
        if($order->getStatus()=='complete_dispatchedcomplete'):           
            $this->createShipmentFromOrderId($order->getId());        
            $this->logger->addInfo("Dispatched mail sent successfully.");
        endif;
    }
}