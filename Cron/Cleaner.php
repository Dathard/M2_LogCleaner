<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Cron;

use Magento\Framework\Archive\Zip;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;

class Cleaner
{
    public static $allowedArchivesCount = 2;


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
        foreach ($this->filesystemDriver->readDirectory($this->dir->getPath('log')) as $filePath) {
            if (preg_match('/(.*?)+\.log$/', $filePath)){
                $this->archivateFile($filePath);
            }
        }

        $this->deleteOldArchives();
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
        $archives = [];

        foreach ($this->filesystemDriver->readDirectory($this->dir->getPath('log')) as $filePath) {
            if (preg_match('/([a-zA-Z_-]+)_([0-9]+)_([0-9]+)_([0-9]+).zip/', $filePath, $found)) {
                array_shift($found);
                list($type, $month, $day, $year) = $found;
                $archives[$filePath] = strtotime( $year.'-'.$month.'-'.$day );
            }
        }

        if (! sizeof($archives)
            || sizeof($archives) <= self::$allowedArchivesCount) {
            return true;
        }

        arsort($archives);
        end($archives);

        for ($i = 1; $i <= sizeof($archives) - self::$allowedArchivesCount; $i++) {
            $filePath = key($archives);
            if ($this->filesystemDriver->isExists($filePath)) {
                $this->filesystemDriver->deleteFile($filePath);
            }
            prev($archives);
        }

        return true;
    }
}

