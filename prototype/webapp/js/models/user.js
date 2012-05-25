define([
    'remote'
], function (Remote) {
    var User = Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'login',
        defaults:{
            email:'',
            password:'',
            authenticated:false
        },
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('email')) return 'missing_email';
            if (attrs.email.length < 6) return 'email_invalid';
            if (!attrs.hasOwnProperty('password')) return 'missing_password';
            if (attrs.password.length < 8) return 'password_invalid';
        }
    });
    return User;
});