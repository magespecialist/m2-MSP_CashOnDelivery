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
namespace MSP\CashOnDelivery\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form;
use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use MSP\CashOnDelivery\Api\CashondeliveryTableInterface;


class Import extends AbstractElement
{
    protected $cashondeliveryTableInterface;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        CashondeliveryTableInterface $cashondeliveryTableInterface,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->cashondeliveryTableInterface = $cashondeliveryTableInterface;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setType('file');
    }

    public function getElementHtml()
    {
        $res = parent::getElementHtml();

        $rowsCount = $this->cashondeliveryTableInterface->getRowsCount();

        if ($rowsCount) {
            $res .= '<br /><p class="note">' . __('You have <strong>%1</strong> rule(s) configured', [$rowsCount]) . '</p>';
        } else {
            $res .= '<br /><p class="note">' . __('Rules table is empty') . '</p>';
        }

        return $res;
    }
}
