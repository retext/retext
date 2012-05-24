define([
    'models/project',
    'remote'
], function (ProjectModel, Remote) {
    var ProjectNewView = Backbone.View.extend({
        'el':$('#project-new'),
        'initialize':function () {
            this.model = new ProjectModel();
        },
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            this.updateModelFromForm();
            var form = $($(this.el).find('form'));
            form.parent().prepend('<div class="well" id="register-progress"><p>Lege Projekt an…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
            this.model.save();
        },
        updateModelFromForm:function () {
            var form = $($(this.el).find('form'));
            var model = this.model;
            _.each(this.model.attributes, function (value, name) {
                model.set(name, form.find('input[name=' + name + ']').attr('value'));
            })
        }
    });
    return ProjectNewView;
})
;