<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Dathard\LogCleaner\Model\LogCleanerManager;

class Cleaner
{
    /**
     * @var LogCleanerManager
     */
    private $logCleanerManager;

    /**
     * @param LogCleanerManager $logCleanerManager
     */
    public function __construct(
        LogCleanerManager   $logCleanerManager
    ) {
        $this->logCleanerManager = $logCleanerManager;
    }

    public function execute()
    {
        $this->logCleanerManager->allLogsOptimization();
    }
}

