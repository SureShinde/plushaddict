<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Block\Adminhtml\Offer\Edit\Tab;

class Basic extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	protected $objectManager;

	protected $_wysiwygConfig;
	
    /**
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
	    \Magento\Framework\ObjectManagerInterface $objectManager,
	    \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, 
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
		$this->objectManager = $objectManager;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }
		
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm(){
		$model = $this->_coreRegistry->registry('offer_data');
		$form = $this->_formFactory->create();
		$fieldset = $form->addFieldset('offer_form', array('legend'=>__('Offer')));
		
		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array('name' => 'id'));
		}
						 
		$fieldset->addField('title', 'text', array(
			'label'     => __('Title'),
			'class'     => 'required-entry',
			'required'  => true,
			'style'     => 'width:500px;',
			'name'      => 'title',
		));

		$fieldset->addField('image_path', 'image', array(
			'name'   => 'image_path',
			'label'  => __('Image Path'),
			'title'  => __('Image Path'),
				
		));
		
		$fieldset->addField('description', 'editor', array(
			'label'     =>__('Description'),
		    'config'    => $this->_wysiwygConfig->getConfig(),
            'wysiwyg'   => true,
			'style'     => 'width:500px;',
			'name'      => 'description',
		));

		$fieldset->addField('caption', 'text', array(
			'label'     =>__('Caption'),
			'style'     => 'width:500px;',
			'name'      => 'caption',
		));

		$fieldset->addField('url', 'text', array(
			'label'     =>__('URL'),
			'style'     => 'width:500px;',
			'name'      => 'url',
		));

		$fieldset->addField('offer_type', 'select', array(
			'label'     =>__('Offer Type'),
			'name'      => 'offer_type',
			'values'    => array(
			  array(
				  'value'     => 'general',
				  'label'     => __('General'),
			  ),

			  array(
				  'value'     => 'blackfriday',
				  'label'     => __('Black Friday'),
			  ),

			 
			),
		));

		$fieldset->addField('sortorder', 'text', array(
			'label'     =>__('Sortorder'),
			'style'     => 'width:500px;',
			'name'      => 'sortorder',
		));

		$fieldset->addField('start_date', 'date', array(
			'name'   => 'start_date',
			'label'  => __('Start Date'),
			'title'  => __('Start Date'),
			'date_format' => 'yyyy-mm-dd',
			'shows_date' => true,
					  
		));

		$fieldset->addField('end_date', 'date', array(
			'name'   => 'end_date',
			'label'  => __('End Date'),
			'title'  => __('End Date'),
			'date_format' => 'yyyy-mm-dd',
			'shows_date' => true,
			
		));

		
		
		$fieldset->addField('status', 'select', array(
			'label'     =>__('Status'),
			'name'      => 'status',
			'values'    => array(
			  array(
				  'value'     => 1,
				  'label'     => __('Enable'),
			  ),

			  array(
				  'value'     => 2,
				  'label'     => __('Disable'),
			  ),

			 
			),
		));

		
		$form->setValues($model->getData());
		$this->setForm($form);
		return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel(){
        return __('offer');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Offer');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
