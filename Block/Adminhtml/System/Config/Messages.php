<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Block\Adminhtml\System\Config;

class Messages extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/messages.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website')
            || $this->getRequest()->getParam('store') ? 5 : 4;

        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }
}
