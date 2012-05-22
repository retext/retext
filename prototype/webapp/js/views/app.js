define([
    'vm',
    'views/menu',
    'views/status',
    'views/register'
], function (Vm, MenuView, StatusView, RegisterView) {
    var AppView = Backbone.View.extend({
        'el':$('#app'),
        'render': function() {
            Vm.MenuView = new MenuView({'model':Vm.menuItems});
            $('#mainmenu').append(Vm.MenuView.render().el);

            Vm.pages['status'] = new StatusView({'id':'status'});
            Vm.pages['status'].render();

            Vm.pages['register'] = new RegisterView({'id':'register'});
            Vm.pages['register'].render();
        }
    });
    return AppView;
});