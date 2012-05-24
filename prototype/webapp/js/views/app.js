define([
    'vm',
    'views/menu',
    'views/status',
    'views/account/register',
    'views/project/new'
], function (Vm, MenuView, StatusView, RegisterView, ProjectNewView) {
    var AppView = Backbone.View.extend({
        'el':$('#app'),
        'render': function() {
            Vm.MenuView = new MenuView({'model':Vm.menuGroups, 'el': $('#mainmenu')});
            Vm.MenuView.render();

            Vm.pages['status'] = new StatusView({'id':'status'});
            Vm.pages['status'].render();

            Vm.pages['register'] = new RegisterView({'id':'register'});
            Vm.pages['register'].render();

            Vm.pages['project-new'] = new ProjectNewView({'id':'project-new'});
            Vm.pages['project-new'].render();
        }
    });
    return AppView;
});