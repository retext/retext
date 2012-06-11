define([
    'models/comment'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
