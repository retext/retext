/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'validateerror'
], function (Remote, ValidateError) {
    var User = Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'login',
        defaults:{
            email:'',
            password:'',
            authenticated:false
        },
        validate:function (attrs) {
            var errors = {
                email:ValidateError.create(),
                password:ValidateError.create()
            };
            if (!attrs.hasOwnProperty('email')) {
                errors.email.isMissing();
            } else if (attrs.email.length < 6) {
                errors.email.isInvalid('must be at least 6 characters long.');
            }
            if (!attrs.hasOwnProperty('password')) {
                errors.password.isMissing();
            } else if (attrs.password.length < 8) {
                errors.password.isInvalid('must be at least 8 characters long.');
            }
            return errors.email.error || errors.password.error ? errors : null;
        }
    });
    return User;
});