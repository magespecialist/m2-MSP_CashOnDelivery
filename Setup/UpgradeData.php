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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    protected $salesSetupFactory;

    public function __construct(
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Upgrade to version 1.0.0
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    protected function upgradeTo100(ModuleDataSetupInterface $setup)
    {
        $attributes = [
            'msp_cod_amount' => 'Cash On Delivery Amount',
            'base_msp_cod_amount' => 'Cash On Delivery Base Amount',
            'msp_cod_tax_amount' => 'Cash On Delivery Tax Amount',
            'base_msp_cod_tax_amount' => 'Cash On Delivery Base Tax Amount',
        ];

        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        foreach ($attributes as $attributeCode => $attributeLabel) {
            $salesSetup->addAttribute('invoice', $attributeCode, ['type' => 'decimal']);
            $salesSetup->addAttribute('creditmemo', $attributeCode, ['type' => 'decimal']);
        }
    }


    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->upgradeTo100($setup);
        }

        $setup->endSetup();
    }
}
