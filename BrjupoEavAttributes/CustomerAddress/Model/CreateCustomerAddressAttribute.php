<?php

namespace BrjupoEavAttributes\CustomerAddress\Model;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\AttributeFactory;
use Magento\CustomerCustomAttributes\Helper\Address as HelperAddress;
use Magento\CustomerCustomAttributes\Helper\Data as HelperData;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Type as EavEntityType;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavEntityAttribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\WebsiteFactory;
use Psr\Log\LoggerInterface;


/**
 * This class will work as main functionality to create Custom Customer Address Attributes programmatically
 * Taking native class as example, this class is found when we save an attribute manually in Admin
 * This "execute" method, now will return a void or exception INSTEAD of redirect as the native class does
 * Also will receive $data as params INSTEAD of a request->getPostValue()
 *
 * Code copied from
 * module-customer-custom-attributes/Controller/Adminhtml/Customer/Address/Attribute/Save.php
 */
class CreateCustomerAddressAttribute
{
    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var SetFactory
     */
    protected $_attrSetFactory;

    /**
     * @var WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var LoggerInterface
     */
    protected  $logger;

    /**
     * @var EavEntityAttribute
     */
    protected  $eavAttribute;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperAddress
     */
    protected $helperAddress;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /** ------------------ CLASS VARIABLES ------------------ */

    /**
     * @var array
     */
    private $deniedAttributes = [];

    /**
     * Customer Address Entity Type instance
     *
     * @var EavEntityType
     */
    protected $_entityType;

    /**
     * @param Config $eavConfig
     * @param AttributeFactory $attrFactory
     * @param SetFactory $attrSetFactory
     * @param WebsiteFactory $websiteFactory
     * @param LoggerInterface $logger
     * @param EavEntityAttribute $eavAttribute
     * @param HelperData $helperData
     * @param HelperAddress $helperAddress
     * @param FilterManager $filterManager
     * @param array $deniedAttributes
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config             $eavConfig,
        AttributeFactory   $attrFactory,
        SetFactory         $attrSetFactory,
        WebsiteFactory     $websiteFactory,
        LoggerInterface    $logger,
        EavEntityAttribute $eavAttribute,
        HelperData         $helperData,
        HelperAddress      $helperAddress,
        FilterManager      $filterManager,
        array              $deniedAttributes = []
    )
    {
        $this->_eavConfig = $eavConfig;
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        $this->websiteFactory = $websiteFactory;
        $this->logger = $logger;
        $this->eavAttribute = $eavAttribute;

        $this->helperData = $helperData;
        $this->helperAddress = $helperAddress;
        $this->filterManager = $filterManager;

        $this->deniedAttributes = $deniedAttributes;
    }

    /**
     * Save attribute action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws LocalizedException
     */
    public function execute($data)
    {
        if (!$data) {
            throw new LocalizedException(__('Data is NOT provided'));
            return;
        }

        // In the data patch BrjupoEavAttributes/CustomerAddress/Setup/Patch/Data/AttributeDropdown.php
        // The serialized_options has been unsearealized for better human reading
        // If you want to understand the Post data unserialized, check vendor file
        // module-customer-custom-attributes/Controller/Adminhtml/Customer/Address/Attribute/Save.php
        try {
            if (isset($data['serialized_options'])) {
                $optionData = $data['serialized_options'];
            } else {
                $data['serialized_options'] = '[]';
            }
        } catch (\InvalidArgumentException $e) {
            throw new LocalizedException(__("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later. " . $e->getMessage()));
            return;
        }

        $data = array_replace_recursive(
            $data,
            $optionData
        );

        /* @var $attributeObject \Magento\Customer\Model\Attribute */
        $attributeObject = $this->_initAttribute();

        //filtering
        try {
            $data = $this->helperAddress->filterPostData($data);
        } catch (LocalizedException $e) {
            throw new LocalizedException($e->getMessage());
            return;
        }

        //AddressMetadataInterface
        $attributeId = $this->eavAttribute->getIdByCode(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $data['attribute_code']);

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || array_key_exists('backend_model', $data)
            ) {
                throw new LocalizedException(__('You cannot edit this attribute. You cannot change this entity type. This attribute entity type is different from "customer_address"'));
                return;
            }

