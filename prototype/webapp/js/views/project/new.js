define([
    'models/project',
    'collections/project',
    'views/project/list'
], function (ProjectModel, ProjectCollection, ProjectListView) {
    var ProjectNewView = Backbone.View.extend({
        'el':$('#project-new'),
        'initialize':function () {
            this.model = new ProjectModel();
            this.projectList = new ProjectCollection();
            $($(this.el).find('form')).after(new ProjectListView({model: this.projectList}).render().el);
        },
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            form.parent().prepend('<div class="well" id="project-new-progress"><p>Lege Projekt an…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
            var projectList = this.projectList;
            this.model.save({name: form.find('input[name=name]').attr('value')}, {
                    error:function () {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#project-new-progress').remove();
                    },
                    success:function (data) {
                        $('#project-new-progress').remove();
                        projectList.fetch();
                    }
                }
            );
        }
    });
    return ProjectNewView;
})
;