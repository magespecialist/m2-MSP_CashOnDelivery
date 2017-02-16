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
namespace MSP\CashOnDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

class CashondeliveryTable extends AbstractDb
{
    protected $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('msp_cashondelivery_table', 'msp_cashondelivery_table_id');
    }

    /**
     * Get fee from table
     *
     * @param double $amount
     * @param string $country
     * @return double
     */
    public function getFee($amount, $country)
    {
        $table = $this->getMainTable();

        $currentWebsite = $this->storeManager->getWebsite()->getCode();

        $connection = $this->getConnection();
        $qry = $connection->select()
            ->from($table, '*')
            ->where(
                '('
                    .'country = '.$connection->quote($country).' OR '
                    .'country = '.$connection->quote('*')
                .') AND ('
                    .'from_amount < '.doubleval($amount) . ' AND '
                    .'('
                        .'website = '.$connection->quote($currentWebsite).' OR '
                        .'website = '.$connection->quote('*')
                    .')'
                .')'
            )
            ->order('from_amount desc')
            ->order(new \Zend_Db_Expr("website = '*'"))
            ->order(new \Zend_Db_Expr("country = '*'"))
            ->limit(1);

        $row = $connection->fetchRow($qry);
        if ($row) {
            if ($row['is_pct']) {
                return doubleval($amount) / 100.0 * doubleval($row['fee']);
            }

            return doubleval($row['fee']);
        }

        return 0;
    }

    /**
     * Get table as array
     *
     * @return array
     */
    public function getTableAsArray()
    {
        $table = $this->getMainTable();

        $connection = $this->getConnection();
        $qry = $connection->select()
            ->from($table, '*');

        // @codingStandardsIgnoreStart
        return $connection->fetchAll($qry);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Populate table from array
     *
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();

        $table = $this->getMainTable();

        $connection->delete($table);
        foreach ($data as $dataRow) {
            $connection->insert($table, $dataRow);
        }

        $connection->commit();
    }

    /**
     * Get rows count
     *
     * @return int
     */
    public function getRowsCount()
    {
        return count($this->getTableAsArray());
    }
}
