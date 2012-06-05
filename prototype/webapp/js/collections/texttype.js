define([
    'models/texttype'
], function (Model) {
    var Collection = Backbone.Collection.extend({
        model:Model
    });
    return Collection;
});