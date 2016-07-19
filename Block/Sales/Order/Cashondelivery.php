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

namespace MSP\CashOnDelivery\Block\Sales\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;

class Cashondelivery extends Template
{
    protected $_source;
    protected $_order;

    public function getSource()
    {
        return $this->_source;
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();

        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $fee = new DataObject(
            [
                'code' => 'msp_cashondelivery',
                'strong' => false,
                'value' => $this->getSource()->getMspCashOnDelivery(),
                'label' => __('Cash on delivery'),
            ]
        );

        $parent->addTotal($fee, 'msp_cashondelivery');

        return $this;
    }
}