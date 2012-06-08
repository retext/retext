define([
    'views/modules/element/element',
    'text!templates/modules/element/structure/container.html'
], function (ElementView, ViewTemplate) {
    var View = ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-container'
    });
    return View;
});
