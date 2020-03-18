<?php
/**
 * @author DAPL Team
 * @copyright Copyright © 2018 DAPL. All rights reserved.
 * @package Dapl_Offer
 */
namespace Dapl\Offer\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_offer = $setup->getConnection()->newTable($setup->getTable('dapl_offer'));
       
        $table_offer->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,'auto_increment' => true),
            'ID'
        );
                        
        $table_offer->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            512,
            [],
            'Title'
        );
               
        $table_offer->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '',
            [],
            'Description'
        );

        $table_offer->addColumn(
            'image_path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'image_path'
        );

        $table_offer->addColumn(
            'caption',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Caption'
        );

        $table_offer->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            512,
            [],
            'URL'
        );

        $table_offer->addColumn(
            'sortorder',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Sortorder'
        );

        $table_offer->addColumn(
            'offer_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            56,
            [],
            'Offer Type'
        );

		$table_offer->addColumn(
            'start_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => NULL],
            'Start Date'
        );
		
		$table_offer->addColumn(
            'end_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' =>NULL],
            'End Date'
        );    
        $table_offer->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'status'
        );
	         
        $setup->getConnection()->createTable($table_offer);

        $setup->endSetup();
    }
}
