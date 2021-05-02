<?php
declare(strict_types=1);

namespace Dathard\LogCleaner\Model\Config\Source\DbLogs;

class Period implements \Magento\Framework\Option\ArrayInterface
{
    const ONCE_A_DAY = 0;
    const ONCE_A_WEEK = 1;
    const ONCE_A_MONTH = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ONCE_A_DAY, 'label' => __('Once a day')],
            ['value' => self::ONCE_A_WEEK, 'label' => __('Once a week')],
            ['value' => self::ONCE_A_MONTH, 'label' => __('Once a month')]
        ];
    }
}
