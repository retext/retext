define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    var Text = ElementModel.extend({
        urlRoot:Remote.apiUrlBase + 'text',
        defaults:{
            '@relations':null,
            '@subject':null,
            id:null,
            project:null,
            parent:null,
            name:null,
            type:null
        }
    });
    return Text;
});