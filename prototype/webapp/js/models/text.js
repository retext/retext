define([
    'remote',
    'models/base'
], function (Remote, BaseModel) {
    var Text = BaseModel.extend({
        urlRoot:Remote.apiUrlBase + 'text',
        defaults:{
            '@relations':[],
            id:null,
            project:null,
            container:null,
            name:null,
            order:1,
            type:null
        },
        validate:function (attrs) {
        }
    });
    return Text;
});