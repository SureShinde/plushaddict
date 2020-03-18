<?php

namespace Dapreport\Wholesalereport\Model;

class Status
{
    /**#@+
     * Status values
     */

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$FormKey = $objectManager->get('Magento\Framework\Data\Form\FormKey');
		$attribute_manager = $objectManager->create('\Magento\Eav\Model\Config');
		
		$attributeOptions = $attribute_manager->getAttribute('catalog_product', 'wholesaler')->getSource()->getAllOptions();
        array_shift($attributeOptions);
        return $attributeOptions;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $option['value'], 'label' => $option['label']];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}