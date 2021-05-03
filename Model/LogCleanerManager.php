<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model;

use Dathard\LogCleaner\Model\Management\DbLogs\Cleaner as DbLogsCleaner;
use Dathard\LogCleaner\Model\Management\LogFiles\Cleaner as LogFilesCleaner;

class LogCleanerManager
{
    /**
     * @var DbLogsCleaner
     */
    private $dbLogsCleaner;

    /**
     * @var LogFilesCleaner
     */
    private $logFilesCleaner;

    /**
     * LogCleanerManager constructor.
     * @param DbLogsCleaner $dbLogsCleaner
     * @param LogFilesCleaner $logFilesCleaner
     */
    public function __construct(
        DbLogsCleaner   $dbLogsCleaner,
        LogFilesCleaner $logFilesCleaner
    ) {
        $this->dbLogsCleaner = $dbLogsCleaner;
        $this->logFilesCleaner = $logFilesCleaner;
    }

    public function allLogsOptimization()
    {
        $this->optimizationDatabaseLogs();
        $this->optimizationLogFiles();
    }

    public function optimizationDatabaseLogs()
    {
        $this->dbLogsCleaner->run();
    }

    public function optimizationLogFiles()
    {
        $this->logFilesCleaner->run();
    }
}
