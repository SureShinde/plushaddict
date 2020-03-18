<?php
namespace Dapreport\Wholesalereport\Model\ResourceModel;

class Wholesalereport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('main_table', 'id');
    }
}
?>