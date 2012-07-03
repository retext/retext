/**
 * Zeigt die Sprachen eines Projekts an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'vm',
    'models/projectlanguage',
    'collections/projectlanguage',
    'views/forms/language',
    'text!templates/modules/project/languages.html'
], function (Vm, LanguageModel, LanguagesCollection, LanguageFormView, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        languages:null,
        events:{
            'click button.btn-danger':'removeLanguage'
        },
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.languages = new LanguagesCollection();
            this.languages.url = this.model.getRelation('http://jsonld.retext.it/Language', true).get('href');
            this.languages.bind('reset', this.render, this);
            this.languages.bind('add', this.render, this);
            this.languages.bind('destroy', this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({model:this.model.toJSON(), languages:this.languages.toJSON()}));
            var newLanguage = new LanguageModel();
            newLanguage.url = this.model.getRelation('http://jsonld.retext.it/Language', true).get('href');
            var formView = Vm.create(this, 'language-create', LanguageFormView, {el:$(this.el).find('form'), model:newLanguage});
            formView.bind('saved', this.languageCreated, this);
            return this;
        },
        removeLanguage:function (ev) {
            var id = $(ev.target).closest('button.btn-danger').data('id');
            var theLanguage = this.languages.get(id);
            theLanguage.destroy();
        },
        languageCreated:function (model) {
            this.languages.add(model);
        },
        complete:function () {
            this.languages.fetch();
        }
    });
});
