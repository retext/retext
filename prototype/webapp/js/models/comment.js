define([
], function () {
    return Backbone.Model.extend({
        defaults:{
            project:null,
            text:null,
            user:null,
            comment:null,
            createdAt:null
        }
    });
});
