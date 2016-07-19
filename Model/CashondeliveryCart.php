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

namespace MSP\CashOnDelivery\Model;

use Magento\Checkout\Model\Cart as MageCart;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MSP\CashOnDelivery\Api\CashondeliveryCartInterface;

class CashondeliveryCart implements CashondeliveryCartInterface
{
    protected $priceCurrencyInterface;
    protected $cart;
    protected $_quote = null;

    public function __construct(
        PriceCurrencyInterface $priceCurrencyInterface,
        MageCart $cart
    ) {
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->cart = $cart;
    }

    /**
     * Get current quote
     * @return \Magento\Quote\Model\Quote
     */
    protected function _getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = $this->cart->getQuote();
            $this->_quote->collectTotals();
        }
        
        return $this->_quote;
    }

    /**
     * Get amount
     * @return double
     */
    public function getAmount()
    {
        return $this->_getQuote()->getMspCodAmount();
    }

    /**
     * Get base amount
     * @return double
     */
    public function getBaseAmount()
    {
        return $this->_getQuote()->getBaseMspCodAmount();
    }

    /**
     * Get additional fee label
     * @return string
     */
    public function getFeeLabel()
    {
        $amount = $this->getAmount();
        $taxAmount = $this->getTaxAmount();

        return __('You will be charged by an extra fee of %1 (+%2 taxes)', [
            $this->priceCurrencyInterface->format($amount),
            $this->priceCurrencyInterface->format($taxAmount),
        ]);
    }

    /**
     * Get base tax amount
     * @return double
     */
    public function getBaseTaxAmount()
    {
        return $this->_getQuote()->getBaseMspCodTaxAmount();
    }

    /**
     * Get tax amount
     * @return double
     */
    public function getTaxAmount()
    {
        return $this->_getQuote()->getMspCodTaxAmount();
    }
}