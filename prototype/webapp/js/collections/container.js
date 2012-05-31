define([
    'models/container'
], function (Container) {
    var ContainerCollection = Backbone.Collection.extend({
        model:Container,
        initialize:function (options) {
            this.url = options.project.url() + '/container';
        }
    });
    return ContainerCollection;
});