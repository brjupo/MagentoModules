<?php

namespace BrjupoEavAttributes\CustomerAddress\Setup\Patch\Data;

use BrjupoEavAttributes\CustomerAddress\Model\CreateCustomerAddressAttribute;
use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Adobe Commerce Docs - Default dependencies for Data Patch
 * https://developer.adobe.com/commerce/php/development/components/declarative-schema/patches/
 */

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * This Data Patch creates a Customer Custom Address Text attribute
 * It send data to model to create the attribute
 */
class AttributeText implements DataPatchInterface, PatchRevertableInterface
{
    const ATTRIBUTE_CODE = 'text_programmatically_magento_ee_245';
    const SORT_ORDER = 20000;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var CreateCustomerAddressAttribute
     */
    private $createCustomerAddressAttribute;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param CreateCustomerAddressAttribute $createCustomerAddressAttribute
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
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $textCustomCustomerAddressAttributeData = [];
        $textCustomCustomerAddressAttributeData['frontend_label'][0] = 'Text programmatically Magento EE 245';
        $textCustomCustomerAddressAttributeData['attribute_code'] = self::ATTRIBUTE_CODE;
        $textCustomCustomerAddressAttributeData['frontend_input'] = 'text';
        $textCustomCustomerAddressAttributeData['is_required'] = false;
        $textCustomCustomerAddressAttributeData['default_value_text'] = '';

        $textCustomCustomerAddressAttributeData['input_validation'] = '';
        $textCustomCustomerAddressAttributeData['min_text_length'] = '';
        $textCustomCustomerAddressAttributeData['max_text_length'] = '';

        $textCustomCustomerAddressAttributeData['input_filter'] = '';
        $textCustomCustomerAddressAttributeData['is_used_in_grid'] = false;
        $textCustomCustomerAddressAttributeData['is_visible_in_grid'] = false;
        $textCustomCustomerAddressAttributeData['is_searchable_in_grid'] = false;
        $textCustomCustomerAddressAttributeData['grid_filter_condition_type'] = '0';
        $textCustomCustomerAddressAttributeData['is_used_for_customer_segment'] = false;
        $textCustomCustomerAddressAttributeData['is_visible'] = true;
        $textCustomCustomerAddressAttributeData['sort_order'] = self::SORT_ORDER;
        $textCustomCustomerAddressAttributeData['used_in_forms'] = [
            'customer_register_address',
            'customer_address_edit'
        ];

        $textCustomCustomerAddressAttributeData['frontend_label'][1] = ''; //Default Store view

        $this->createCustomerAddressAttribute->execute($textCustomCustomerAddressAttributeData);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Adobe Commerce and Magento Open Source DO NOT ALLOW YOU TO REVERT A PARTICULAR MODULE DATA PATCH.
     * However, you can revert all composer installed or non-composer installed data patches using
     *   the module:uninstall command.
     * bin/magento module:uninstall --non-composer Vendor_ModuleName
     * bin/magento module:uninstall Vendor_ModuleName
     * https://developer.adobe.com/commerce/php/development/components/declarative-schema/patches/#reverting-data-patches
     *
     * @return void
     */
    public function revert(): void
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
    public function getAliases(): array
    {
        return [];
    }
}
