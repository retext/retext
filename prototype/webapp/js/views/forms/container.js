/**
 * Formular zum Bearbeiten eines Containers
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/forms/item',
    'text!templates/forms/container.html'
], function (ItemForm, ViewTemplate) {
    return ItemForm.extend({
        template:_.template(ViewTemplate)
    });
});