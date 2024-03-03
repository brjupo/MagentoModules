define([
    'mage/utils/wrapper',
    'jquery',
    'domReady!'
], function (wrapper, $) {
    'use strict';

    return function (proceedToFunction) {
        return wrapper.wrap(proceedToFunction,
            function (originalFunction, payload) {
                // Your logic BEFORE original function works
                // add extended functionality here

                //Execute wrapped function
                originalFunction(payload);
                //Your logic AFTER original function works
                // add extended functionality here
                var distrito_envio_rapido = $('[name="custom_attributes[d1713]"]').val();
                if (typeof payload.addressInformation['extension_attributes'] != "undefined") {
                    payload.addressInformation['extension_attributes'] = {};
                }
                //To work, the field 'direccionestiendas_id' MUST BE DEFINED in extension_attributes.xml
                //For class Magento\Checkout\Api\Data\ShippingInformationInterface
                payload.addressInformation['extension_attributes']['direccionestiendas_id'] = parseInt(distrito_envio_rapido);
            });
    };
});
