define([
    'remote'
], function (Remote) {
    var LoginView = Backbone.View.extend({
        'el':$('#login'),
        'events':{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            var model = this.model;
            form.parent().prepend('<div class="well" id="login-progress"><p>Melde an…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
            this.model.save(
                {
                    email:form.find('input[name=email]').attr('value'),
                    password:form.find('input[name=password]').attr('value')
                },
                {
                    error:function () {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#login-progress').remove();
                        model.set('authenticated', false);
                    },
                    success:function () {
                        form.parent().prepend('<div class="alert alert-success">Hallo!</div>');
                        form.hide();
                        $('#login-progress').remove();
                        model.set('authenticated', true);
                    }
                }
            );
            $(".alert").alert('close');
        }
    });
    return LoginView;
});