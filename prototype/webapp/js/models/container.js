define([
    'remote'
], function (Remote) {
    var Container = Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'container',
        defaults:{
            id:null,
            project:null,
            name:null,
            parent:null,
            childcount:0
        },
        validate:function (attrs) {
        }
    });
    return Container;
});