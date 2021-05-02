<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model;

use Dathard\LogCleaner\Helper\Config;
use Magento\Framework\Filesystem\DirectoryList;
use Dathard\LogCleaner\Model\Management\ArchiveManagement;
use Magento\Framework\Filesystem\Glob;

class LogManagement
{
    /**
     * @var \Dathard\LogCleaner\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Dathard\LogCleaner\Model\Management\ArchiveManagement
     */
    private $archiveManagement;

    /**
     * LogManagement constructor.
     * @param \Dathard\LogCleaner\Helper\Config                         $config
     * @param \Magento\Framework\Filesystem\DirectoryList               $directoryList
     * @param \Dathard\LogCleaner\Model\Management\ArchiveManagement    $archiveManagement
     */
    public function __construct(
        Config $config,
        DirectoryList $directoryList,
        ArchiveManagement $archiveManagement
    ) {
        $this->config = $config;
        $this->directoryList = $directoryList;
        $this->archiveManagement = $archiveManagement;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function optimizeLogs(): bool
    {
        if (! $this->config->enableLogsCleaning(Config::GROUP_FILES)) {
            return false;
        }

        $logDir = $this->directoryList->getPath('log');

        if ($this->allowedToArchivate()) {
            foreach (Glob::glob($logDir . '/*.log') as $filePath) {
                if (preg_match('/(.*?)+\.log$/', $filePath)){
                    $destination = $logDir . '/logs_from_' . date('m_d_y') . '.zip';
                    $this->archiveManagement->archivateFile($filePath, $destination);
                }
            }
        }

        $this->archiveManagement->deleteOldArchives(
            $logDir . '/logs_from_*_*_*.zip',
            $this->config->getAllowedArchivesCount(Config::GROUP_FILES)
        );

        return true;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function allowedToArchivate(): bool
    {
        if (! $this->config->enableLogsCleaning(Config::GROUP_FILES)) {
            return false;
        }

        switch ($this->config->getRotationPeriod(Config::GROUP_FILES)) {
            case 0:
                $logDir = $this->directoryList->getPath('log');
                $archives = $this->archiveManagement->getArchivesData($logDir . '/logs_from_*_*_*.zip');

                if (count($archives) > 0) {
                    arsort($archives);
                    $lastDate = array_shift($archives);
                    $alow = date('d') != date('d', $lastDate);
                } else {
                    $alow = true;
                }
                break;
            case 1:
                $alow = date('w') == 1;
                break;
            case 2:
                $alow = date('d') == 1;
                break;
            default:
                $logDir = $this->directoryList->getPath('log');
                $archives = $this->archiveManagement->getArchivesData($logDir . '/logs_from_*_*_*.zip');
                arsort($archives);

                $lastDate = array_shift($archives);
                $dateDiff = abs(time() - $lastDate) / 86400;

                $alow = $dateDiff >= $this->config->getCustomRotationPeriod(Config::GROUP_FILES);
        }

        return $alow;
    }
}
