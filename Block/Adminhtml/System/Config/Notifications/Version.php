<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Block\Adminhtml\System\Config\Notifications;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Dathard\LogCleaner\Service\GitApiService;
use Dathard\LogCleaner\Helper\Data;

class Version extends Template
{
    /**
     * @var string
     */
    protected $_template = 'system/config/notifications/version.phtml';

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
     * @return string
     */
    public function getLatestVersion()
    {
        $latestReleaseData = $this->getLatestReleaseData();

        return str_replace("REL-", '', (string) $latestReleaseData['tag_name']);
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

    /**
     * @return string
     */
    public function getRepositoryUrl()
    {
        return Data::REPOSITORY_URL;
    }
}
