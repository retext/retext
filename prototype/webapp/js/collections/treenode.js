define([
    'models/treenode'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
