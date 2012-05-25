define([
    'events',
    'vm',
    'views/menu'
], function (Events, Vm, MenuView) {
    var AppView = Backbone.View.extend({
        el:$('#app'),
        initialize:function () {
            Events.on('userLogon', this.userLogon, this);
            Events.on('userLogoff', this.userLogoff, this);
        },
        render:function () {
            Vm.create(this, 'mainmenu', MenuView);
        },
        userLogon:function () {
            $(document.body).data('authenticated', true);
            Events.trigger('userAuthChange');
        },
        userLogoff:function () {
            $(document.body).data('authenticated', false);
            Events.trigger('userAuthChange');
        }
    });
    return AppView;
});