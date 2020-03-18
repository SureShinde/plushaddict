<?php
namespace Dapreport\Wholesalereport\Model;

class Wholesalereport extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dapreport\Wholesalereport\Model\ResourceModel\Wholesalereport');
    }
}
?>