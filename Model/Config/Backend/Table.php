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
namespace MSP\CashOnDelivery\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use MSP\CashOnDelivery\Api\CashondeliveryTableInterface;

class Table extends Value
{
    protected $cashondeliveryTableInterface;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        CashondeliveryTableInterface $cashondeliveryTableInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->cashondeliveryTableInterface = $cashondeliveryTableInterface;
    }

    public function afterSave()
    {
        // @codingStandardsIgnoreStart
        if (empty($_FILES['groups']['tmp_name']['msp_cashondelivery']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['msp_cashondelivery']['fields']['import']['value'];
        // @codingStandardsIgnoreEnd

        $this->cashondeliveryTableInterface->saveFromFile($csvFile);

        return parent::afterSave();
    }
}
