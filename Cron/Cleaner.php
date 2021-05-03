<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Dathard\LogCleaner\Model\LogCleanerManager;

class Cleaner
{
    /**
     * @var \Dathard\LogCleaner\Model\LogCleanerManager
     */
    private $logCleanerManager;

    /**
     * Cleaner constructor.
     * @param \Dathard\LogCleaner\Model\LogCleanerManager   $logCleanerManager
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

