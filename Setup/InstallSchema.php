<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_CashOnDelivery
 * @copyright  Copyright (c) 2016 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\CashOnDelivery\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    protected function _setupTable(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableName = $setup->getTable('msp_cashondelivery_table');

        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'msp_cashondelivery_table_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entry ID'
            )
            ->addColumn(
                'country',
                Table::TYPE_TEXT,
                255,
                [],
                'Country'
            )
            ->addColumn(
                'from_amount',
                Table::TYPE_DECIMAL,
                '10,4',
                [],
                'From amount'
            )
            ->addColumn(
                'fee',
                Table::TYPE_DECIMAL,
                '10,4',
                [],
                'Fee'
            )
            ->addColumn(
                'is_pct',
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Is fee percetage?'
            );

        $setup->getConnection()->createTable($table);
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_setupTable($setup, $context);

        $setup->endSetup();
    }
}
