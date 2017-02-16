<?php
namespace MSP\CashOnDelivery\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected function upgradeTo110(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('msp_cashondelivery_table');
        $setup->getConnection()->addColumn($tableName, 'website', [
            'type' => Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'Website ID',
        ]);

        $setup->getConnection()->update($tableName, [
            'website' => '*',
        ]);
    }

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $this->upgradeTo110($setup);
        }

        $setup->endSetup();
    }
}
