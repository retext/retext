/**
 * Sprach-Umschalter. Wird z.B. bei der Übersetzung verwendet.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'text!templates/modules/project/language-switcher.html'
], function (Template) {
    return Backbone.View.extend({
        template:_.template(Template),
        className:'btn-group',
        selectedLanguage:null,
        events:{
            'click a':'entrySelected'
        },
        initialize:function (options) {
            this.model = options.model;
            this.selectedLanguage = options.selectedLanguage;
        },
        render:function () {
            $(this.el).html(this.template({languages:this.model.toJSON(), selectedLanguage:this.selectedLanguage.toJSON()}));
            return this;
        },
        entrySelected:function (ev) {
            var newLang = this.model.get($(ev.target).closest('a').data('id'));
            if (!_.isEqual(newLang.id, this.selectedLanguage.id)) {
                this.selectedLanguage = newLang;
                this.render();
                this.trigger('language:changed', newLang);
            }
        }
    });
})
;
