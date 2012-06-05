define([
    'views/modules/element/item',
    'text!templates/modules/element/text.html'
], function (ItemView, ViewTemplate) {
    var View = ItemView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text'
    });
    return View;
});
