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
namespace MSP\CashOnDelivery\Controller\Adminhtml\Table;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Response\Http\FileFactory;
use MSP\CashOnDelivery\Api\CashondeliveryTableInterface;

class Export extends Action
{
    protected $cashondeliveryTableInterface;
    protected $fileFactory;

    public function __construct(
        Action\Context $context,
        CashondeliveryTableInterface $cashondeliveryTableInterface,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);

        $this->cashondeliveryTableInterface = $cashondeliveryTableInterface;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $csvFile = $this->cashondeliveryTableInterface->getTableAsCsv();
        return $this->fileFactory->create(
            'msp_cashondelivery.csv',
            $csvFile,
            DirectoryList::VAR_DIR,
            'text/csv',
            null
        );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Payment::payment');
    }
}