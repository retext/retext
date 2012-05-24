define([
], function (MenuItemCollection) {
    var MenuItem = Backbone.Model.extend({
        defaults:{
            active:false,
            children: [],
            icon:false
        }
    });
    return MenuItem;
});