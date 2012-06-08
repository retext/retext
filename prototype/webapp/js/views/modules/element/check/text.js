define([
    'views/modules/element/element',
    'text!templates/modules/element/check/text.html'
], function (ElementView, ViewTemplate) {
    var View = ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text'
    });
    return View;
});
