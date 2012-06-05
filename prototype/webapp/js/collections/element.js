define([
    'models/container'
], function (Container) {
    var ContainerCollection = Backbone.Collection.extend({
        model:Container
    });
    return ContainerCollection;
});