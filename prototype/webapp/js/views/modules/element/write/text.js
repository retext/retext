define([
    'views/modules/element/element',
    'models/text',
    'text!templates/modules/element/write/text.html'
], function (ElementView, TextModel, ViewTemplate) {
    return ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text gui-write-text',
        events:{
            'blur .text':'textBlur'
        },
        textBlur:function (ev) {
            var inp = $(ev.target).closest('.text');
            var newText = inp.attr('value');
            if (_.isEmpty(newText)) newText = null;
            var oldText = this.model.get('showText');
            if (_.isEmpty(oldText)) oldText = null;
            if (_.isEqual(newText, oldText)) return;
            this.model.set('showText', newText);
            var el = $(this.el);
            var progress = el.find('div.gui-saving-progress');
            progress.css('display', 'block');
            var error = el.find('div.gui-saving-error');

            var updatedText = this.model.get('text');
            updatedText[this.model.get('showLanguage')] = newText;
            this.model.save({text:updatedText}, {
                success:function () {
                    progress.css('display', 'none');
                },
                error:function () {
                    progress.css('display', 'none');
                    error.css('display', 'block');
                }
            });
        }
    });
});
