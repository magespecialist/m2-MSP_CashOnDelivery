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

namespace MSP\CashOnDelivery\Model\Total\Invoice;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order\Invoice;
use MSP\CashOnDelivery\Api\CashondeliveryInterface;

class Cashondelivery extends AbstractTotal
{
    protected $cashOnDeliveryInterface;
    protected $priceCurrencyInterface;

    public function __construct(
        PriceCurrencyInterface $priceCurrencyInterface,
        CashondeliveryInterface $cashOnDeliveryInterface,
        $data = []
    ) {
        $this->cashOnDeliveryInterface = $cashOnDeliveryInterface;
        $this->priceCurrencyInterface = $priceCurrencyInterface;

        parent::__construct($data);
    }

    public function collect(Invoice $invoice)
    {
        return $this;
    }

    public function fetch()
    {
        return [
            'code' => 'msp_cashondelivery',
            'title' => 'Cash On Delivery',
            'value' => 0,
        ];
    }

    public function getLabel()
    {
        return __('Cash On Delivery');
    }
}
