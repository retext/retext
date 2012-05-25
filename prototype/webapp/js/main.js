require.config({
    urlArgs:"bust=" + (new Date()).getTime(),
    paths:{
        text:'../assets/require',
        templates:'../templates'
    }
});

require([
    'views/app',
    'router'
], function (AppView, Router) {
    var appView = new AppView();
    appView.render();
    new Router(appView);
    Backbone.history.start();
});