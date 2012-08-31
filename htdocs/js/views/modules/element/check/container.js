/**
 * Anzeige des Containers in der Prüfen-Ansicht
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/modules/element/element',
    'text!templates/modules/element/check/container.html'
], function (ElementView, ViewTemplate) {
    return ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-container'
    });
});
