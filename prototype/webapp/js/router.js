define([
    "events",
    "vm"
], function (Events, Vm) {
    var AppRouter = Backbone.Router.extend({
        initialize:function (appView) {
            this.appView = appView;
        },
        routes:{
            "":"home",
            "*page":"showPage"
        },
        home:function () {
            this.showPage('login');
        },
        showPage:function (pageId) {
            var appView = this.appView;
            require(['views/page/' + pageId], function (PageView) {
                Vm.create(appView, pageId, PageView, {});
            });
        }
    });
    return AppRouter;
});