<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const SECTION_ID = 'logcleaner';
    const GROUP_GENERAL = 'general';

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
     * @param null $store
     * @param null $scope
     * @return bool
     */
    public function isModuleEnabled($store = null, $scope = null): bool
    {
        return (bool) $this->getConfig(self::GROUP_GENERAL, 'enable');
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getRotationPeriod($store = null, $scope = null): int
    {
        return (int) $this->getConfig(self::GROUP_GENERAL, 'period');
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getCustomPeriod($store = null, $scope = null): int
    {
        return (int) abs($this->getConfig(self::GROUP_GENERAL, 'custom_period'));
    }

    /**
     * @param null $store
     * @param null $scope
     * @return int
     */
    public function getAllowedArchivesCount($store = null, $scope = null): int
    {
        return (int) abs($this->getConfig(self::GROUP_GENERAL, 'allowed_archives_count'));
    }
}
