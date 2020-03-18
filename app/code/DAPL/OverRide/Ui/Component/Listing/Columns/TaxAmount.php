<?php
namespace DAPL\OverRide\Ui\Component\Listing\Columns;

class TaxAmount extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $_orderFactory;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $components = [],
        array $data = []
    ){
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $orderId = $item['entity_id'];
                $order = $this->_orderFactory->create()->load($orderId);
                $item['tax_amount'] = $order->getTaxAmount();//Here you can do anything with actual data
            }
        }

        return $dataSource;
    }
}