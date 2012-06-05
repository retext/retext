define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    var Text = ElementModel.extend({
        urlRoot:Remote.apiUrlBase + 'text',
        defaults:{
            '@relations':[],
            id:null,
            project:null,
            parent:null,
            name:null,
            type:null
        },
        validate:function (attrs) {
        }
    });
    return Text;
});