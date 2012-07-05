/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    return ElementModel.extend({
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
            showText:null,
            showLanguage:null,
            commentCount:null,
            spellingApproved:false,
            contentApproved:false,
            approved:false,
            approvedCount:0
        },
        parse:function (response) {
            if (_.has(response, 'text') && _.isEmpty(response.text)) response.text = null;
            return response;
        }
    });
});
