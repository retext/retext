define([
    'views/forms/item',
    'text!templates/forms/container.html'
], function (ItemForm, ViewTemplate) {
    var FormView = ItemForm.extend({
        template:_.template(ViewTemplate)
    });
    return FormView;
});