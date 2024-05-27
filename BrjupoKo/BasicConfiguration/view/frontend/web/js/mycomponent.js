define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        // Instead of declaration in layout XML file, you can add the HTML template here
        // defaults: {
        //     template: 'BrjupoKo_BasicConfiguration/mytemplate'
        // },
        initialize: function () {
            this._super();
            this.message = 'Hi from JS component. This is a knockout template using an XML layout';
        }
    });
});
