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

namespace MSP\CashOnDelivery\Block\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use MSP\CashOnDelivery\Model\Payment;

class Cashondelivery extends Template
{
    public function displayFullSummary()
    {
        return true;
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        $payment = $this->getPayment($source);
        if ($payment && ($payment->getMethod() == Payment::CODE)) {
            $fee = new DataObject(
                [
                    'code' => 'msp_cashondelivery',
                    'strong' => false,
                    'value' => $source->getBaseMspCodAmount() - $source->getBaseMspCodTaxAmount(),
                    'label' => __('Cash on delivery'),
                ]
            );

            $parent->addTotalBefore($fee, 'grand_total');
        }

        return $this;
    }

    protected function getPayment($source)
    {
        if ($source instanceof InvoiceInterface) {
            return $source->getOrder()->getPayment();
        }

        if ($source instanceof OrderInterface) {
            return $source->getPayment();
        }

        if ($source instanceof CreditMemoInterface) {
            return $source->getOrder()->getPayment();
        }

        return null;
    }
}
