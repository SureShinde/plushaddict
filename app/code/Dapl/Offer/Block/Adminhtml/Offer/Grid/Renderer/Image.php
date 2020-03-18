<?php
/**
 * @author Prasanta Hatui
 * @copyright Copyright © 2018 MallzApp. All rights reserved.
 * @package Henote_Mall
 */
namespace Dapl\Offer\Block\Adminhtml\Offer\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $objectManager;
  
    /**
     * @param \Magento\Backend\Block\Context $context
   * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
    \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
    $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * Render action
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
      $value = $row->getData($this->getColumn()->getIndex());
    return '<img src="'.$this->getMediaUrl().$value.'" width="90" />';
    }
  
  /**
     * return media url
     */
    public function getMediaUrl(){
    $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
      ->getStore()
      ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    return $media_dir;
     }  
}