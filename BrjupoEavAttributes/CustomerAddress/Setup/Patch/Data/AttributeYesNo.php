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
class AttributeYesNo implements DataPatchInterface, PatchRevertableInterface
{
    const ATTRIBUTE_CODE = 'yesno_programmatically_magento_ee_245';
    const SORT_ORDER = 50010;
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

        $yesnoCustomCustomerAddressAttributeData = [];
        $yesnoCustomCustomerAddressAttributeData['frontend_label'][0] = 'Yes No programmatically Magento EE 245';
        $yesnoCustomCustomerAddressAttributeData['attribute_code'] = self::ATTRIBUTE_CODE;
        $yesnoCustomCustomerAddressAttributeData['frontend_input'] = 'boolean';
        $yesnoCustomCustomerAddressAttributeData['is_required'] = false;
        $yesnoCustomCustomerAddressAttributeData['default_value_yesno'] = 0;

        $yesnoCustomCustomerAddressAttributeData['input_validation'] = '';

        $yesnoCustomCustomerAddressAttributeData['input_filter'] = '';
        $yesnoCustomCustomerAddressAttributeData['is_used_in_grid'] = false;
        $yesnoCustomCustomerAddressAttributeData['is_visible_in_grid'] = false;
        $yesnoCustomCustomerAddressAttributeData['is_filterable_in_grid'] = false;
        $yesnoCustomCustomerAddressAttributeData['is_searchable_in_grid'] = false;
        $yesnoCustomCustomerAddressAttributeData['grid_filter_condition_type'] = '0';
        $yesnoCustomCustomerAddressAttributeData['is_used_for_customer_segment'] = false;
        $yesnoCustomCustomerAddressAttributeData['is_visible'] = true;
        $yesnoCustomCustomerAddressAttributeData['sort_order'] = self::SORT_ORDER;
        $yesnoCustomCustomerAddressAttributeData['used_in_forms'] = [
            'customer_register_address',
            'customer_address_edit'
        ];

        $yesnoCustomCustomerAddressAttributeData['frontend_label'][1] = 'Title Ejemplo YesNo'; //Default Store view

        $this->createCustomerAddressAttribute->execute($yesnoCustomCustomerAddressAttributeData);

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
