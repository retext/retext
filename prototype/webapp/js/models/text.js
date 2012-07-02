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
            identifier:null,
            project:null,
            parent:null,
            name:null,
            type:null,
            typeData:{name:null, fontsize:null, fontname:null},
            text:null,
            commentCount:null,
            spellingApproved:false,
            contentApproved:false,
            approved:false,
            approvedCount:0
        }
    });
    return Text;
});
