/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote'
], function (Remote) {
    var Register = Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'user',
        defaults:{
            email:null
        },
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('email')) return 'missing_email';
            if (attrs.email.length < 6) return 'email_invalid';
        }
    });
    return Register;
});