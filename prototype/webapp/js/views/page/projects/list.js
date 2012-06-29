define([
    'vm',
    'views/page/base',
    'views/modules/project/listing',
    'views/modules/project/detail',
    'models/project',
    'models/projectprogress',
    'collections/project',
    'text!templates/page/projects/list.html',
    'text!templates/modules/loading.html'
], function (Vm, PageViewBase, ProjectListingView, ProjectInfoDetailView, ProjectModel, ProjectProgressModel, ProjectCollection, ProjectListPageTemplate, LoadingTemplate) {
    return PageViewBase.extend({
        projects:null,
        showDetailTab:null,
        initialize:function (options) {
            if (_.has(options, 'model')) {
                this.model = new ProjectModel({id:options.model.id});
                if (_.has(options.model, 'tab')) this.showDetailTab = options.model.tab;
            } else {
                this.model = null;
            }
            this.projects = new ProjectCollection();
            this.projects.bind('reset', this.renderProject, this)
        },
        render:function () {
            var el = $(this.el);
            el.html(ProjectListPageTemplate);
            el.find('div.view-projectlist').html(Vm.create(this, 'projectlisting', ProjectListingView, {model:this.projects}).el);
            if (!_.isNull(this.model)) {
                $(this.el).find('div.view-projectinfo').html(LoadingTemplate);
            }
            return this;
        },
        renderProject:function () {
            if (_.isNull(this.model)) {
                return;
            }
            $(this.el).find('div.view-projectinfo').html(Vm.create(this, 'project-info-details', ProjectInfoDetailView, {model:this.projects.get(this.model.id)}).el);
            if (!_.isNull(this.showDetailTab)) {
                $('a[data-target=#project-tab-' + this.showDetailTab + ']').tab('show');
            }
        }
    });
});
