define([
    'uiComponent',
    'ko',
    'mage/storage'
], function (Component, ko, storage) {
    'use strict';

    return Component.extend({
        // Remember the 'template' word is reserved for template location
        defaults: {
            myVisibleHiddenVariable: true,
            myTextVariable: 'This is my text from my-component.js',
            myHtmlVariable: "<em>For further details, view the report <a href='report.html'>here</a>.</em>",
            myClassVariable: 'my-custom--class',
            myCssVariable: true,
            myStyleVariable: 'lightblue',
            url: ("year-end.html"),
            details: ("Report including final year-end statistics"),
            people: [
                {firstName: 'Bert', lastName: 'Bertington'},
                {firstName: 'Charles', lastName: 'Charlesforth'},
                {firstName: 'Denise', lastName: 'Dentiste'}
            ],
            qty: 12,
            radius: 5,
            numberOfClicks: ko.observable(0),
            userData: ko.observable("")
        },
        initialize: function () {
            this._super();
            this.message = 'Hi from JS component. This is a knockout template using an JS alias in requirejs-config';
            return this;
        },
        incrementClickCounter: function () {
            var previousCount = this.numberOfClicks();
            this.numberOfClicks(previousCount + 1);
            //You can also get information from a GET Ajax
            // storage.get('url/to/api/endpoint')
            //     .done(response => {
            //         console.log(response)
            //     })
            //     .fail(() => {
            //         console.log('Product NOT found');
            //     })
        }
    });
});
