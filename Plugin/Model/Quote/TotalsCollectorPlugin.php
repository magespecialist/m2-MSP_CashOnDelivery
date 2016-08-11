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
namespace MSP\CashOnDelivery\Plugin\Model\Quote;

use Magento\Quote\Model\Quote;

class TotalsCollectorPlugin
{
    public function aroundCollect(Quote\TotalsCollector $subject, \Closure $procede, Quote $quote)
    {
        $total = $procede($quote);

        foreach ($quote->getAllAddresses() as $address) {
            $addressTotal = $subject->collectAddressTotals($quote, $address);

            $total->setMspCodAmount($addressTotal->getMspCodAmount());
            $total->setBaseMspCodAmount($addressTotal->getBaseMspCodAmount());

            $total->setMspCodTaxAmount($addressTotal->getMspCodTaxAmount());
            $total->setBaseMspCodTaxAmount($addressTotal->getBaseMspCodTaxAmount());
        }

        return $total;
    }
}
