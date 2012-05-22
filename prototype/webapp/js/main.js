require.config({
    urlArgs:"bust=" + (new Date()).getTime()
});

require([
    'views/app',
    'router'
], function (AppView, Router) {
    var appView = new AppView();
    appView.render();
    Router.initialize({appView:appView});
});