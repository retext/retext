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
            this.model.set({
                email:email,
                password:pass
            }, {
                error:function (model, validate_result) {
                    viewmodel.set({error:true, validate_error:true, validate:validate_result, email:email, password:pass});
                }
            });
            if (this.model.isValid()) {
                viewmodel.set({error:false, loading:true, email:email, password:pass});
                this.model.save({},
                    {
                        error:function (model, response) {
                            console.log(model);
                            console.log(response);
                            console.log(response.responseText);
                            var status = JSON.parse(response.responseText);
                            if (_.has(status, 'code') && status.code == Remote.errorCode.loginFailed) {
                                viewmodel.set({error:true, errormessage:status.message, loading:false});
                            } else {
                                viewmodel.set({error:true, unexpected_error:true, loading:false});
                            }
                            Events.trigger('userLogoff');
                        },
                        success:function () {
                            model.set('authenticated', true);
                            viewmodel.set({authenticated:true, loading:false});
                            Events.trigger('userLogon');
                        }
                    }
                );
            }

        },
        render:function () {
            $(this.el).html(this.template({model:this.viewmodel.toJSON()}));
            return this;
        }
    });
});