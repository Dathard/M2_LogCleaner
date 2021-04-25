<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    const MODULE_NAME = 'Dathard_LogCleaner';

    const REPOSITORY_URL = 'https://github.com/Dathard/M2_LogCleaner';

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return '1.0.0';
        return (string) $this->moduleList
            ->getOne(self::MODULE_NAME)['setup_version'];
    }
}
