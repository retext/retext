define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    var Container = ElementModel.extend({
        urlRoot:Remote.apiUrlBase + 'container',
        defaults:{
            '@relations':[],
            id:null,
            project:null,
            name:null,
            parent:null,
            childCount:0
        },
        validate:function (attrs) {
        }
    });
    return Container;
});