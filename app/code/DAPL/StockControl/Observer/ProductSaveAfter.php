<?php
namespace DAPL\StockControl\Observer;
 
use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use DAPL\StockControl\Helper\Data as StockControlHelper;

 
class ProductSaveAfter implements ObserverInterface
{
    
    protected $logger;

    protected $request;    

    private $_objectManager;

    private $stockcontrolhelper;
   
    public function __construct(
        LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        StockControlHelper $stockcontrolhelper
        )
    {
        $this->logger = $logger;
        $this->request = $request;
        $this->_objectManager = $objectmanager;
        $this->stockcontrolhelper = $stockcontrolhelper;
    }
 
    public function execute(Observer $observer)
    {       
        $productObj = $observer->getEvent()->getProduct();
        $post = $this->request->getPost('product');
        $stock = $post['quantity_and_stock_status'];
        $qty = $stock['qty'];
       /*if($productObj && $qty>0){
            $this->stockcontrolhelper->processSavedProduct($productObj,$qty);
        }*/
        
        if($productObj){
            $this->stockcontrolhelper->processSavedProduct($productObj,$qty);
        }
        
    }
}