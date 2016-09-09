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

namespace MSP\CashOnDelivery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    protected $quoteRepository;

    public function __construct(
        QuoteRepository $quoteRepository
    ) {

        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getPayment()->getMethod() == 'msp_cashondelivery') {
            $order->setMspCodAmount($quote->getMspCodAmount());
            $order->setBaseMspCodAmount($quote->getBaseMspCodAmount());
            $order->setMspCodTaxAmount($quote->getMspCodTaxAmount());
            $order->setBaseMspCodTaxAmount($quote->getBaseMspCodTaxAmount());
        }
    }
}
