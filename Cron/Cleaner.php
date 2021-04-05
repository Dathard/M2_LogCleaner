<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Magento\Framework\Archive\Zip;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;

class Cleaner
{
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
        DirectoryList $dir,
        File $filesystemDriver,
        Zip $zipArchive
    ) {
        $this->dir = $dir;
        $this->filesystemDriver = $filesystemDriver;
        $this->zipArchive = $zipArchive;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $logDir = $this->dir->getPath('log');
        $rootDir = $this->dir->getRoot();

        foreach ($this->filesystemDriver->readDirectory($logDir) as $logFile) {
            if (preg_match('/(.*?)+\.log$/', $logFile)){
                $this->zipArchive->pack(
                    str_replace($rootDir . '/', '', $logFile),
                    $logDir . '/logs_from_' . date('m_d_y') . '.zip'
                );
            }
        }
    }
}

