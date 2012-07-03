/**
 * Formular zum Bearbeiten einer Sprache
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/forms/item',
    'text!templates/forms/language.html'
], function (ItemForm, ViewTemplate) {
    return ItemForm.extend({
        template:_.template(ViewTemplate)
    });
});