<?php

namespace BrjupoEavAttributesOldWorkaround\CustomerAddress\Options;

use Psr\Log\LoggerInterface;

class DropdownOptions extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
//        $this->_options = [
//            ['label' => __('Select hour...'), 'value' => 'default'],
//            ['label' => __('Today from 12pm to 4pm'), 'value' => 'today__1200_1600'],
//            ['label' => __('Today from 4pm to 8pm'), 'value' => 'today__1600_2000'],
//            ['label' => __('Tomorrow from 12pm to 4pm'), 'value' => 'tomorrow__1200_1600'],
//            ['label' => __('Tomorrow from 4pm to 8pm'), 'value' => 'tomorrow__1600_2000']
//        ];
        $this->_options = [
            ['label' => __('A'), 'value' => 'a'],
            ['label' => __('B'), 'value' => 'b'],
            ['label' => __('C'), 'value' => 'c'],
            ['label' => __('D'), 'value' => 'd'],
            ['label' => __('E'), 'value' => 'e']
        ];
        return  $this->_options;
    }
}
