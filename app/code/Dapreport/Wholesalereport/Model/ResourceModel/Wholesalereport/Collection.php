<?php

namespace Dapreport\Wholesalereport\Model\ResourceModel\Wholesalereport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dapreport\Wholesalereport\Model\Wholesalereport', 'Dapreport\Wholesalereport\Model\ResourceModel\Wholesalereport');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>