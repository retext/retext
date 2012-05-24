require.config({
    urlArgs:"bust=" + (new Date()).getTime()
});

require([
    'views/app',
    'router',
    'vm',
    'models/user'
], function (AppView, Router, Vm, User) {
    var user = new User();
    var appView = new AppView(user);
    appView.render();
    new Router(appView, Vm, user);
    Backbone.history.start();
});