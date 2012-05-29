define([
    'remote'
], function (Remote) {
    var Auth = Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'auth',
        defaults:{
            authorized:false
        }
    });
    return Auth;
});