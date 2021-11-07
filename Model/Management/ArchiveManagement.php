<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Management;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Archive\Zip;
use Magento\Framework\Filesystem\Glob;

class ArchiveManagement
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var File
     */
    private $filesystemDriver;

    /**
     * @var Zip
     */
    private $zipArchive;

    /**
     * @param DirectoryList $directoryList
     * @param File $filesystemDriver
     * @param Zip $zipArchive
     */
    public function __construct(
        DirectoryList $directoryList,
        File $filesystemDriver,
        Zip $zipArchive
    ) {
        $this->directoryList = $directoryList;
        $this->filesystemDriver = $filesystemDriver;
        $this->zipArchive = $zipArchive;
    }

    /**
     * @param string $pattern
     * @param bool $prepare
     * @return array
     */
    public function getArchivesData(string $pattern, bool $prepare = true): array
    {
        $archivesData = Glob::glob($pattern);

        if ($prepare) {
            $archivesData = $this->prepareArchivesData($archivesData);
        }

        return $archivesData;
    }

    /**
     * @param array $archives
     * @return array
     */
    public function prepareArchivesData(array $archives): array
    {
        $archivesData = [];

        foreach ($archives as $filePath) {
            if (preg_match('/([a-zA-Z_-]+)_([0-9]+)_([0-9]+)_([0-9]+).zip/', $filePath, $found)) {
                array_shift($found);
                list($type, $month, $day, $year) = $found;
                $archivesData[$filePath] = strtotime( $year.'-'.$month.'-'.$day );
            }
        }

        return $archivesData;
    }

    /**
     * @param string $filePath
     * @param string $destination
     * @return false|string
     * @throws FileSystemException
     */
    public function archivateFile(string $filePath, string $destination)
    {
        if (! $this->filesystemDriver->isExists($filePath)) {
            return false;
        }

        $this->zipArchive->pack(
            str_replace($this->directoryList->getRoot().'/' , '', $filePath),
            $destination
        );

        $this->filesystemDriver->deleteFile($filePath);

        return $destination;
    }

    /**
     * @param string $pattern
     * @param int $allowedArchivesCount
     * @return bool
     * @throws FileSystemException
     */
    public function deleteOldArchives(string $pattern, int $allowedArchivesCount): bool
    {
        $archives = $this->getArchivesData($pattern);

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
