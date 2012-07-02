define([
    'models/projectlanguage',
    'remote'
], function (Model, Remote) {
    return Backbone.Collection.extend({
        model:Model
    });
});