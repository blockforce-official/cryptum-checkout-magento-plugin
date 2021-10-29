<?php
namespace Cryptum\Cryptum\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface {

    /**
     * Possible environment types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => 'Test',
            ],
            [
                'value' => 1,
                'label' => 'Production',
            ]
        ];
    }
}
