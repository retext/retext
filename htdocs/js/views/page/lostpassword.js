/**
 * Kümmert sich um die Anzeige des Passwort-Vergessen-Formulars
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'views/page/base',
    'models/lostpassword',
    'viewmodels/lostpassword',
    'text!templates/page/lostpassword.html'
], function (Remote, PageViewBase, Model, ViewModel, ViewTemplate) {
    return PageViewBase.extend({
        className:'container compact',
        template:_.template(ViewTemplate),
        events:{
            'submit form':'submitForm'
        },
        initialize:function () {
            this.model = new Model();
            this.viewmodel = new ViewModel();
            this.viewmodel.bind('change', this.render, this);
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            var viewmodel = this.viewmodel;
            var model = this.model;
            var email = form.find('input[name=email]').attr('value');
            viewmodel.set({error:false, loading:true, email:email});
            this.model.save({
                    email:email
                },
                {
                    error:function (model, response) {
                        viewmodel.set({error:true, error_message:Remote.getErrorMessage(response), loading:false});
                    },
                    success:function () {
                        viewmodel.set({success:true, loading:false});
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
