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

namespace MSP\CashOnDelivery\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use MSP\CashOnDelivery\Api\CashondeliveryInterface;

class Cashondelivery extends AbstractTotal
{
    protected $cashOnDeliveryInterface;
    protected $priceCurrencyInterface;

    public function __construct(
        PriceCurrencyInterface $priceCurrencyInterface,
        CashondeliveryInterface $cashOnDeliveryInterface
    ) {
        $this->cashOnDeliveryInterface = $cashOnDeliveryInterface;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->setCode('msp_cashondelivery');
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        //parent::collect($quote, $shippingAssignment, $total);

        $country = $quote->getShippingAddress()->getCountryModel()->getData('iso2_code');

        $baseAmount = $this->cashOnDeliveryInterface->getBaseAmount($total->getAllBaseTotalAmounts(), $country);
        $amount = $this->priceCurrencyInterface->convert($baseAmount);

        if ($this->_canApplyTotal($quote)) {
            $total->setBaseTotalAmount('msp_cashondelivery', $baseAmount);
            $total->setTotalAmount('msp_cashondelivery', $amount);

            $total->setBaseMspCodAmount($baseAmount);
            $total->setMspCodAmount($amount);

            $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseAmount);
            $total->setGrandTotal($total->getGrandTotal() + $amount);
        }

        return $this;
    }

    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code' => 'msp_cashondelivery',
            'title' => __('Cash On Delivery'),
            'value' => $this->_canApplyTotal($quote) ? $total->getMspCodAmount() : 0,
        ];
    }

    public function getLabel()
    {
        return __('Cash On Delivery');
    }
}
