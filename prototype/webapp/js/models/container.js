define([
], function () {
    var Container = Backbone.Model.extend({
        defaults:{
            id:null,
            name:null,
            parent:null,
            childcount:0
        },
        validate:function (attrs) {
        }
    });
    return Container;
});