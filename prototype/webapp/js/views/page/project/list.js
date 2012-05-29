define([
    'vm',
    'events',
    'views/page/base',
    'views/modules/project/info',
    'collections/project',
    'models/project',
    'text!templates/page/project/list.html'
], function (Vm, Events, PageViewBase, ProjectInfoView, ProjectCollection, ProjectModel, ProjectListPageTemplate) {
    var StatusView = PageViewBase.extend({
        template:_.template(ProjectListPageTemplate),
        events:{
            'click a.project':'selectProject'
        },
        initialize:function (options) {
            this.projectList = new ProjectCollection();
            this.projectList.bind("change", this.render, this);
            this.projectList.bind("reset", this.render, this);
            this.options = _.defaults(options, {projectId:null});
        },
        render:function () {
            $(this.el).html(this.template({projects:this.projectList.toJSON()}));
            if (this.options.projectId !== null) {
                Vm.create(this, 'projectinfo', ProjectInfoView, {el:$('#projectinfo'), model:new ProjectModel({id:this.options.projectId})});
            }
            return this;
        },
        complete:function () {
            this.projectList.fetch();
        },
        selectProject:function (ev) {
            ev.preventDefault();
            var a = $(ev.target);
            Events.trigger('navigate', a.attr('href'));
            Vm.create(this, 'projectinfo', ProjectInfoView, {el:$('#projectinfo'), model:new ProjectModel({id:a.data('projectid')})});
        }
    });
    return StatusView;
});