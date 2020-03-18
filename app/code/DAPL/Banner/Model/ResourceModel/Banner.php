<?php
/**
 * Copyright © 2015 DAPL. All rights reserved.
 */
namespace DAPL\Banner\Model\ResourceModel;

/**
 * Banner resource
 */
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('banner_banner', 'id');
    }

  
}
