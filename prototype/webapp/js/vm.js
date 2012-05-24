define([
    'models/menuitem',
    'collections/menuitem'
], function (MenuItem, MenuItemCollection) {
    var pages = {};
    _.each(['login', 'about'], function (pageId) {
        pages[pageId] = new Backbone.View({'el':$('#' + pageId), 'id':pageId});
    });

    var menuItems = new MenuItemCollection();
    var projectMenuItem = new MenuItem({'id':'projects', 'label':'Projekte', 'children': [new MenuItem({'id':'project-new', 'label':'Neues Projekt…'})]});
    menuItems.add(projectMenuItem);
    _.each([
        ['login', 'Anmelden'],
        ['register', 'Registrieren'],
        ['status', 'Status'],
    ], function (menuItem) {
        menuItems.add(new MenuItem({'id':menuItem[0], 'label':menuItem[1]}));
    });
    return {
        pages:pages,
        menuItems:menuItems
    };
});