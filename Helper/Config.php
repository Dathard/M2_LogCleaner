<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Config extends AbstractHelper
{
    const SECTION_ID = 'logcleaner';
    const GROUP_FILES = 'log_files';
    const GROUP_DB = 'db_logs';

    /**
     * Receive magento config value
     *
     * @param string      $path
     * @param string|int  $scopeCode store view code or website code
     * @param string|null $scopeType
     * @return mixed
     */
    public function getConfig($group, $path, $scopeCode = null, $scopeType = null)
    {
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }

        return $this->scopeConfig->getValue(
            implode('/', [static::SECTION_ID, $group, $path]),
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $logsType
     * @return bool
     * @throws NoSuchEntityException
     */
    public function enableLogsCleaning(string $logsType): bool
    {
        switch ($logsType) {
            case self::GROUP_FILES:
                $value = $this->getConfig(self::GROUP_FILES, 'enable');
                break;
            case self::GROUP_DB:
                $value = $this->getConfig(self::GROUP_DB, 'enable');;
                break;
            default:
                throw new NoSuchEntityException(
                    __('Invalid log type "%1".', $logsType)
                );
        }

        return (bool) $value;
    }

    /**
     * @param string $logsType
     * @return int
     * @throws NoSuchEntityException
     */
    public function getRotationPeriod(string $logsType): int
    {
        switch ($logsType) {
            case self::GROUP_FILES:
                $value = $this->getConfig(self::GROUP_FILES, 'period');
                break;
            case self::GROUP_DB:
                $value = $this->getConfig(self::GROUP_DB, 'period');;
                break;
            default:
                throw new NoSuchEntityException(
                    __('Invalid log type "%1".', $logsType)
                );
        }

        return (int) $value;
    }

    /**
     * @param string $logsType
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCustomRotationPeriod(string $logsType): int
    {
        switch ($logsType) {
            case self::GROUP_FILES:
                $value = $this->getConfig(self::GROUP_FILES, 'custom_period');
                break;
            default:
                throw new NoSuchEntityException(
                    __('Invalid log type "%1".', $logsType)
                );
        }

        return (int) $value;
    }

    /**
     * @param string $logsType
     * @return int
     * @throws NoSuchEntityException
     */
    public function getAllowedArchivesCount(string $logsType): int
    {
        switch ($logsType) {
            case self::GROUP_FILES:
                $value = $this->getConfig(self::GROUP_FILES, 'allowed_archives_count');
                break;
            default:
                throw new NoSuchEntityException(
                    __('Invalid log type "%1".', $logsType)
                );
        }

        return (int) $value;
    }
}
