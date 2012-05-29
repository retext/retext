define([
    'vm',
    'events',
    'views/page/base',
    'views/modules/project/info',
    'views/modules/project/listing',
    'collections/project',
    'models/project',
    'text!templates/page/projects/list.html'
], function (Vm, Events, PageViewBase, ProjectInfoView, ProjectListingView, ProjectCollection, ProjectModel, ProjectListPageTemplate) {
    var StatusView = PageViewBase.extend({
        events:{
            'click a.project':'selectProject'
        },
        initialize:function (options) {
            this.options = _.defaults(options, {projectId:null});
            this.selectedProject = new ProjectModel();
            if (this.options.projectId !== null) {
                this.selectedProject.id = this.options.projectId;
            }
        },
        render:function () {
            $(this.el).html(ProjectListPageTemplate);
            Vm.create(this, 'projectlisting', ProjectListingView, {el:$('#projectlist'), model:new ProjectCollection()});
            Vm.create(this, 'projectinfo', ProjectInfoView, {el:$('#projectinfo'), model:this.selectedProject});
            return this;
        },
        selectProject:function (ev) {
            ev.preventDefault();
            var a = $(ev.target);
            Events.trigger('navigate', a.attr('href'));
            this.selectedProject.id = a.data('projectid');
            this.selectedProject.fetch();
        }
    });
    return StatusView;
});