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
            "projects/list/:id":"listProject",
            "project/:projectId/:parentContainerId":"project",
            "project/:projectId":"project",
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
            this.showPage('projects/list', {projectId:projectId});
        },
        project:function (projectId, parentContainerId) {
            var opts = {id:projectId};
            if (!_.isUndefined(parentContainerId)) opts.parentContainerId = parentContainerId;
            this.showPage('project', opts);
        }
    });
    return AppRouter;
});