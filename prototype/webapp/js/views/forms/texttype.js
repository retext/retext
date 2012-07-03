/**
 * Formular zum Bearbeiten eines Text-Typens
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/forms/item',
    'text!templates/forms/texttype.html'
], function (ItemForm, ViewTemplate) {
    return ItemForm.extend({
        template:_.template(ViewTemplate)
    });
});
