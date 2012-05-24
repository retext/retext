define([
    'vm',
    'views/menu',
    'views/status',
    'views/account/register',
    'views/account/login',
    'views/project/new',
    'models/menu/group',
    'collections/menu/group',
    'models/menu/item',
    'collections/menu/item'
], function (Vm, MenuView, StatusView, RegisterView, LoginView, ProjectNewView, MenuGroup, MenuGroupCollection, MenuItem, MenuItemCollection) {
    var AppView = Backbone.View.extend({
        el:$('#app'),
        initialize:function (user) {
            this.user = user;

            var leftMenuItems = new MenuItemCollection();
            var projectMenuItem = new MenuItem({id:'projects', label:'Projekte', 'children':[new MenuItem({id:'project-new', label:'Neues Projekt…'})]});
            leftMenuItems.add(projectMenuItem);
            leftMenuItems.add(new MenuItem({id:'register', label:'Registrieren'}));
            leftMenuItems.add(new MenuItem({id:'status', label:'Status'}));

            var rightMenuItems = new MenuItemCollection();
            rightMenuItems.add(new MenuItem({id:'login', label:'Anmelden', 'icon':'icon-user icon-white'}));
            rightMenuItems.add(new MenuItem({id:'logout', label:'Abmelden', 'icon':'icon-eject icon-white'}));

            var leftGroup = new MenuGroup({'children':leftMenuItems});
            var rightGroup = new MenuGroup({'align':'right', 'children':rightMenuItems});
            Vm.menuGroups.add(leftGroup);
            Vm.menuGroups.add(rightGroup);

            this.user.bind("change", this.userChange, this);
        },
        render:function () {
            Vm.MenuView = new MenuView({model:Vm.menuGroups, el:$('#mainmenu')});
            Vm.MenuView.render();
            this.userChange();

            Vm.pages['status'] = new StatusView({id:'status'});
            Vm.pages['status'].render();

            Vm.pages['register'] = new RegisterView({id:'register'});
            Vm.pages['register'].render();

            Vm.pages['login'] = new LoginView({id:'login', model: this.user});
            Vm.pages['login'].render();

            Vm.pages['project-new'] = new ProjectNewView({id:'project-new'});
            Vm.pages['project-new'].render();
        },
        userChange: function()
        {
            if (this.user.get('authenticated')) {
                $(this.el).find('a[href="#login"]').parent().hide();
                $(this.el).find('a[href="#logout"]').parent().show();
                $(this.el).find('a[href="#projects"]').parent().show();
            } else {
                $(this.el).find('a[href="#login"]').parent().show();
                $(this.el).find('a[href="#logout"]').parent().hide();
                $(this.el).find('a[href="#projects"]').parent().hide();
            }
        }
    });
    return AppView;
});