<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Management\DbLogs;

use Dathard\LogCleaner\Model\Management\CleanerInterface;
use Dathard\LogCleaner\Model\Config\Source\DbLogs\Period;
use Dathard\LogCleaner\Helper\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class Cleaner implements CleanerInterface
{
    /**
     * @var string[]
     */
    private $tablesToTruncate = [
        'report_event',
        'report_viewed_product_index',
        'report_compared_product_index',
        'customer_visitor'
    ];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param Config $config
     * @param ResourceConnection $resourceConnection
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
     * @throws NoSuchEntityException
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
     * @throws NoSuchEntityException
     */
    public function allowedToClean(): bool
    {
        if (! $this->config->enableLogsCleaning(Config::GROUP_DB)) {
            return false;
        }

        $rotationPeriod = $this->config->getRotationPeriod(Config::GROUP_DB);
        switch ($rotationPeriod) {
            case Period::ONCE_A_DAY:
                $allow = true;
                break;
            case Period::ONCE_A_WEEK:
                $allow = date('w') == 1;
                break;
            case Period::ONCE_A_MONTH:
                $allow = date('j') == 1;
                break;
            default:
                $allow = false;
        }

        return $allow;
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
