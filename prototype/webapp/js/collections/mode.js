define([
    'models/mode'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
