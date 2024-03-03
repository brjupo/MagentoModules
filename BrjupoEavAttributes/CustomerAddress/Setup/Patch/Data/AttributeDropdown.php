<?php

namespace Brjupo\CustomerAddress\Setup\Patch\Data;

/**
 * Adobe Commerce Docs - Default dependencies for Data Patch
 * https://developer.adobe.com/commerce/php/development/components/declarative-schema/patches/
 */

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Additional dependencies for this Data Patch
 */

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Indexer\Address\AttributeProvider;

use Brjupo\CustomerAddress\Model\CreateCustomerAddressAttribute;

class AttributeDropdown implements DataPatchInterface, PatchRevertableInterface
{
    const ATTRIBUTE_CODE = 'dropdown_18feb_2130';
    const SORT_ORDER = 20400;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    private CreateCustomerAddressAttribute $createCustomerAddressAttribute;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface       $moduleDataSetup,
        CustomerSetupFactory           $customerSetupFactory,
        CreateCustomerAddressAttribute $createCustomerAddressAttribute
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->createCustomerAddressAttribute = $createCustomerAddressAttribute;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $dropdownCustomCustomerAddressAttributeData = [];
        $dropdownCustomCustomerAddressAttributeData['serialized_options'] = $this->getSerializedOptions();
        $dropdownCustomCustomerAddressAttributeData['frontend_label'][0] = 'Feb18_Dropdown_2130';
        $dropdownCustomCustomerAddressAttributeData['attribute_code'] = self::ATTRIBUTE_CODE;
        $dropdownCustomCustomerAddressAttributeData['frontend_input'] = 'select';
        $dropdownCustomCustomerAddressAttributeData['is_required'] = false;

        $dropdownCustomCustomerAddressAttributeData['is_used_in_grid'] = false;
        $dropdownCustomCustomerAddressAttributeData['is_visible_in_grid'] = false;
        $dropdownCustomCustomerAddressAttributeData['is_searchable_in_grid'] = false;
        $dropdownCustomCustomerAddressAttributeData['grid_filter_condition_type'] = '0';
        $dropdownCustomCustomerAddressAttributeData['is_used_for_customer_segment'] = false;
        $dropdownCustomCustomerAddressAttributeData['is_visible'] = true;
        $dropdownCustomCustomerAddressAttributeData['sort_order'] = self::SORT_ORDER;
        $dropdownCustomCustomerAddressAttributeData['used_in_forms'] = [
            'customer_register_address',
            'customer_address_edit'
        ];

        $dropdownCustomCustomerAddressAttributeData['frontend_label'][1] = ''; //Default Store view
        $dropdownCustomCustomerAddressAttributeData['dropdown_attribute_validation'] = '';
        $dropdownCustomCustomerAddressAttributeData['dropdown_attribute_validation_unique'] = '';

        $this->createCustomerAddressAttribute->execute($dropdownCustomCustomerAddressAttributeData);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    private function getSerializedOptions()
    {
        /**
         * First line ->  option[order][option_0]=1&
         *  'option_0' is the first option displayed in admin options
         *      starts at zero
         *  '1' represents the ordinal number "first, second, third, fourth, fifth"
         *
         * Second line  ->  default[]=option_0&
         *  This line indicates the default option, If you want you make option_5 the default option you MUST delete this line here and add it in the option_5 section as "default[]=option_5&"
         *
         * Third line  ->  option[value][option_0][0]=default_option&
         *  [option_'n'][0] represents the option value
         *
         * Fourth line  ->  option[value][option_0][1]=Label at default store for default option&
         *  'Label at default store for default option' Will be displayed in the frontend
         *
         * For the third and fourth line, the HTML will be
         *  <option value="default_option">Label at default store for default option</option>
         *
         * NOTICE that the text [option_0] changes each time a new option is created
         */

        $encodedFields = [
            "option[order][option_0]=1&
            default[]=option_0&
            option[value][option_0][0]=default_option&
            option[value][option_0][1]=Label at default store for default option&
            option[delete][option_0]=",

            "option[order][option_1]=2&
            option[value][option_1][0]=second_option&
            option[value][option_1][1]=Label default store for second option&
            option[delete][option_1]=",

            "option[order][option_2]=3&
            option[value][option_2][0]=third_option&
            option[value][option_2][1]=Label default store for third option&
            option[delete][option_2]=",

            "option[order][option_3]=4&
            option[value][option_3][0]=fourth_option&
            option[value][option_3][1]=Label default store for fourth option&
            option[delete][option_3]=",

            "option[order][option_4]=5&
            option[value][option_4][0]=fifth_option&
            option[value][option_4][1]=Label default store for fifth option&
            option[delete][option_4]="
        ];

        $formData = [];
        foreach ($encodedFields as $item) {
            $item = str_replace('\n', '', $item);
            $item = str_replace('\r', '', $item);
            $item = preg_replace('!\s+!', ' ', $item);
            $decodedFieldData = [];
            parse_str($item, $decodedFieldData);
            $formData = array_replace_recursive($formData, $decodedFieldData);
        }

        return $formData;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Adobe Commerce and Magento Open Source DO NOT ALLOW YOU TO REVERT A PARTICULAR MODULE DATA PATCH. However, you can revert all composer installed or non-composer installed data patches using the module:uninstall command.
     * bin/magento module:uninstall --non-composer Vendor_ModuleName
     * bin/magento module:uninstall Vendor_ModuleName
     * https://developer.adobe.com/commerce/php/development/components/declarative-schema/patches/#reverting-data-patches
     *
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->removeAttribute(AttributeProvider::ENTITY, self::ATTRIBUTE_CODE);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
