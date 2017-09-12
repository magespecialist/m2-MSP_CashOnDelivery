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
 * @copyright  Copyright (c) 2014 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\CashOnDelivery\Block\Info;

use Magento\Payment\Block\Info;
use Magento\Sales\Model\Order;

class CashOnDelivery extends Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        return parent::_prepareSpecificInformation($transport);

//        if (is_null($this->_paymentSpecificInformation)) {
//            $transport = parent::_prepareSpecificInformation($transport);
//            $data = [];
//
//            /** @var Order $order */
//            $order = $this->getInfo()->getOrder();
//
//            $taxes = $order->getMspCodTaxAmount();
//            $amount = $order->getMspCodAmount() - $taxes;
//
//            $data[(string) __("Cash On Delivery Amount")] = $order->getOrderCurrency()->formatPrecision($amount, 2, [], false);
//            $data[(string) __("Cash On Delivery Tax")] = $order->getOrderCurrency()->formatPrecision($taxes, 2, [], false);
//
//            $this->_paymentSpecificInformation = $transport->setData(array_merge($data, $transport->getData()));
//        }
//
//        return $this->_paymentSpecificInformation;
    }
}
