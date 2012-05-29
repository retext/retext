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
    new Router(appView);
    appView.render();
    Backbone.history.start();
    appView.complete();
});