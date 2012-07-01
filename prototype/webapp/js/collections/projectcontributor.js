define([
    'models/projectcontributor',
    'remote'
], function (Model, Remote) {
    return Backbone.Collection.extend({
        model:Model
    });
});