define([
    'vm',
    'views/menu/group',
    'models/menu/group',
    'collections/menu/group',
    'models/menu/item',
    'collections/menu/item'
], function (Vm, MenuGroupView, MenuGroup, MenuGroupCollection, MenuItem, MenuItemCollection) {
    var MenuView = Backbone.View.extend({
        el:'#mainmenu',
        initialize:function () {
            this.model = new MenuGroupCollection();
            this.model.bind("change", this.render, this);

            var leftMenuItems = new MenuItemCollection();
            var projectMenuItem = new MenuItem({id:'projects', label:'Projekte', children:[new MenuItem({id:'project/new', label:'Neu…'}), new MenuItem({id:'project/list', label:'Meine Projekte'})], authOnly:true});
            leftMenuItems.add(new MenuItem({id:'dashboard', label:'Dashboard', authOnly: true}));
            leftMenuItems.add(projectMenuItem);
            leftMenuItems.add(new MenuItem({id:'status', label:'Status'}));

            var rightMenuItems = new MenuItemCollection();
            rightMenuItems.add(new MenuItem({id:'register', label:'Registrieren', anonOnly:true}));
            rightMenuItems.add(new MenuItem({id:'login', label:'Anmelden', 'icon':'icon-user icon-white', anonOnly:true}));
            rightMenuItems.add(new MenuItem({id:'logout', label:'Abmelden', 'icon':'icon-eject icon-white', authOnly:true}));

            var leftGroup = new MenuGroup({children:leftMenuItems});
            var rightGroup = new MenuGroup({'align':'right', children:rightMenuItems});
            this.model.add(leftGroup);
            this.model.add(rightGroup);
        },
        render:function () {
            $(this.el).empty();
            _.each(this.model.models, function (menuGroup) {
                $(this.el).append(new MenuGroupView({model:menuGroup}).render().el)
            }, this);
            return this;
        }
    });
    return MenuView;
});