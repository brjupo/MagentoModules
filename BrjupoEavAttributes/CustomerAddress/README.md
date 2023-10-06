# BrjupoEavAttributes_CustomerAddress module

This module create a new address attribute this is saved in quote and then in order

This attribute is NOT saved as an address, this attribute CANNOT be preserved in address forms

The relation between Data Patch, Plugin, db_schema, extension_attributes is:

1. Create the customer_address_attribute using **data patch**, with code 'd1713'
2. Create the **payload-extender-mixin.js** and the **requirejs-config.js**.
3. In the extender, the value is read using jQuery selector.
    1. $('[name="custom_attributes[d1713]"]').val();
4. Also in extender the value is sent using 'direccionestiendas_id'
    1. payload.addressInformation['extension_attributes']['direccionestiendas_id'] = parseInt(distrito_envio_rapido);
5. To use this extension_attribute name 'direccionestiendas_id', the attribute MUST be created in
   **extension_attributes.xml**
   ```xml
   <extension_attributes for="Magento\Checkout\Api\Data\ShippingInformationInterface">
      <attribute code="direccionestiendas_id" type="int"/>
   </extension_attributes>
   ```
6. To save this value in quote, you MUST create a column in quote table using **db_schema.xml**
    ```xml
    <table name="quote" resource="checkout" comment="Sales Flat Quote">
        <column xsi:type="int" name="direccionestiendas_id" unsigned="true" nullable="true"
                comment="Save direccionestiendas_id in quote"/>
    </table>
    ```

7. With the extension_attribute defined as 'direccionestiendas_id', and the quote table column name as '
   direccionestiendas_id', now you can use it in the **PHP plugin**, with **camelCase!**. This plugin intersects data of
   endpoint /V1/carts/mine/shipping-information OR /V1/guest-carts/:cartId/shipping-information
    ```php
    $extAttributes = $addressInformation->getExtensionAttributes();
    $idDireccionestiendas = $extAttributes->getDireccionestiendasId();
    $quote->setDireccionestiendasId($idDireccionestiendas);
    ```

8. Create columns for sales_order and sales_order_grid in db_schema.xml
    ```xml
    <table name="sales_order" resource="sales" comment="Sales Flat Order">
        <column xsi:type="text" name="direcciones_tiendas" nullable="true" comment="Save direccionestiendas as sent to Savar in order"/>
    </table>
    <table name="sales_order_grid" resource="sales" comment="Sales Flat Order Grid">
        <column xsi:type="text" name="direcciones_tiendas" nullable="true" comment="Save direccionestiendas as sent to Savar in order grid"/>
    </table>
    ```

9. Create observer to save quote field *direccionestiendas_id* in sales_order
    ```xml
    <event name="sales_model_service_quote_submit_before">
        <observer name="custom_fields_sales_address_save" instance="BrjupoEavAttributes\CustomerAddress\Observer\SaveCustomFieldsInOrder" />
    </event>
    ```
    ```php
    $order = $observer->getEvent()->getOrder();
    $quote = $observer->getEvent()->getQuote();
    
    $value = "default";
    if (intval($quote->getDireccionestiendasId()) == 234) {
        $value = "It's the number 234";
    }
    $order->setData('direcciones_tiendas', $value);
    ```

10. Create a di.xml to save info from sales_order to sales_order_grid
    ```xml
    <virtualType name="Magento\Sales\Model\ResourceModel\Order\Grid" type="Magento\Sales\Model\ResourceModel\Grid">
        <arguments>
            <argument name="columns" xsi:type="array">
                <item name="direcciones_tiendas" xsi:type="string">sales_order.direcciones_tiendas</item>
            </argument>
        </arguments>
    </virtualType>
    ```


11. Create sales_order_grid.xml to show the new attribute in Admin > Sales Orders. 
    1. BrjupoEavAttributes/CustomerAddress/view/adminhtml/ui_component/sales_order_grid.xml

    ```xml
    <listing xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Ui:etc/ui_configuration.xsd">
        <columns name="sales_order_columns">
            <column name="direcciones_tiendas">
                <argument name="data" xsi:type="array">
                    <item name="config" xsi:type="array">
                        <item name="filter" xsi:type="string">textRange</item>
                        <item name="bodyTmpl" xsi:type="string">ui/grid/cells/html</item>
                        <item name="label" xsi:type="string" translate="true">Dirección Tienda a Savar</item>
                    </item>
                </argument>
            </column>
        </columns>
    </listing>
    ```




Tested in:

- Magento 2 version 2.4.5-p1

Thanks to:

- Jeff Yu http://techjeffyu.com/blog/magento-2-add-a-custom-field-to-checkout-shipping
- MageAnts https://www.mageants.com/blog/how-to-create-order-attribute-programmatically-in-magento-2.html
