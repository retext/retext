define([
], function () {
    var Relation = Backbone.Model.extend({
        defaults:{
            '@context':null,
            relatedcontext:null,
            role:null,
            href:null,
            list:false
        }
    });
    return Relation;
});