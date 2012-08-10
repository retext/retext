/**
 * Der Router zeigt je nach URL-Fragment die jeweils dafür registrierte View an.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    "events",
    "vm"
], function (Events, Vm) {
    return Backbone.Router.extend({
        initialize:function (appView) {
            this.appView = appView;
            Events.on('navigate', this.navigate, this);
        },
        /* TODO: Konfiguration der Routen in Views auslagern */
        routes:{
            "":"home",
            "project/:projectId/:mode/:parentContainerId":"project",
            "projects/list/:projectId/:tab":"listProjectTab",
            "projects/list/:projectId":"listProject",
            "*page":"showPage"
        },
        home:function () {
            this.showPage('login');
        },
        showPage:function (pageId, options) {
            var appView = this.appView;
            require(['views/page/' + pageId], function (PageView) {
                $('#page').html(Vm.create(appView, 'page', PageView, options).el);
            });
        },
        project:function (projectId, mode, parentContainerId) {
            var opts = {model:{id:projectId, parentContainerId:parentContainerId, mode:mode}};
            this.showPage('project', opts);
        },
        listProject:function (projectId) {
            this.showPage('projects/list', {model:{id:projectId}});
        },
        listProjectTab:function (projectId, tab) {
            this.showPage('projects/list', {model:{id:projectId, tab:tab}});
        }
    });
});
