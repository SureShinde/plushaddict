<?php
namespace Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Dapreport\Wholesalereport\Model\wholesalereportFactory
     */
    protected $_wholesalereportFactory;

    /**
     * @var \Dapreport\Wholesalereport\Model\Status
     */
    protected $_status;
	
	protected $_alloptions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dapreport\Wholesalereport\Model\wholesalereportFactory $wholesalereportFactory
     * @param \Dapreport\Wholesalereport\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product $productCollectionFactory,
        \Dapreport\Wholesalereport\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
		\Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport\wholesaleoptions $alloptions,
        array $data = []
    ) {
        $this->_wholesalereportFactory = $productCollectionFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
		$this->_alloptions = $alloptions;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
       
		$collection = $this->_wholesalereportFactory->getCollection();
		
		$collection->addAttributeToSelect('*')->addAttributeToFilter('reorder_quantity', array('gt' => 0));
		
		//$newcollection = $productattribute->getSelect()->joinleft('cataloginventory_stock_item','e.entity_id = cataloginventory_stock_item.product_id')->columns('cataloginventory_stock_item.*');
		
		$stockProductCllctn = $collection->joinField('qty','cataloginventory_stock_item','qty','product_id=entity_id',null,'left');
		
		$entityIds = [];
		$i = 0; 
		foreach($stockProductCllctn as $_collection)
		{
			
			if($_collection->getQty() <= $_collection->getReorderQuantity())
			{
				$entityIds[$i] = $_collection->getEntityId();
				$i++;
			}
			
		}
		
		$endCollection = $stockProductCllctn->addAttributeToFilter('entity_id', array('in'=> $entityIds));
		
		
		
        $this->setCollection($endCollection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
    
		
				$this->addColumn(
					'sku',
					[
						'header' => __('SKU'),
						'index' => 'sku',
					]
				);
				
				$this->addColumn(
					'wholesaler',
					[
						'header' => __('Wholesale'),
						'index' => 'wholesaler',
						'renderer'  => '\Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport\Wholesale',
						'type' => 'options',
                        'options' => $this->_alloptions->retrieveOptions(),
					]
				);
				
				$this->addColumn(
					'manufacturers_code',
					[
						'header' => __('Manufactures Code'),
						'index' => 'manufacturers_code',
					]
				);
				
				$this->addColumn(
					'name',
					[
						'header' => __('Name'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'price',
					[
						'header' => __('Price'),
						'index' => 'price',
					]
				);
				
				$this->addColumn(
					'qty',
					[
						'header' => __('Qty'),
						'index' => 'qty',
					]
				);
				
				$this->addColumn(
					'qty_offset',
					[
						'header' => __('Qty Offset'),
						'index' => 'qty_offset',
					]
				);
				
				$this->addColumn(
					'reorder_quantity',
					[
						'header' => __('Reorder quantity'),
						'index' => 'reorder_quantity',
						//'editable' => 'true',
					]
				);
				
				$this->addColumn(
					'cost',
					[
						'header' => __('Cost price'),
						'index' => 'cost',
					]
				);
				
				$this->addColumn(
					'reorder_po_number',
					[
						'header' => __('PO Number'),
						'index' => 'reorder_po_number',
					]
				);
				
				$this->addColumn(
					'warehouse_location',
					[
						'header' => __('Warehouse Location'),
						'index' => 'warehouse_location',
					]
				);
				
				$this->addColumn(
					'stock_input',
					[
						'header' => __('Stock Input'),
						'index' => 'stock_input',
						'renderer'  => '\Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport\Stockinput'
					]
				);
				
				$this->addColumn(
					'reorder_qty_input',
					[
						'header' => __('ReOrder Quantity Input'),
						'index' => 'reorder_qty_input',
						'renderer'  => '\Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport\Reorderinput'
					]
				);
				
				$this->addColumn(
					'qty_offset_input',
					[
						'header' => __('QTY OFFSet Input'),
						'index' => 'qty_offset_input',
						'renderer'  => '\Dapreport\Wholesalereport\Block\Adminhtml\Wholesalereport\Qtyoffset'
					]
				);
				

		   $this->addExportType($this->getUrl('wholesalereport/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('wholesalereport/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Dapreport_Wholesalereport::wholesalereport/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('wholesalereport');


        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'reorder_po_number',
            [
                'label' => __('Update Po Number'),
                'url' => $this->getUrl('wholesalereport/*/massponumber', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'reorder_po_number',
                        'type' => 'text',
                        'class' => 'required-entry',
                        'label' => __('Reorder Po Number:')
                    ]
                ]
            ]
        );
		
		
		$this->getMassactionBlock()->addItem(
            'update_stock',
            [
                'label' => __('Update Stock'),
                'url' => $this->getUrl('wholesalereport/*/massupdatestock', ['_current' => true]),
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('wholesalereport/*/index', ['_current' => true]);
    }

    /**
     * @param \Dapreport\Wholesalereport\Model\wholesalereport|\Magento\Framework\Object $row
     * @return string
     */
  

	

}