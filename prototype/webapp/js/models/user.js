define([
], function () {
    var User = Backbone.Model.extend({
        defaults:{
            authenticated:false
        }
    });
    return User;
});