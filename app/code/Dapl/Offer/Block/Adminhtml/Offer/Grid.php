<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Block\Adminhtml\Offer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Dapl\Offer\Model\OfferFactory
     */
    protected $offerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Dapl\Offer\Model\OfferFactory $offerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Dapl\Offer\Model\OfferFactory $offerFactory,
        array $data = []
    ) {
        $this->offerFactory = $offerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct(){
        parent::_construct();
        $this->setId('offerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection(){
        $collection = $this->offerFactory->create()->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns(){

        

        $this->addColumn("title", array(
            "header" =>__("Title"),
            "align" =>"right",
            "width" => "200px",
            "type" => "text",
            "index" => "title",
        ));

        $this->addColumn("image_path", array(
            "header" =>__("Image Path"),
            "type" =>   "text", 
            "index" =>  "image_path",
            'renderer'  => '\Dapl\Offer\Block\Adminhtml\Offer\Grid\Renderer\Image',
        ));

        

        $this->addColumn("caption", array(
            "header" =>__("Caption"),
            "align" =>"right",
            "width" => "200px",
            "type" => "text",
            "index" => "caption",
        ));

        

        $this->addColumn("sortorder", array(
            "header" =>__("Sortorder"),
            "align" =>"right",
            "width" => "200px",
            "type" => "text",
            "index" => "sortorder",
        ));

        $this->addColumn("start_date", array(
            "header" =>__("Start Date"),
            "type" =>   "date", 
            "index" =>  "start_date",
        ));

        $this->addColumn("end_date", array(
            "header" =>__("End Date"),
            "type" =>   "date", 
            "index" =>  "end_date",
        ));

        
        $this->addColumn("status", array(
            "header" =>__("Status"),
            "align" =>"right",
            "width" => "200px",
            "type" => "options",
            "index" => "status",
            'options' => array(1 => 'Enable',2 => 'Disable' ,)
        ));

        $this->addColumn(
            'action', [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'id',
                    ],
                    [
                        'caption' => __('Delete'),
                        'url' => ['base' => '*/*/delete'],
                        'field' => 'id',
                        'confirm' => __('Are you sure?')
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('offer/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl(){
        return $this->getUrl('offer/*/index', ['_current' => true]);
    }

    /**
     * @param \Henote\Offer\Model\Offer|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row){
        return $this->getUrl(
            'offer/*/edit',
            ['id' => $row->getId()]
        );
		
    }
}