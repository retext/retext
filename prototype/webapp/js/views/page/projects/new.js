/**
 * Erstellt ein neues Projekt
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'events',
    'views/page/base',
    'models/project',
    'text!templates/page/projects/new.html'
], function (Events, PageViewBase, ProjectModel, PageProjectNewTemplate) {
    return PageViewBase.extend({
        'initialize':function () {
            this.model = new ProjectModel();
        },
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            form.parent().prepend('<div class="well" id="project-new-progress"><p>Lege Projekt an…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
            this.model.save({name:form.find('input[name=name]').attr('value')}, {
                    error:function () {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#project-new-progress').remove();
                    },
                    success:function (model, request) {
                        $('#project-new-progress').remove();
                        Events.trigger('navigate', 'project/' + model.id + '/structure/' + model.get('rootContainer'), {trigger:true});
                    }
                }
            );
        },
        render:function () {
            $(this.el).html(PageProjectNewTemplate);
            return this;
        }
    });
});
