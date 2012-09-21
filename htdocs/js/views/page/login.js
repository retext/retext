/**
 * Kümmert sich um die Anzeige des Logins
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/page/base',
    'events',
    'models/user',
    'viewmodels/login',
    'text!templates/page/login.html',
    'remote'
], function (PageViewBase, Events, UserModel, ViewModel, ViewTemplate, Remote) {
    return PageViewBase.extend({
        className:'container compact',
        template:_.template(ViewTemplate),
        events:{
            'submit form':'submitForm'
        },
        initialize:function () {
            this.model = new UserModel();
            this.viewmodel = new ViewModel();
            this.viewmodel.bind('change', this.render, this);
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            var model = this.model;
            var viewmodel = this.viewmodel;
            var email = form.find('input[name=email]').attr('value');
            var pass = form.find('input[name=password]').attr('value');
            viewmodel.set({error:false, loading:true, email:email, password:pass});
            this.model.save({
                    email:email,
                    password:pass
                },
                {
                    error:function (model, response) {
                        viewmodel.set({error:true, error_message:Remote.getErrorMessage(response), loading:false});
                        Events.trigger('userLogoff');
                    },
                    success:function () {
                        model.set('authenticated', true);
                        viewmodel.set({authenticated:true, loading:false});
                        Events.trigger('userLogon');
                    }
                }
            );
        },
        render:function () {
            $(this.el).html(this.template({model:this.viewmodel.toJSON()}));
            return this;
        }
    });
});