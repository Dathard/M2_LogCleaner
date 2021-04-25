<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Controller\Adminhtml\Module;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;

class Notifications extends Action
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    public $layoutFactory;

    /**
     * Notifications constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $result = [
            'html' => $this->layoutFactory->create()
                ->createBlock(\Dathard\LogCleaner\Block\Adminhtml\System\Config\Notifications\Version::class)
                ->toHtml()
        ];

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