            $data['attribute_code'] = $attributeObject->getAttributeCode();
            $data['is_user_defined'] = $attributeObject->getIsUserDefined();
            $data['frontend_input'] = $attributeObject->getFrontendInput();
            $data['is_user_defined'] = $attributeObject->getIsUserDefined();
            $data['is_system'] = $attributeObject->getIsSystem();
        } else {
            /** ------------------------- START - IF ATTRIBUTE DOES NOT EXIST ------------------------- */
            $data['backend_model'] = $this->helperData->getAttributeBackendModelByInputType(
                $data['frontend_input']
            );
            $data['source_model'] = $this->helperData->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_type'] = $this->helperData->getAttributeBackendTypeByInputType($data['frontend_input']);
            $data['is_user_defined'] = 1;
            $data['is_system'] = 0;

            // add set and group info
            $data['attribute_set_id'] = $this->_getEntityType()->getDefaultAttributeSetId();
            /** @var $attrSet \Magento\Eav\Model\Entity\Attribute\Set */
            $attrSet = $this->_attrSetFactory->create();
            $data['attribute_group_id'] = $attrSet->getDefaultGroupId($data['attribute_set_id']);
            /** ------------------------- END - IF ATTRIBUTE DOES NOT EXIST ------------------------- */
        }

        //Always assign the default form
        if (!isset($data['used_in_forms_disabled'])) {
            $defaultFormName = 'adminhtml_customer_address';
            if (empty($data['used_in_forms'])) {
                $data['used_in_forms'] = [$defaultFormName];
            } elseif (is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = $defaultFormName;
            }
        }

        $defaultValueField = $this->helperData->getAttributeDefaultValueByInput($data['frontend_input']);
        if ($defaultValueField) {
            $scopeKeyPrefix = '';
            if (isset($data['website']) && $data['website']) {
                $scopeKeyPrefix = 'scope_';
            }
            if (isset($data[$scopeKeyPrefix . $defaultValueField])) {
                $defaultValue = $data[$scopeKeyPrefix . $defaultValueField];
                $data[$scopeKeyPrefix . 'default_value'] = $defaultValue
                    ? $this->filterManager->stripTags($defaultValue) : null;
            } else {
                $data[$scopeKeyPrefix . 'default_value'] = null;
            }
        }

        $data['entity_type_id'] = $this->_getEntityType()->getId();
        $data['validate_rules'] = $this->helperData->getAttributeValidateRules($data['frontend_input'], $data);

        $validateRulesErrors = $this->helperData->checkValidateRules(
            $data['frontend_input'],
            $data['validate_rules']
        );
        if (count($validateRulesErrors)) {
            $allErrors = '';
            foreach ($validateRulesErrors as $message) {
                $allErrors = $allErrors . ' ' . $message;
            }
            throw new LocalizedException($allErrors);
            return;
        }

        $attributeObject->addData($data);

        /**
         * Check "Use Default Value" checkboxes values
         */
        if (isset($data['use_default'])) {
            $useDefaults = $data['use_default'];
            if ($useDefaults) {
                foreach ($useDefaults as $key) {
                    if (!is_array($key) && !in_array($key, $this->deniedAttributes)) {
                        $attributeObject->setData($key, null);
                    }
                }
            }
        }

        try {
            $frontendLabel = $attributeObject->getDataByKey(AttributeInterface::FRONTEND_LABEL);
            $attributeObject->save();

            if ($this->isRegionAttribute($attributeObject)) {
                $this->setRegionIdFrontendLabel($frontendLabel);
            }
            $this->logger->info(__('You saved the customer address attribute.'));
            return;
        } catch (LocalizedException $e) {
            throw new LocalizedException($e->getMessage());
            return;
        } catch (\Exception $e) {
            throw new LocalizedException($e->getMessage());
            return;
        }
    }

    /**
     * Sets RegionId frontend label equal to Region frontend label.
     *
     * RegionId is hidden frontend input attribute and isn't available for updating via admin panel,
     * but frontend label of this attribute is visible in address forms as Region label.
     * So frontend label for RegionId should be synced with frontend label for Region attribute, which is
     * available for updating.
     *
     * @param array|string $frontendLabel
     * return void
     * @throws LocalizedException
     */
    private function setRegionIdFrontendLabel($frontendLabel): void
    {
        $attributeRegionId = $this->_initAttribute();
        $attributeRegionId->loadByCode($this->_getEntityType(), AddressInterface::REGION_ID);
        if ($attributeRegionId->getId()) {
            $attributeRegionId->setData(AttributeInterface::FRONTEND_LABEL, $frontendLabel);
            $attributeRegionId->save();
        }
    }

    /**
     * Check if attribute is a region.
     *
     * @param \Magento\Customer\Model\Attribute $attributeObject
     * @return bool
     */
    private function isRegionAttribute(\Magento\Customer\Model\Attribute $attributeObject): bool
    {
        return $attributeObject->getAttributeCode() === AddressInterface::REGION;
    }

    /**
     * Retrieve customer attribute object
     * Function copied from module-customer-custom-attributes/Controller/Adminhtml/Customer/Address/Attribute.php
     *
     * @return \Magento\Customer\Model\Attribute
     */
    private function _initAttribute($dataWebsite = null)
    {
        /** @var $attribute \Magento\Customer\Model\Attribute */
        $attribute = $this->_attrFactory->create();
        $website = $dataWebsite ?: $this->websiteFactory->create();
        $attribute->setWebsite($website);
        return $attribute;
    }

    /**
     * Return Customer Address Entity Type instance
     * Function copied from module-customer-custom-attributes/Controller/Adminhtml/Customer/Address/Attribute.php
     *
     * @return EavEntityType
     */
    private function _getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = $this->_eavConfig->getEntityType('customer_address');
        }
        return $this->_entityType;
    }
}
