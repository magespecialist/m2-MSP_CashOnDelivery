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
namespace MSP\CashOnDelivery\Plugin\Model\Quote\Address;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrder;

class 
ToOrderPlugin
{
    protected $orderExtensionFactory;

    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    public function aroundConvert(
        ToOrder $subject,
        \Closure $procede,
        Address $address,
        $data = []
    ) {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $procede($address, $data);

        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes == null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $fields = [
            'msp_cod_amount',
            'base_msp_cod_amount',
            'msp_cod_tax_amount',
            'base_msp_cod_tax_amount',
        ];

        foreach ($fields as $field) {
            $extensionAttributes->setData($field, $address->getData($field));
            $order->setData($field, $address->getData($field));
        }

        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}