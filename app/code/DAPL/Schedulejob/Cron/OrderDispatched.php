<?php
/**
 * A Magento 2 module named DAPL/Schedulejob
 * Copyright (C) 2017 DAPL2018
 * 
 * This file included in DAPL/Schedulejob is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace DAPL\Schedulejob\Cron;

class OrderDispatched
{

    protected $logger;

    protected $_orderCollectionFactory;

    protected $_orderFactory;




    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(

        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory

        )
    {
        $this->logger = $logger;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
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
