define([
    "events",
    "vm"
], function (Events, Vm) {
    var AppRouter = Backbone.Router.extend({
        routes:{
            "":"home",
            "logout":"logout",
            "*page":"showPage"
        },
        home:function () {
            this.showPage('login');
        },
        logout:function () {
            Events.trigger('userLogoff');
            window.location.href = '/app/';
        },
        showPage:function (pageId) {
            var appView = this.appView;
            require(['views/page/' + pageId], function (PageView) {
                Vm.create(appView, pageId, PageView, {});
            });
        },
        initialize:function (appView) {
            this.appView = appView;
        }
    });
    return AppRouter;
});