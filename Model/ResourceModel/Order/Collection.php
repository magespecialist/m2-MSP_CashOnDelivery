<?php

namespace MSP\CashOnDelivery\Model\ResourceModel\Order;

use Magento\Framework\DB\Select;

/**
 * Custom order report collection so the lifetime total sales and revenue do not include the cash on delivery fee
 *
 * @author      Antonis Galanis <ant.galanis@mycenter.gr>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
    /**
     * Is live
     *
     * @var bool
     */
    protected $_isLive = false;

    /**
     * Sales amount expression
     *
     * @var string
     */
    protected $_salesAmountExpression;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager instance
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Locale date instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Order config instance
     *
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * Reports order factory
     *
     * @var \Magento\Sales\Model\ResourceModel\Report\OrderFactory
     */
    protected $_reportOrderFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Helper $coreResourceHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Sales\Model\ResourceModel\Report\OrderFactory $reportOrderFactory
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Report\OrderFactory $reportOrderFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $coreResourceHelper,
            $scopeConfig,
            $storeManager,
            $localeDate,
            $orderConfig,
            $reportOrderFactory,
            $connection,
            $resource
        );
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->_orderConfig = $orderConfig;
        $this->_reportOrderFactory = $reportOrderFactory;
    }

    /**
     * Get sales amount expression
     * Here i added the last to arguments to the expression
     * to subtract the cash on deliry and the tax amount form the total revenue
     *
     * @return string
     */
    protected function _getSalesAmountExpression()
    {
        if (null === $this->_salesAmountExpression) {
            $connection = $this->getConnection();
            $expressionTransferObject = new \Magento\Framework\DataObject(
                [
                    'expression' => '%s - %s - %s - (%s - %s - %s) - %s - %s',
                    'arguments' => [
                        $connection->getIfNullSql('main_table.base_total_invoiced', 0),
                        $connection->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $connection->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $connection->getIfNullSql('main_table.base_total_refunded', 0),
                        $connection->getIfNullSql('main_table.base_tax_refunded', 0),
                        $connection->getIfNullSql('main_table.base_shipping_refunded', 0),
                        $connection->getIfNullSql('main_table.base_msp_cod_amount', 0),
                        $connection->getIfNullSql('main_table.base_msp_cod_tax_amount', 0),
                    ],
                ]
            );

            $this->_eventManager->dispatch(
                'sales_prepare_amount_expression',
                ['collection' => $this, 'expression_object' => $expressionTransferObject]
            );
            $this->_salesAmountExpression = vsprintf(
                $expressionTransferObject->getExpression(),
                $expressionTransferObject->getArguments()
            );
        }

        return $this->_salesAmountExpression;
    }
}
