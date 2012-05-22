define([
], function () {
    var Register = Backbone.Model.extend({
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('email')) return 'missing_email';
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            if (attrs.email.length < 6 || reg.test(attrs.email) == false) return 'email_invalid';
        }
    });
    return Register;
});