define([
    'models/project',
    'remote'
], function (ProjectModel, Remote) {
    var ProjectNewView = Backbone.View.extend({
        'el':$('#project-new'),
        'initialize': function()
        {
            this.model = new ProjectModel();
        },
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            this.model.set('name', this.$($(this.el).find('input')).attr('value'));
            if (this.model.isValid()) {
                var form = $($(this.el).find('form'));
                form.parent().prepend('<div class="well" id="register-progress"><p>Lege Projekt an…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
                $(".alert").alert('close');
                $.ajax({
                    'url':Remote.apiUrlBase + 'project',
                    'dataType':'json',
                    'data':this.model.toJSON(),
                    'type':'PUT',
                    'error':function () {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#register-progress').remove();
                    },
                    'success':function () {
                        form.parent().prepend('<div class="alert alert-success">Toll! Danke für deine Registrierung. Bitte überprüfe dein Postfach um die Registrierung abzuschließen.</div>');
                        form.hide();
                    }
                });
            }
        }
    });
    return ProjectNewView;
});