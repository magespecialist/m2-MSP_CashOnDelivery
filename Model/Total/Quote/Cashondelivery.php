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
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use MSP\CashOnDelivery\Api\CashondeliveryInterface;
use Magento\Quote\Model\Quote\Address;

class Cashondelivery extends AbstractTotal
{
    protected $cashOnDeliveryInterface;
    protected $priceCurrencyInterface;

    public function __construct(
        PaymentMethodManagementInterface $paymentMethodManagement,
        PriceCurrencyInterface $priceCurrencyInterface,
        CashondeliveryInterface $cashOnDeliveryInterface
    ) {
        parent::__construct($paymentMethodManagement);

        $this->cashOnDeliveryInterface = $cashOnDeliveryInterface;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->setCode('msp_cashondelivery');
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        if ($shippingAssignment->getShipping()->getAddress()->getAddressType() != Address::TYPE_SHIPPING
            || $quote->isVirtual()
        ) {
            return $this;
        }

        $country = $quote->getShippingAddress()->getCountryModel()->getData('iso2_code');
        $region = $quote->getShippingAddress()->getRegionModel()->getData('code');

        if (!$region && is_string($quote->getShippingAddress()->getRegion())) {
            $region = $quote->getShippingAddress()->getRegion();
        }

        $baseAmount = 0;
        $totalAmounts = $total->getAllBaseTotalAmounts();
        if ($totalAmounts) {
            $baseAmount = $this->cashOnDeliveryInterface->getBaseAmount($totalAmounts, $country, $region);
            $baseTaxAmount = $baseAmount - $this->cashOnDeliveryInterface->getBaseTaxAmount($baseAmount);
        }
        $amount = $this->priceCurrencyInterface->convert($baseAmount);
        $TaxAmount = $this->priceCurrencyInterface->convert($baseTaxAmount);      

        if ($this->_canApplyTotal($quote)) {
            $total->setBaseTotalAmount('msp_cashondelivery', $baseAmount);
            $total->setTotalAmount('msp_cashondelivery', $amount);

            $total->setBaseMspCodAmount($baseAmount);
            $total->setMspCodAmount($amount);
          
            $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseTaxAmount);
            $total->setGrandTotal($total->getGrandTotal() + $TaxAmount );
        }

        /*
         * This must be always calculated despite the method selection.
         * An empty value will result in a wrong fee preview on payment method.
         */
        $quote->setBaseMspCodAmount($baseAmount);
        $quote->setMspCodAmount($amount);

        return $this;
    }

    public function fetch(Quote $quote, Total $total)
    {
        if ($this->_canApplyTotal($quote)) {
            return [
                'code' => $this->getCode(),
                'title' => __('Cash On Delivery'),
                'value' => $total->getMspCodAmount(),
            ];
        }

        return null;
    }

    public function getLabel()
    {
        return __('Cash On Delivery');
    }
}
