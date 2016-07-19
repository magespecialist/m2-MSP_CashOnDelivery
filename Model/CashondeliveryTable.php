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

namespace MSP\CashOnDelivery\Model;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use MSP\CashOnDelivery\Api\CashondeliveryTableInterface;

class CashondeliveryTable extends AbstractModel implements CashondeliveryTableInterface
{
    protected $csv;
    protected $filesystem;
    protected $file;

    protected $_columns = ['country', 'from_amount', 'fee'];

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Csv $csv,
        Filesystem $filesystem,
        File $file,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->csv = $csv;
        $this->filesystem = $filesystem;
        $this->file = $file;
    }

    protected function _construct()
    {
        $this->_init('MSP\CashOnDelivery\Model\ResourceModel\CashondeliveryTable');
    }

    /**
     * Get cash on delivery fee
     *
     * @param double $amount
     * @param string $country
     * @return double
     */
    public function getFee($amount, $country)
    {
        return $this->_getResource()->getFee($amount, $country);
    }

    /**
     * Get table as array
     * 
     * @return array
     */
    public function getTableAsArray()
    {
        return $this->_getResource()->getTableAsArray();
    }

    /**
     * Get table as CSV
     *
     * @return string
     */
    public function getTableAsCsv()
    {
        $data = $this->getTableAsArray();

        $tmpDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $fileName = $tmpDir->getAbsolutePath(uniqid(md5(time())).'.csv');

        $dataOut = [$this->_columns];
        foreach ($data as $row) {
            $dataOutRow = [];
            foreach ($this->_columns as $column) {
                if (($column == 'fee') && ($row['is_pct'])) {
                    $dataOutRow[] = $row[$column].'%';
                } else {
                    $dataOutRow[] = $row[$column];
                }
            }
            $dataOut[] = $dataOutRow;
        }

        $this->csv->saveData($fileName, $dataOut);

        $res = $this->file->fileGetContents($fileName);
        $this->file->deleteFile($fileName);

        return $res;
    }

    /**
     * Save from file
     *
     * @param string $fileName
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFromFile($fileName)
    {
        $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($fileName);
        $stream = $tmpDirectory->openFile($path);

        $headers = $stream->readCsv();
        if ($headers === false || count($headers) < 3) {
            $stream->close();
            throw new LocalizedException(__('Invalid columns count.'));
        }

        $columnsMap = array_flip($headers);

        $data = [];

        $rowNumber = 0;
        while (false !== ($csvLine = $stream->readCsv())) {

            if (empty($csvLine)) {
                continue;
            }

            $rowNumber++;

            $dataRow = [];

            // @codingStandardsIgnoreStart
            for ($i=0; $i<count($headers); $i++) {
            // @codingStandardsIgnoreEnd
                foreach ($this->_columns as $columnName) {

                    $value = $csvLine[$columnsMap[$columnName]];

                    if ($columnName == 'fee') {
                        $dataRow['is_pct'] = (strpos($value, '%') !== false);
                        $value = floatval(str_replace('%', '', $value));

                    } else if ($columnName == 'from_amount') {
                        $value = floatval($value);

                    }

                    $dataRow[$columnName] = $value;
                }
            }

            $data[] = $dataRow;
        }

        $this->_getResource()->populateFromArray($data);

        return $rowNumber;
    }

    /**
     * Get number of rows
     * @return int
     */
    public function getRowsCount()
    {
        return $this->_getResource()->getRowsCount();
    }
}