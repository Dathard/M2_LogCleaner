<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Block\Adminhtml\System\Config\Notifications;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Dathard\LogCleaner\Service\GitApiService;
use Dathard\LogCleaner\Helper\Data;

class ChangeLogs extends Template
{
    /**
     * @var string
     */
    protected $_template = 'system/config/notifications/change-logs.phtml';

    /**
     * @var null|array
     */
    protected $latestReleaseData;

    /**
     * @var \Dathard\LogCleaner\Service\GitApiService
     */
    private $gitApiService;

    /**
     * @var \Dathard\LogCleaner\Helper\Data
     */
    private $dataHelper;

    /**
     * Version constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Dathard\LogCleaner\Service\GitApiService $gitApiService
     * @param \Dathard\LogCleaner\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        GitApiService $gitApiService,
        Data $dataHelper
    ) {
        $this->gitApiService = $gitApiService;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @inherit
     */
    public function toHtml()
    {
        $currentVersion = $this->dataHelper->getVersion();

        if (! version_compare($currentVersion, $this->getLatestVersion(), '<')) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return array|null
     */
    public function getReleases()
    {
        return $this->gitApiService->getReleasesList();
    }

    /**
     * @return string
     */
    public function getLatestVersion()
    {
        $latestReleaseData = $this->getLatestReleaseData();

        return str_replace("REL-", '', (string) $latestReleaseData['tag_name']);
    }

    /**
     * @param array $release
     * @return string
     */
    public function getVersion($release = [])
    {
        if (!array_key_exists('tag_name', $release)) {
            return '';
        }

        return (string) str_replace("REL-", '', (string) $release['tag_name']);
    }

    /**
     * @param array $release
     * @return string
     */
    public function getPublishedDate($release = [])
    {
        if (!array_key_exists('published_at', $release)) {
            return '';
        }

        $publishedDate = $release['published_at'];

        return (string) date('M d, Y', strtotime($publishedDate));
    }

    /**
     * @param array $release
     * @return string
     */
    public function getReleaseDescription($release = [])
    {
        if (!array_key_exists('body', $release)) {
            return '';
        }

        return str_ireplace(array("\r\n", "\n", "\r"), '<br />', $release['body']);;
    }

    /**
     * @param array $release
     * @return bool
     */
    public function isNewerVersion($release = [])
    {
        if (!array_key_exists('tag_name', $release)) {
            return false;
        }

        $currentVersion = $this->dataHelper->getVersion();
        $version = str_replace("REL-", '', (string) $release['tag_name']);

        return (bool) version_compare($version, $currentVersion, '>');
    }

    /**
     * @return array|null
     */
    public function getLatestReleaseData()
    {
        if (! $this->latestReleaseData) {
            $this->latestReleaseData = $this->gitApiService->getLatestRelease();
        }

        return $this->latestReleaseData;
    }
}
