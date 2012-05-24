define([
    'models/menu/item'
], function (MenuItem) {
    var MenuItemCollection = Backbone.Collection.extend({
        'model':MenuItem
    });
    return MenuItemCollection;
});