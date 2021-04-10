<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Dathard\LogCleaner\Helper\Config;
use Magento\Framework\Archive\Zip;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Glob;
use phpDocumentor\Reflection\Types\This;

class Cleaner
{
    public static $allowedArchivesCount = 7;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var File
     */
    protected $filesystemDriver;

    /**
     * @var Zip
     */
    protected $zipArchive;

    /**
     * Cleaner constructor.
     * @param DirectoryList $dir
     * @param File $filesystemDriver
     * @param Zip $zipArchive
     */
    public function __construct(
        Config $config,
        DirectoryList $dir,
        File $filesystemDriver,
        Zip $zipArchive
    ) {
        $this->config = $config;
        $this->dir = $dir;
        $this->filesystemDriver = $filesystemDriver;
        $this->zipArchive = $zipArchive;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        if ($this->config->isModuleEnabled()) {
            if ($this->allowedToArchivate()) {
                foreach ($this->filesystemDriver->readDirectory($this->dir->getPath('log')) as $filePath) {
                    if (preg_match('/(.*?)+\.log$/', $filePath)){
                        $this->archivateFile($filePath);
                    }
                }
            }

            $this->deleteOldArchives();
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function prepareArchivesData()
    {
        $archivesData = [];
        $logDir = $this->dir->getPath('log');

        foreach (Glob::glob($logDir . '/logs_from_*_*_*.zip') as $filePath) {
            if (preg_match('/([a-zA-Z_-]+)_([0-9]+)_([0-9]+)_([0-9]+).zip/', $filePath, $found)) {
                array_shift($found);
                list($type, $month, $day, $year) = $found;
                $archivesData[$filePath] = strtotime( $year.'-'.$month.'-'.$day );
            }
        }

        return $archivesData;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function allowedToArchivate(): bool
    {
        if (! $this->config->isModuleEnabled()) {
            return false;
        }

        switch ($this->config->getRotationPeriod()) {
            case 2:
                $alow = date('d') == 1;
                break;
            case 1:
                $alow = date('w') == 1;
                break;
            default:
                if ($this->prepareArchivesData() === 0) {
                    $periodInDays = 1;
                } else {
                    $periodInDays = $this->config->getCustomPeriod();
                }

                $archives = $this->prepareArchivesData();
                arsort($archives);
                $lastDate = array_shift($archives);
                $dateDiff = abs(time() - $lastDate) / 86400;
                $alow = $dateDiff >= $periodInDays;
        }

        return $alow;
    }

    /**
     * @param $filePath
     * @return false|string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function archivateFile($filePath)
    {
        if (! $this->filesystemDriver->isExists($filePath)) {
            return false;
        }

        $rootDir = $this->dir->getRoot() . '/';
        $destination = $this->dir->getPath('log') . '/logs_from_' . date('m_d_y') . '.zip';

        $this->zipArchive->pack(
            str_replace($rootDir , '', $filePath),
            $destination
        );

        $this->filesystemDriver->deleteFile($filePath);

        return $destination;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteOldArchives()
    {
        $archives = $this->prepareArchivesData();
        $allowedArchivesCount = $this->config->getAllowedArchivesCount();

        if (! sizeof($archives)
            || sizeof($archives) <= $allowedArchivesCount) {
            return true;
        }

        arsort($archives);
        end($archives);

        for ($i = 1; $i <= sizeof($archives) - $allowedArchivesCount; $i++) {
            $filePath = key($archives);
            if ($this->filesystemDriver->isExists($filePath)) {
                $this->filesystemDriver->deleteFile($filePath);
            }
            prev($archives);
        }

        return true;
    }
}

