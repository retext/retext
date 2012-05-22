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
            'submit form':'submitForm',
            'keyup input':'changeForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            if (this.model.isValid()) {
                var form = $($(this.el).find('form'));
                $.ajax({
                    'url':Remote.apiUrlBase + 'user',
                    'dataType':'json',
                    'data':this.model.toJSON(),
                    'type':'PUT'
                });
                form.parent().prepend('<div class="alert alert-success">Toll! Danke für deine Registrierung. Bitte überprüfe dein Postfach um die Registrierung abzuschließen.</div>');
                form.hide();
            }
        },
        changeForm:function (ev) {
            this.model.set('email', this.inp.attr('value'));
            if (this.model.isValid()) {
                this.button.removeAttr('disabled');
            } else {
                this.button.attr('disabled', 'disabled');
            }
        }
    });
    return RegisterView;
});