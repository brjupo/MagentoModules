define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        // Instead of declaration in PHTML file, you can add the HTML template here
        // defaults: {
        //     template: 'BrjupoKo_BasicConfigUsingAlias/my-template'
        // },
        initialize: function () {
            this._super();
            this.message = 'Hi from JS component. This is a knockout template using an JS alias in requirejs-config';
            return this;
        }
    });
});
