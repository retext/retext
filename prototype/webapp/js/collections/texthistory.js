define([
    'models/textversion'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
