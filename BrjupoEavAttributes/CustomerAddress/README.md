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
   direccionestiendas_id', now you can use it in the **PHP plugin**, with **camelCase!**
    ```php
    $extAttributes = $addressInformation->getExtensionAttributes();
    $idDireccionestiendas = $extAttributes->getDireccionestiendasId();
    $quote->setDireccionestiendasId($idDireccionestiendas);
    ```
    
    
