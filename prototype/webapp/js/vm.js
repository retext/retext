define([
    'models/menu/group',
    'collections/menu/group',
    'models/menu/item',
    'collections/menu/item'
], function (MenuGroup, MenuGroupCollection, MenuItem, MenuItemCollection) {
    var pages = {};
    _.each(['login', 'about'], function (pageId) {
        pages[pageId] = new Backbone.View({'el':$('#' + pageId), 'id':pageId});
    });

    var leftMenuItems = new MenuItemCollection();
    var projectMenuItem = new MenuItem({'id':'projects', 'label':'Projekte', 'children': [new MenuItem({'id':'project-new', 'label':'Neues Projekt…'})]});
    leftMenuItems.add(projectMenuItem);
    leftMenuItems.add(new MenuItem({'id':'register', 'label':'Registrieren'}));
    leftMenuItems.add(new MenuItem({'id':'status', 'label':'Status'}));

    var rightMenuItems = new MenuItemCollection();
    rightMenuItems.add(new MenuItem({'id':'login', 'label':'Anmelden', 'align': 'right', 'icon': 'icon-user icon-white'}));

    var leftGroup = new MenuGroup({'children': leftMenuItems});
    var rightGroup = new MenuGroup({'align': 'right', 'children': rightMenuItems});
    var menuGroups = new MenuGroupCollection();
    menuGroups.add(leftGroup);
    menuGroups.add(rightGroup);

    return {
        pages:pages,
        menuGroups:menuGroups
    };
});