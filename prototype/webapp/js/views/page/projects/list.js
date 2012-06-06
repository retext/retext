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
        initialize:function () {
            // IDs are passed as in the model param
            if (!_.isUndefined(this.model) && _.has(this.model, 'projectId')) {
                this.model = new ProjectModel({id:this.model.projectId});
            } else {
                this.model = new ProjectModel();
            }
        },
        render:function () {
            $(this.el).html(ProjectListPageTemplate);
            Vm.create(this, 'projectlisting', ProjectListingView, {el:$('#projectlist'), model:new ProjectCollection()});
            Vm.create(this, 'projectinfo', ProjectInfoView, {el:$('#projectinfo'), model:this.model});
            return this;
        },
        selectProject:function (ev) {
            ev.preventDefault();
            var a = $(ev.target);
            Events.trigger('navigate', a.attr('href'));
            this.model.id = a.data('projectid');
            this.model.fetch();
        }
    });
    return StatusView;
});
