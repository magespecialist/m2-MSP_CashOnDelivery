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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    protected $salesSetupFactory;
    protected $quoteSetupFactory;

    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    protected function _setupSalesTables(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $attributes = [
            'msp_cod_amount' => 'Cash On Delivery Amount',
            'base_msp_cod_amount' => 'Cash On Delivery Base Amount',
            'msp_cod_tax_amount' => 'Cash On Delivery Tax Amount',
            'base_msp_cod_tax_amount' => 'Cash On Delivery Base Tax Amount',
        ];

        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        foreach ($attributes as $attributeCode => $attributeLabel) {
            $salesSetup->addAttribute('order', $attributeCode, ['type' => 'decimal']);
            $salesSetup->addAttribute('order_address', $attributeCode, ['type' => 'decimal']);
        }

        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        foreach ($attributes as $attributeCode => $attributeLabel) {
            $quoteSetup->addAttribute('quote', $attributeCode, ['type' => 'decimal']);
            $quoteSetup->addAttribute('quote_address', $attributeCode, ['type' => 'decimal']);
        }
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->_setupSalesTables($setup, $context);

        $setup->endSetup();
    }
}