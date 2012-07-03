/**
 * Anzeige der Texte in der Definieren-Ansicht
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/modules/element/element',
    'text!templates/modules/element/structure/text.html'
], function (ElementView, ViewTemplate) {
    return ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text'
    });
});
