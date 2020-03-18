<?php
namespace DAPL\OverRide\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{

	public function upgrade(SchemaSetupInterface $setup,ModuleContextInterface $context){
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.0.1') < 0) {

			// Get module table
			$tableName = $setup->getTable('amasty_rewards_rewards');

			// Check if the table already exists
			if ($setup->getConnection()->isTableExists($tableName) == true) {

				// Declare data
				$columns = [
					'order_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'unsigned' => true,
					'comment' => 'Order Id',
					],
					'rule_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'unsigned' => true,
					'comment' => 'Rule Id',
					],

				];
				$connection = $setup->getConnection();
				foreach ($columns as $name => $definition) {

					$connection->addColumn($tableName, $name, $definition);

				}

			 
			}

		}	 
		
		if (version_compare($context->getVersion(), '1.0.2') < 0) {

			// Get module table
			$tableName = $setup->getTable('sales_order_grid');

			// Check if the table already exists
			if ($setup->getConnection()->isTableExists($tableName) == true) {

				// Declare data
				$columns = [
					'amazon_order_id' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Amazon Order Id',
					]
				];
				$connection = $setup->getConnection();
				foreach ($columns as $name => $definition) {

					$connection->addColumn($tableName, $name, $definition);

				}

			 
			}

		}
		$setup->endSetup();
	}

}