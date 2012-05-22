define([
    'models/menuitem',
    'collections/menuitem'
], function (MenuItem, MenuItemCollection) {
    var pages = {};
    _.each(['login', 'about'], function (pageId) {
        pages[pageId] = new Backbone.View({'el':$('#' + pageId), 'id':pageId});
    });

    var menuItems = new MenuItemCollection();
    _.each([
        ['login', 'Anmelden'],
        ['register', 'Registrieren'],
        ['about', 'Über'],
        ['status', 'Status']
    ], function (menuItem) {
        menuItems.add(new MenuItem({'id':menuItem[0], 'label':menuItem[1]}));
    });
    return {
        pages:pages,
        menuItems:menuItems
    };
});