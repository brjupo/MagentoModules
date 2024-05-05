define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BrjupoKo_BasicConfigUsingAlias/my-template'
        },
        initialize: function () {
            this._super();
            this.message = 'Hi from JS component. This is a knockout template using an JS alias in requirejs-config';
            return this;
        }
    });
});
