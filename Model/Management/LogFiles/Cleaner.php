<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Management\LogFiles;

use Dathard\LogCleaner\Model\Management\CleanerInterface;
use Dathard\LogCleaner\Model\Config\Source\LogFiles\Period;
use Dathard\LogCleaner\Helper\Config;
use Dathard\LogCleaner\Model\Management\ArchiveManagement;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Glob;

class Cleaner implements CleanerInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ArchiveManagement
     */
    private $archiveManagement;

    /**
     * @param Config $config
     * @param DirectoryList $directoryList
     * @param ArchiveManagement $archiveManagement
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
     * @throws FileSystemException
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
     * @throws FileSystemException
     * @throws NoSuchEntityException
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
                    $allow = date('d') != date('d', $lastDate);
                } else {
                    $allow = true;
                }
                break;
            case Period::ONCE_A_WEEK:
                $allow = date('w') == 1;
                break;
            case Period::ONCE_A_MONTH:
                $allow = date('d') == 1;
                break;
            case Period::CUSTOM_PERIOD:
                $logDir = $this->directoryList->getPath('log');
                $archives = $this->archiveManagement->getArchivesData($logDir . '/logs_from_*_*_*.zip');
                arsort($archives);

                $lastDate = array_shift($archives);
                $dateDiff = abs(time() - $lastDate) / 86400;

                $allow = $dateDiff >= $this->config->getCustomRotationPeriod(Config::GROUP_FILES);
                break;
            default:
                $allow = false;
        }

        return $allow;
    }

    /**
     * @return CleanerInterface
     * @throws FileSystemException
     * @throws NoSuchEntityException
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
