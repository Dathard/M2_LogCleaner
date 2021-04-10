<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Dathard\LogCleaner\Helper\Config;
use Dathard\LogCleaner\Model\LogManagement;

class Cleaner
{
    /**
     * @var \Dathard\LogCleaner\Helper\Config
     */
    private $config;

    /**
     * @var \Dathard\LogCleaner\Model\LogManagement
     */
    private $logManagement;

    /**
     * Cleaner constructor.
     * @param \Dathard\LogCleaner\Helper\Config $config
     * @param \Dathard\LogCleaner\Model\LogManagement $logManagement
     */
    public function __construct(
        Config $config,
        LogManagement $logManagement
    ) {
        $this->config = $config;
        $this->logManagement = $logManagement;
    }

    public function execute()
    {
        if ($this->config->isModuleEnabled()) {
            $this->logManagement->optimizeLogs();
        }
    }
}

