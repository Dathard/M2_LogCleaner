<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Management\DbLogs;

use Dathard\LogCleaner\Model\Management\CleanerInterface;
use Dathard\LogCleaner\Model\Config\Source\DbLogs\Period;
use Dathard\LogCleaner\Helper\Config;
use Magento\Framework\App\ResourceConnection;

class Cleaner implements CleanerInterface
{
    private $tablesToTruncate = [
        'report_event',
        'report_viewed_product_index',
        'report_compared_product_index',
        'customer_visitor'
    ];

    /**
     * @var \Dathard\LogCleaner\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * Cleaner constructor.
     * @param \Dathard\LogCleaner\Helper\Config         $config
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        Config              $config,
        ResourceConnection  $resourceConnection
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return CleanerInterface
     */
    public function run(): CleanerInterface
    {
        if ($this->allowedToClean()) {
            $this->cleaning();
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function allowedToClean(): bool
    {
        if (! $this->config->enableLogsCleaning(Config::GROUP_DB)) {
            return false;
        }

        $rotationPeriod = $this->config->getRotationPeriod(Config::GROUP_DB);
        switch ($rotationPeriod) {
            case Period::ONCE_A_DAY:
                $alow = true;
            case Period::ONCE_A_WEEK:
                $alow = date('w') == 1;
                break;
            case Period::ONCE_A_MONTH:
                $alow = date('j') == 1;
                break;
            default:
                $alow = false;
        }

        return (bool) $alow;
    }

    /**
     * @return CleanerInterface
     */
    private function cleaning(): CleanerInterface
    {
        $connection = $this->resourceConnection->getConnection();

        foreach($this->tablesToTruncate as $table){
            $tableName = $this->resourceConnection->getTableName($table);
            $connection->truncateTable($tableName);
        }

        return $this;
    }
}
