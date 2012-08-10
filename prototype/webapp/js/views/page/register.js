/**
 * Kümmert sich um die Anzeige der Registrierung
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/page/base',
    'models/register',
    'text!templates/page/register.html'
], function (PageViewBase, RegisterModel, RegisterPageTemplate) {
    return PageViewBase.extend({
        initialize:function () {
            this.model = new RegisterModel();
        },
        events:{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            form.parent().prepend('<div class="well" id="register-progress"><p>Verarbeite Registrierung…</p><div class="progress progress-striped active"><div class="bar" style="width: 50%;"></div></div></div>');
            $(".alert").alert('close');
            this.model.save(
                {
                    email:form.find('input[type=email]').attr('value')
                },
                {
                    error:function () {
                        form.parent().prepend('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a><strong>Oops.</strong> Irgendwas ist schief gelaufen.</div>');
                        $('#register-progress').remove();
                    },
                    success:function () {
                        form.parent().prepend('<div class="alert alert-success">Toll! Danke für deine Registrierung. Bitte überprüfe dein Postfach um die Registrierung abzuschließen.</div>');
                        form.hide();
                        $('#register-progress').remove();
                    }
                }
            );
        },
        render:function () {
            $(this.el).html(RegisterPageTemplate);
            return this;
        }
    });
});
