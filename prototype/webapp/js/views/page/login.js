define([
    'views/page/base',
    'events',
    'models/user',
    'text!templates/page/login.html'
], function (PageViewBase, Events, UserModel, LoginPageTemplate) {
    var LoginView = PageViewBase.extend({
        events:{
            'submit form':'submitForm'
        },
        initialize:function () {
            this.model = new UserModel();
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
                        Events.trigger('userLogon');
                    }
                }
            );
            $(".alert").alert('close');
        },
        render:function () {
            $(this.el).html(LoginPageTemplate);
            return this;
        }
    });
    return LoginView;
});