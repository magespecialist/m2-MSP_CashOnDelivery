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

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal as MageAbstractTotal;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote;

abstract class AbstractTotal extends MageAbstractTotal
{
    /**
     * Return true if can apply totals
     * @param Quote $quote
     * @return bool
     */
    protected function _canApplyTotal(Quote $quote)
    {
        return ($quote->getPayment()->getMethod() == 'msp_cashondelivery');
    }
}
