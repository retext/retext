/**
 * Anzeige der Elemente in der Übersetzen-Ansicht
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'vm',
    'collections/projectlanguage',
    'views/modules/element/write/list',
    'views/modules/project/language-switcher'
], function (Vm, LanguagesCollection, WriteListView, LanguageSwitchView) {
    return WriteListView.extend({
        preferredContext:'info',
        mode:'translate',
        languages:null,
        sourceLanguage:null,
        sourceLanguageSwitch:null,
        targetLanguage:null,
        targetLanguageSwitch:null,
        postInitialize:function () {
            this.languages = new LanguagesCollection();
            this.languages.url = this.model.get('project').getRelation('http://jsonld.retext.it/Language', true).get('href');
            this.languages.bind('reset', this.renderLanguageSwitches, this);
            this.languages.bind('add', this.renderLanguageSwitches, this);
            this.languages.bind('destroy', this.renderLanguageSwitches, this);
        },
        renderLanguageSwitches:function () {
            var project = this.model.get('project');
            this.sourceLanguageSwitch = Vm.create(this, 'language-switch-source', LanguageSwitchView, {model:this.languages, selectedLanguage:_.filter(this.languages.models, function (lang) {
                return lang.get('name') == project.get('defaultLanguage');
            })[0]});
            this.sourceLanguageSwitch.bind('language:changed', this.changeSourceLanguage, this);
            this.targetLanguageSwitch = Vm.create(this, 'language-switch-target', LanguageSwitchView, {model:this.languages, selectedLanguage:_.filter(this.languages.models, function (lang) {
                return lang.get('name') != project.get('defaultLanguage');
            })[0]});
            this.targetLanguageSwitch.bind('language:changed', this.changeTargetLanguage, this);
            this.sourceLanguage = this.sourceLanguageSwitch.selectedLanguage;
            this.targetLanguage = this.targetLanguageSwitch.selectedLanguage;
            $(this.el).find('.view-language-switch-source').html(this.sourceLanguageSwitch.el);
            $(this.el).find('.view-language-switch-target').html(this.targetLanguageSwitch.el);

            this.renderListAfterLanguages();
        },
        preRenderText:function (element) {
            element.set('sourceText', element.get('text')[this.sourceLanguage.get('name')]);
            element.set('sourceLanguage', this.sourceLanguage.toJSON());
            element.set('targetText', element.get('text')[this.targetLanguage.get('name')]);
            element.set('targetLanguage', this.targetLanguage.toJSON());
        },
        renderList:function () {
            this.languages.fetch();
        },
        renderListAfterLanguages:function () {
            // Liste erst rendern, wenn Sprachen geladen sind
            this.list.empty();
            _.each(this.elements.models, function (model) {
                this.renderElement(model);
            }, this);
        },
        changeSourceLanguage:function (language) {
            this.sourceLanguage = language;
            this.renderListAfterLanguages();
        },
        changeTargetLanguage:function (language) {
            this.targetLanguage = language;
            this.renderListAfterLanguages();
        }
    });
});
