define([
], function (Remote) {
    return Backbone.Model.extend({
        defaults:{
            project:null,
            parent:null,
            text:null,
            createdAt:null
        }
    });
});
