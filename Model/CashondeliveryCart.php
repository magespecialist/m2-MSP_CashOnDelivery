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

use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MSP\CashOnDelivery\Api\CashondeliveryCartInterface;
use MSP\CashOnDelivery\Api\CashondeliveryInterface;

class CashondeliveryCart implements CashondeliveryCartInterface
{
    protected $priceCurrencyInterface;
    protected $checkoutSession;
    protected $cashondeliveryInterface;
    protected $quote = null;

    public function __construct(
        PriceCurrencyInterface $priceCurrencyInterface,
        CashondeliveryInterface $cashondeliveryInterface,
        CheckoutSession $checkoutSession
    ) {
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->checkoutSession = $checkoutSession;
        $this->cashondeliveryInterface = $cashondeliveryInterface;
    }

    /**
     * Get current quote
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (is_null($this->quote)) {
            $this->quote = $this->checkoutSession->getQuote();
            $this->quote->collectTotals();
        }
        
        return $this->quote;
    }

    /**
     * Get amount
     * @return double
     */
    public function getAmount()
    {
        return $this->getQuote()->getMspCodAmount();
    }

    /**
     * Get base amount
     * @return double
     */
    public function getBaseAmount()
    {
        return $this->getQuote()->getBaseMspCodAmount();
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
        return $this->getQuote()->getBaseMspCodTaxAmount();
    }

    /**
     * Get tax amount
     * @return double
     */
    public function getTaxAmount()
    {
        return $this->getQuote()->getMspCodTaxAmount();
    }
}
