define([
    'views/modules/element/element',
    'models/text',
    'text!templates/modules/element/write/text.html'
], function (ElementView, TextModel, ViewTemplate) {
    var View = ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text gui-write-text',
        events:{
            'blur input':'textBlur'
        },
        textBlur:function (ev) {
            var inp = $(ev.target).closest('input');
            var newText = inp.attr('value');
            if (_.isEmpty(newText)) newText = null;
            var oldText = this.model.get('text');
            if (_.isEmpty(oldText)) oldText = null;
            if (_.isEqual(newText, oldText)) return;
            var el = $(this.el);
            var progress = el.find('div.gui-saving-progress');
            progress.css('display', 'block');
            var error = el.find('div.gui-saving-error');
            this.model.save({text:inp.attr('value')}, {
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
    return View;
});
