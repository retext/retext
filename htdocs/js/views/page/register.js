/**
 * Kümmert sich um die Anzeige der Registrierung
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/page/base',
    'models/register',
    'viewmodels/register',
    'text!templates/page/register.html'
], function (PageViewBase, Model, ViewModel, ViewTemplate) {
    return PageViewBase.extend({
        className:'container compact',
        template:_.template(ViewTemplate),
        initialize:function () {
            this.model = new Model();
            this.viewmodel = new ViewModel();
            this.viewmodel.bind('change', this.render, this);
        },
        events:{
            'submit form':'submitForm'
        },
        submitForm:function (ev) {
            ev.preventDefault();
            var form = $($(this.el).find('form'));
            var model = this.model;
            var viewmodel = this.viewmodel;
            var email = form.find('input[name=email]').attr('value');
            var code = form.find('input[name=code]').attr('value');
            viewmodel.set({error:false, loading:true, email:email, code:code});
            this.model.save(
                {
                    email:email, code:code
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
