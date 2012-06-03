define([
    'events',
    'vm',
    'views/menu',
    'models/auth'
], function (Events, Vm, MenuView, Auth) {
    var AppView = Backbone.View.extend({
        el:$('#app'),
        initialize:function () {
            Events.on('userLogon', this.userLogon, this);
            Events.on('userLogoff', this.userLogoff, this);
            Events.on('all', this.logEvents, this);
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
        },
        // Check if already authorized
        complete:function () {
            var authorized = new Auth();
            authorized.fetch(
                {
                    success:function (model, response) {
                        if (model.get('authorized')) {
                            Events.trigger('userLogon');
                        }
                    }
                });
        },
        // Log all events
        logEvents:function (eventName) {
            if (!_.isUndefined(console) && _.isFunction(console.log)) {
                console.log('Event: ' + eventName);
            }
        }
    });
    return AppView;
});