<?php

namespace BrjupoEavAttributes\CustomerAddress\Setup\Patch\Data;

/**
 * Adobe Commerce Docs - Default dependencies for Data Patch
 * https://developer.adobe.com/commerce/php/development/components/declarative-schema/patches/
 */

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Additional dependencies for this Data Patch
 */

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Indexer\Address\AttributeProvider;


class DropdownAttribute implements DataPatchInterface, PatchRevertableInterface
{
    const ATTRIBUTE_CODE = 'distrito_envio_rapido';
    const SORT_ORDER = 3000;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory     $customerSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute(AttributeProvider::ENTITY, self::ATTRIBUTE_CODE, [
            'label' => 'Distrito',
            'input' => 'select',
            'required' => false,
            'is_used_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            //'grid_filter_condition_type' => 0,
            'is_used_for_customer_segment' => false,
            'visible' => true,
            'sort_order' => self::SORT_ORDER,
            'position' => self::SORT_ORDER,
            'user_defined' => true,
            'system' => 0,
        ]);


        $attribute = $customerSetup->getEavConfig()->getAttribute(AttributeProvider::ENTITY, self::ATTRIBUTE_CODE);

        $attribute->setData('used_in_forms', [
            //'adminhtml_customer',
            'adminhtml_checkout',
            'adminhtml_customer_address',
            //'customer_account_create',
            //'customer_account_edit',
            'customer_address_edit',
            'customer_register_address',
        ]);

        $attribute->save();

        $attributeId = $customerSetup->getAttributeId(AttributeProvider::ENTITY, self::ATTRIBUTE_CODE);

        $options = [
            'depto1 | prov1 | dist1',
            'depto2 | prov2 | dist2',
            'depto3 | prov3 | dist3',
        ];

        $customerSetup->addAttributeOption([
            'values' => $options,
            'attribute_id' => $attributeId
        ]);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
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
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Here should go code that will revert all operations from `apply` method
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->removeAttribute(AttributeProvider::ENTITY, self::ATTRIBUTE_CODE);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
