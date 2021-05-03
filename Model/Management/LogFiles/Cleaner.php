<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Management\LogFiles;

use Dathard\LogCleaner\Model\Management\CleanerInterface;
use Dathard\LogCleaner\Model\Config\Source\LogFiles\Period;
use Dathard\LogCleaner\Helper\Config;
use Dathard\LogCleaner\Model\Management\ArchiveManagement;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Glob;

class Cleaner implements CleanerInterface
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
     * Cleaner constructor.
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
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function allowedToClean(): bool
    {
        if (! $this->config->enableLogsCleaning(Config::GROUP_FILES)) {
            return false;
        }

        $rotationPeriod = $this->config->getRotationPeriod(Config::GROUP_FILES);
        switch ($rotationPeriod) {
            case Period::ONCE_A_DAY:
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
            case Period::ONCE_A_WEEK:
                $alow = date('w') == 1;
                break;
            case Period::ONCE_A_MONTH:
                $alow = date('d') == 1;
                break;
            case Period::CUSTOM_PERIOD:
                $logDir = $this->directoryList->getPath('log');
                $archives = $this->archiveManagement->getArchivesData($logDir . '/logs_from_*_*_*.zip');
                arsort($archives);

                $lastDate = array_shift($archives);
                $dateDiff = abs(time() - $lastDate) / 86400;

                $alow = $dateDiff >= $this->config->getCustomRotationPeriod(Config::GROUP_FILES);
                break;
            default:
                $alow = false;
        }

        return (bool) $alow;
    }

    /**
     * @return CleanerInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function cleaning(): CleanerInterface
    {
        $logDir = $this->directoryList->getPath('log');

        foreach (Glob::glob($logDir . '/*.log') as $filePath) {
            if (preg_match('/(.*?)+\.log$/', $filePath)){
                $destination = $logDir . '/logs_from_' . date('m_d_y') . '.zip';
                $this->archiveManagement->archivateFile($filePath, $destination);
            }
        }

        $this->archiveManagement->deleteOldArchives(
            $logDir . '/logs_from_*_*_*.zip',
            $this->config->getAllowedArchivesCount(Config::GROUP_FILES)
        );

        return $this;
    }
}
