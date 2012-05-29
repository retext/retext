define([
    "events",
    "vm"
], function (Events, Vm) {
    var AppRouter = Backbone.Router.extend({
        initialize:function (appView) {
            this.appView = appView;
            Events.on('navigate', this.navigate, this);
        },
        routes:{
            "":"home",
            "project/list/:id":"listProject",
            "*page":"showPage"
        },
        home:function () {
            this.showPage('login');
        },
        showPage:function (pageId, options) {
            var appView = this.appView;
            require(['views/page/' + pageId], function (PageView) {
                Vm.create(appView, pageId, PageView, options);
            });
        },
        listProject:function (projectId) {
            this.showPage('project/list', {projectId:projectId});
        }
    });
    return AppRouter;
});