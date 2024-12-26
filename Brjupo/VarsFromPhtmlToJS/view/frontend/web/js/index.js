define([], function () {
    return function (config, $element) {
        console.log(config);
        console.log(config.foo);
        console.log($element);
    };
});
