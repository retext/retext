define([
    'vm',
    'views/menu',
    'views/status'
], function (Vm, MenuView, StatusView) {
    var AppView = Backbone.View.extend({
        'el':$('#app'),
        'render': function() {
            Vm.MenuView = new MenuView({'model':Vm.menuItems});
            $('#mainmenu').append(Vm.MenuView.render().el);
            Vm.pages['status'] = new StatusView({'id':'status'});
            Vm.pages['status'].render();
        }
    });
    return AppView;
});