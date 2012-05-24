define([
    'models/register',
    'remote'
], function (RegisterModel, Remote) {
    var RegisterView = Backbone.View.extend({
        'el':$('#register'),
        'initialize':function () {
            this.model = new RegisterModel();
            this.inp = $($(this.el).find('input'));
            this.button = $($(this.el).find('button'));
        },
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            if (this.model.isValid()) {
                var form = $($(this.el).find('form'));
                form.parent().prepend('<div class="well" id="register-progress"><p>Verarbeite Registrierung…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
                $(".alert").alert('close');
                $.ajax({
                    'url':Remote.apiUrlBase + 'user',
                    'dataType':'json',
                    'data':this.model.toJSON(),
                    'type':'PUT',
                    'error': function() {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#register-progress').remove();
                    },
                    'success': function()
                    {
                        form.parent().prepend('<div class="alert alert-success">Toll! Danke für deine Registrierung. Bitte überprüfe dein Postfach um die Registrierung abzuschließen.</div>');
                        form.hide();
                    }
                });
            }
        }
    });
    return RegisterView;
});