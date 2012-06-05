define([
    'remote',
    'models/base'
], function (Remote, BaseModel) {
    var Container = BaseModel.extend({
        urlRoot:Remote.apiUrlBase + 'container',
        defaults:{
            '@relations':[],
            id:null,
            project:null,
            name:null,
            parent:null,
            order:1,
            childCount:0
        },
        validate:function (attrs) {
        }
    });
    return Container;
});